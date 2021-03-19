<?php
	include('../includes/inc.php');
//	Process GET data.
	$p=$_GET["p"];
	$w=$_GET["w"];
	$u=$_GET["u"];
	require_once('../class.phpmailer.php');
	$url=WEB.'/';
	$now=date("U",strtotime("now"));
	$strIN=array(' ','#');
	$strOUT=array('_','');
	
//	Retrieve project name from project id
	$query=mysqli_query($con,"SELECT * FROM projects WHERE id = '".$p."'") or die("This folder does not exist");
	while($row=mysqli_fetch_assoc($query)){
		$project=$row["name"];
	}

//	Retrieve ad name from ad id
	$query=mysqli_query($con,"SELECT * FROM ads WHERE id = '".$w."'") or die("This folder does not exist");
	while($row=mysqli_fetch_assoc($query)){
		$ad=$row["name"];
	}
	
//	Prepare directory variables.
	$dir = ABSDIR."projects/";
	$projectdir = $dir.str_replace($strIN,$strOUT,$project).'/';
	$weekdir = $projectdir.str_replace($strIN,$strOUT,$ad).'/';
	$pdfdir = $weekdir.'pdf';
	
//	Modify directory permissions for writing.
	exec('chmod -R 0775 "'.$weekdir.'"');
	
//	Move files from temporary storage folder to new folder.
	if(isset($_FILES["myfile"])) {
		$ret = array();
		$error =$_FILES["myfile"]["error"];
    	if(!is_array($_FILES["myfile"]['name'])) { //single file
       	 	$fileName = $_FILES["myfile"]["name"];
       	 	$in = array('.pdf','.PDF',' ','(',')');
       	 	$out = array('','','_','','');
       	 	$imgName = str_replace($in,$out,$fileName);
       	 	move_uploaded_file($_FILES["myfile"]["tmp_name"],$pdfdir.'/1/'.$imgName.'.pdf');
       	 	$dimString = shell_exec('/bin/pdfinfo '.$pdfdir.'/1/'.$imgName.'.pdf');
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
			exec('/bin/gs -dBATCH -dNOPAUSE -r72 -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=80 -g'.$dimScale.' -dPDFFitPage -sOutputFile="'.$weekdir.'/img/1/'.$imgName.'.jpg" "'.$pdfdir.'/1/'.$imgName.'.pdf" 2>&1');
			$result=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify) VALUES (null,'".$imgName."',".$p.",".$w.",1,'Active',".$now.",".$now.",".$now.")");
			$result=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$u."','Add Page','".$project."','".$ad."','".$imgName."',1,'-',".$now.")");
    	} else { //multiple files
			$fileCount = count($_FILES["myfile"]['name']);
			for($i=0; $i < $fileCount; $i++) {
				$fileName = $_FILES["myfile"]["name"][$i];
				$in = array('.pdf','.PDF',' ');
       	 		$out = array('','','_');
       	 		$imgName = str_replace($in,$out,$fileName);
				move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$pdfdir.$imgName.'.pdf');
				exec('/bin/gs -dBATCH -dNOPAUSE -r72 -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=80 -dDOINTERPOLATE -sOutputFile='.$weekdir.'/img/0/'.$imgName.'.jpg '.$pdfdir.'/'.$imgName.'.pdf')or die("GhostScript error");
			}
		}
	}
	
//	Retrieve list of users to be notified.
	$uQuery=mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$p.":%' AND notify = 1");
	while($m=mysqli_fetch_assoc($uQuery)){
		if($m["email"]!==null && $m["email"]!==''){
			$link=$url.'ad.php?p='.$p.'&w='.$w.'&qp='.$imgName.'&t='.$m["sha256"].':'.$m["id"];
			$optOut=$url.'unsubscribe.php?&t='.$m["sha256"].':'.$m["id"];
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->IsHTML(true);
			$mail->SMTPAuth = true;
			$mail->Username = 'support@dodatcommunications.com';
			$mail->Host = gethostbyname('smtpout.secureserver.net');
			$mail->From = 'support@dodatcommunications.com';
			$mail->FromName = 'SpartanNash Proof Express';
			$mail->Subject = 'New Page Upload';
			$mail->Body = '
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
							<h3>'.$ad.' / '.$imgName.' (Revision 1)</h3>
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
			$mail->AddAddress($m["email"]);
			$deliver = $mail->Send() or die("Error sending email: ".$mail->ErrorInfo());
		}
	}	
    echo json_encode($ret);
?>
