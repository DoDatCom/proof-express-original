<?php

//	#############################################################
//	# Name:		update.php										#
//	# Purpose:	AJAX script to handle uploading new versions of	#
//	#			pages to ads that are already in the proofing	#
//	#			system.											#
//	#############################################################

	include('../includes/inc.php');
	
//	Set filename.
	if(isset($_FILES["myfile"])) {
		$ret = array();
		$error =$_FILES["myfile"]["error"];
		if(!is_array($_FILES["myfile"]['name'])) {
			$fileName = $_FILES["myfile"]["name"];
			if(stripos($fileName,".pdf")!==false){
				$in = array('.pdf','.PDF',' ','(',')');
			} else {
				$in = array('.jpg','.JPG',' ','(',')');
			}
			$out = array('','','_','','');
			$imgName = str_replace($in,$out,$fileName);
		}
	}
	
	$pgName=$_GET["n"];
	if($pgName!==$imgName){
		echo 'ERROR:'.$imgName;
		return;
	}
	
//	Process GET data.
	$pid=$_GET["p"];
	$pname=projectName($pid);
	if(isset($_GET["f"])){
		$fid=$_GET["f"];
		$fname=folderName($fid);
		$folder=true;
	} else {
		$folder=false;
	}
	$aid=$_GET["a"];
	$aname=adName($aid);
	$user=$_GET["u"];
	require_once('../class.phpmailer.php');
	$url=WEB.'/';
	$adurl=WEB.'/ad.php?p='.$pid;
	if(isset($fid)){
		$adurl.='$f='.$fid;
	}
	$adurl.='&a='.$aid;
	
	$now=date("U",strtotime("now"));
	$strIN=array(' ','#');
	$strOUT=array('_','');
	
//	Retrieve next revision in sequence
	$rQuery=mysqli_query($con,"SELECT * FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND name = '".$imgName."' ORDER BY rev DESC");
	while($rQ=mysqli_fetch_assoc($rQuery)){
		$rev=$rQ["rev"] + 1;
		break;
	}
		
//	Initialize directory variables.
	$dir = ABSDIR.'proofs/';
	
	if(preg_match("/pdf/i",$fileName)){
		$result=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$imgName."',".$pid.",".$aid.",".$rev.",'Active',".$now.",".$now.",".$now.",'','pdf')");
	} else {
		$result=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$imgName."',".$pid.",".$aid.",".$rev.",'Active',".$now.",".$now.",".$now.",'','jpg')");
	}
	$newPage=mysqli_query($con,"SELECT * FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND name = '".$imgName."' AND rev = ".$rev);
	$result=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$user."','Add Page','".$pname."','".$aname."','".$imgName."',".$rev.",'-',".$now.")");
	while($pg=mysqli_fetch_assoc($newPage)){
		if($pg["hrtype"]=='pdf'){
			move_uploaded_file($_FILES["myfile"]["tmp_name"],$dir.'PDF/'.$pg["id"].'.pdf');
			exec('chmod -R 0775 "'.$dir.'PDF/'.$pg["id"].'.pdf"');
			$dimString = shell_exec(PDFINFO_PATH.'pdfinfo '.$dir.'PDF/'.$pg["id"].'.pdf');
			$dimPre = str_replace('Page size:','',strstr($dimString,'Page size:'));
			$dim = str_replace(' ','',strstr($dimPre,'pts',true));
			$dimSplit = explode('x',$dim);
			if($dimSplit[0] > $dimSplit[1]) {	// landscape
				$dimY = round(($dimSplit[1]/$dimSplit[0]) * 4096);
				$dimScale = '4096x'.$dimY;
			} else {
				$dimX = round(($dimSplit[0]/$dimSplit[1]) * 4096);
				$dimScale = $dimX.'x4096';
			}
			$gs=exec_timeout(GS_PATH.'gs -dBATCH -dNOPAUSE -r72 -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=80 -g'.$dimScale.' -dPDFFitPage -sOutputFile="'.$dir.'IMG/'.$pg["id"].'.jpg" "'.$dir.'PDF/'.$pg["id"].'.pdf" 2>&1',60);
			if($gs!=='GS_OK'){
				exec('cp '.ABSDIR.'/images/logos/pdf_logo.jpg '.$dir.'IMG/'.$pg["id"].'.jpg');
			}
			$hrType="pdf";
		} else {
			move_uploaded_file($_FILES["myfile"]["tmp_name"],$dir.'JPG/'.$pg["id"].'.jpg');
			$uploadedfile = $dir.'JPG/'.$pg["id"].'.jpg';
			$src = imagecreatefromjpeg($uploadedfile);        
			list($width, $height) = getimagesize($uploadedfile);
			if($width>$height){
				$dimY = round(($height/$width) * 4096);
				$dimX = 4096;
			} else {
				$dimX = round(($width/$height) * 4096);
				$dimY = 4096;
			}
			$tmp = imagecreatetruecolor($dimX, $dimY); 
			imagecopyresampled($tmp, $src, 0, 0, 0, 0, $dimX, $dimY, $width, $height); 
			imagejpeg($tmp, $dir.'IMG/'.$pg["id"].'.jpg', 80);
       	 		
			exec('chmod -R 0775 "'.$dir.'IMG/'.$pg["id"].'.jpg"');
			$hrType="jpg";
		}
	}
	
//	Retrieve list of users to be notified.
	$uQuery=mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$pid.":%' AND notify = 1");
	while($m=mysqli_fetch_assoc($uQuery)){
		if($m["email"]!==null && $m["email"]!==''){
			$link=$adurl.'&qp='.$imgName.'&r='.$rev.'&t='.$m["sha256"].':'.$m["id"];
			$optOut=$url.'unsubscribe.php?&t='.$m["sha256"].':'.$m["id"];
			// Multiple recipients
			$to = $m["email"]; // note the comma

			// Subject
			$subject = 'Page Update';

			// Message
			$message = '
<html>
	<head>
		<meta charset="utf-8">
		<link href="'.$url.'css/bootstrap.css" rel="stylesheet">
		<link href="'.$url.'css/dodat.css" rel="stylesheet">
		<style type="text/css" media="screen">
			body { margin:1em; text-align:center; }
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<p align="center"><img src="'.$url.'images/SN_logo_400.png" class="img-responsive"/></p>
				<div class="well">
					<div class="row">
						<div class="col-sm-12">
							<p><b>A proof page has been updated on Proof Express.</b></p>
							<br/>
							<h3>'.$aname.' / '.$imgName.' (Revision '.$rev.')</h3>
							<br/>
							<p align="center">To view this proof, <a href="'.$link.'">click here.</a></p>
							<br/>
							<p><i>Thank you!</i></p>
						</div>
					</div>
				</div>
				<p align="center"><small>To opt out of future Proof Express notifications, <a href="'.$optOut.'">click here.</a></p>
			</div>
			<div class="col-sm-2"></div>
		</div>
	</body>
</html>
			';
			// To send HTML mail, the Content-type header must be set
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';

			// Additional headers
			$headers[] = 'To: '.$m["fullname"].' <'.$m["email"].'>';
			$headers[] = 'From: Proof Express <proofexpress@spartannash.com>';

			// Mail it
			mail($to, $subject, $message, implode("\r\n", $headers));
		}
	}
	$ret=$adurl.'&qp='.$pg.'&r='.$rev;
		
    echo json_encode($ret);
?>