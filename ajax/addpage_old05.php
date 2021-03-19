<?php

//	#############################################################
//	# Name:		addpage.php										#
//	# Purpose:	AJAX script to handle uploading pages to ads	#
//	#			that are already in the proofing system.		#
//	#############################################################

	include('../includes/inc.php');
	$pid=$_GET["p"];
	$pname=projectName($pid);
	$aid=$_GET["a"];
	$aname=adName($aid);
	if(isset($_GET["fid"])){
		$fid=$_GET["fid"];
		$fname=folderName($fid);
		$folder=true;
	} else {
		$folder=false;
	}
		
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
	
//	Check if page already exists
	$ckQuery=mysqli_query($con,"SELECT * FROM pages WHERE ad = ".$aid." AND project = ".$pid." AND name = '".$imgName."'");
	if(mysqli_num_rows($ckQuery)>0){
		echo 'EXISTS';
		return;
	}
	
	$adurl=WEB.'/ad.php?p='.$pid;
	if(isset($fid)){
		$adurl.='$f='.$fid;
	}
	$adurl.='&a='.$aid;
		
//	Initialize directory variables.
	$dir = ABSDIR.'proofs/';
	
	$user=$_GET["u"];
	$url=WEB.'/';
	
	$now=date("U",strtotime("now"));
	$strIN=array(' ','#');
	$strOUT=array('_','');
	
	if(preg_match("/pdf/i",$fileName)){
		$result=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$imgName."',".$pid.",".$aid.",1,'Active',".$now.",".$now.",".$now.",'','pdf')");
	} else {
		$result=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$imgName."',".$pid.",".$aid.",1,'Active',".$now.",".$now.",".$now.",'','jpg')");
	}
	$newPage=mysqli_query($con,"SELECT * FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND name = '".$imgName."'");
	$result=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$user."','Add Page','".$pname."','".$aname."','".$imgName."',1,'-',".$now.")");
	while($pg=mysqli_fetch_assoc($newPage)){
		$pgID=$pg["id"];
	
//	Move files from temporary storage folder to new folder.
		if($pg["hrtype"]=='pdf'){
			move_uploaded_file($_FILES["myfile"]["tmp_name"],$dir.'PDF/'.$pgID.'.pdf');
			$dimString = shell_exec(PDFINFO_PATH.'pdfinfo '.$dir.'PDF/'.$pgID.'.pdf');
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
//			$ret = $dimScale;
			$gs=exec_timeout(GS_PATH.'gs -dBATCH -dNOPAUSE -r72 -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=80 -g'.$dimScale.' -dPDFFitPage -sOutputFile="'.$dir.'IMG/'.$pgID.'.jpg" "'.$dir.'PDF/'.$pgID.'.pdf" 2>&1',30);
			if($gs!=='GS_OK'){
				exec('cp '.ABSDIR.'/images/logos/pdf_logo.jpg '.$dir.'IMG/'.$pgID.'.jpg');
			}
			$hrType="pdf";
			$hrImg=$dir.'PDF/'.$pgID.'.pdf';
			$imgImg=$dir.'IMG/'.$pgID.'.jpg';
		} else {
			move_uploaded_file($_FILES["myfile"]["tmp_name"],$dir.'JPG/'.$pgID.'.jpg');
			$uploadedfile = $dir.'JPG/'.$pgID.'.jpg';
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
			imagejpeg($tmp, $dir.'IMG/'.$pgID.'.jpg', 80);
       	 		
			exec('chmod -R 0775 "'.$dir.'IMG/'.$pgID.'.jpg"');
			$hrType="jpg";
			$hrImg=$dir.'JPG/'.$pgID.'.jpg';
			$imgImg=$dir.'IMG/'.$pgID.'.jpg';
		}
	}
	
//	Retrieve list of users to be notified.
	$uQuery=mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$pid.":%'");
	while($m=mysqli_fetch_assoc($uQuery)){
		if($m["email"]!==null && $m["email"]!=='' && $m["notify"]==1){
			$link=$adurl.'&pg='.$pgID.'&t='.$m["sha256"].':'.$m["id"];
			$optOut=$url.'unsubscribe.php?&t='.$m["sha256"].':'.$m["id"];
			
			$to = $m["email"];
			$subject = 'New Page Upload';
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
							<p><b>A new page has been added to a proof on Proof Express.</b></p>
							<br/>
							<h3>'.$aname.' / '.$imgName.' (Revision 1)</h3>
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
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';

			// Additional headers
			$headers[] = 'To: '.$m["fullname"].' <'.$to.'>';
			$headers[] = 'From: Proof Express <proofexpress@spartannash.com>';

			// Mail it
			mail($to, $subject, $message, implode("\r\n", $headers));
		}
	}
		
	$pgCtQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$aid);
	while($pgCt=mysqli_fetch_assoc($pgCtQuery)){
		$newCt=$pgCt["pages"] + 1;
		$newPgCt=mysqli_query($con,"UPDATE ads SET pages = ".$newCt." WHERE id = ".$aid);
	}

    echo json_encode($adurl.'&pg='.$pgID);
?>