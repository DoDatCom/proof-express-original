<?php
	include('../includes/inc.php');
	
	$sendIt="NO";
	
//	Process GET data.
	$pid=$_GET["pid"];
	$pname=projectName($pid);
	if(isset($_GET["fid"])){
		$fid=$_GET["fid"];
		$folder=true;
	} else {
		$folder=false;
	}
	$aname=$_GET["aname"];
	$user=$_GET["u"];
	$strIN=array(' ','#');
	$strOUT=array('_','');
	
	require_once('../class.phpmailer.php');
	
	$url=WEB.'/';

	$now=date("U",strtotime("now"));
	
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
		
//	Initialize directory variables.
	$dir = ABSDIR.'proofs/';
	
	if($folder==true){
		$qString="SELECT * FROM ads WHERE project = ".$pid." AND folder = ".$fid." AND name = '".$aname."' AND status = 'Active'";
		$query=mysqli_query($con,$qString);
		$nQuery="INSERT INTO ads (id,name,project,comments,approved,pages,due_date,status,type,folder,created,last_activity) VALUES (null,'".$aname."',".$pid.",0,0,0,TIMESTAMPADD(WEEK,1,NOW()),'Active','A',".$fid.",".$now.",".$now.")";
	} else {
		$qString="SELECT * FROM ads WHERE project = ".$pid." AND type = 'A' AND name = '".$aname."' AND status = 'Active'";
		$query=mysqli_query($con,$qString);
		$nQuery="INSERT INTO ads (id,name,project,comments,approved,pages,due_date,status,type,folder,created,last_activity) VALUES (null,'".$aname."',".$pid.",0,0,0,TIMESTAMPADD(WEEK,1,NOW()),'Active','A','root',".$now.",".$now.")";
	}
	if(mysqli_num_rows($query)==0){
		$newad=mysqli_query($con,$nQuery);
		$query=mysqli_query($con,$qString);
	}
	while($row = mysqli_fetch_assoc($query)){
		$ad=$row["id"];
		$sendOnce=$row["pages"];
	}
	
	if(preg_match("/pdf/i",$fileName)){
		$result=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$imgName."',".$pid.",".$ad.",1,'Active',".$now.",".$now.",".$now.",'','pdf')");
	} else {
		$result=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$imgName."',".$pid.",".$ad.",1,'Active',".$now.",".$now.",".$now.",'','jpg')");
	}
	$newPage=mysqli_query($con,"SELECT * FROM pages WHERE project = ".$pid." AND ad = ".$ad." AND name = '".$imgName."'");
	$result=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$user."','Add Page','".$pname."','".$aname."','".$imgName."',1,'-',".$now.")");
	while($pg=mysqli_fetch_assoc($newPage)){
		if($pg["hrtype"]=='pdf'){
			move_uploaded_file($_FILES["myfile"]["tmp_name"],$dir.'PDF/'.$pg["id"].'.pdf');
			exec('chmod -R 0775 "'.$dir.'PDF/'.$pg["id"].'.pdf');
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
//     	 	$ret = $dimScale;
			$gs=exec_timeout(GS_PATH.'gs -dBATCH -dNOPAUSE -r72 -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=80 -g'.$dimScale.' -dPDFFitPage -sOutputFile="'.$dir.'IMG/'.$pg["id"].'.jpg" "'.$dir.'PDF/'.$pg["id"].'.pdf" 2>&1',60);
			if($gs!=='GS_OK'){
				exec('cp '.ABSDIR.'/images/logos/pdf_logo.jpg '.$aDir.'IMG/'.$pg["id"].'.jpg');
			}
		} else {
    		move_uploaded_file($_FILES["myfile"]["tmp_name"],$dir.'JPG/'.$pg["id"].'.jpg');
       	 	exec('chmod -R 0775 "'.$dir.'JPG/'.$pg["id"].'.jpg');
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
    	}
	}
	
//	See if 
	if($sendOnce==1){
		$logOnce=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$user."','Upload Ad','".$pname."','".$aname."','".$imgName."',1,'-',".$now.")");
		$uQuery=mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$pid.":%'");
		while($m=mysqli_fetch_assoc($uQuery)){
			if($m["email"]!==null && $m["email"]!=='' && $m["notify"]==1){
				$link=$url.'ad.php?p='.$pid.'&w='.$ad.'&t='.$m["sha256"].':'.$m["id"];
				$optOut=$url.'unsubscribe.php?&t='.$m["sha256"].':'.$m["id"];
				// Multiple recipients
				$to = $m["email"]; // note the comma

				// Subject
				$subject = 'New Proof Upload';

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
							<p><b>A new proof is available for you to view on Proof Express.</b></p>
							<br/>
							<h3>'.$aname.' (Revision 1)</h3>
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
	}
		
	$pgCtQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND id = ".$ad);
	while($pgCt=mysqli_fetch_assoc($pgCtQuery)){
		$newCt=$pgCt["pages"] + 1;
		$newPgCt=mysqli_query($con,"UPDATE ads SET pages = ".$newCt." WHERE project = ".$pid." AND id = ".$ad);
	}

    echo json_encode($sendIt);

?>
