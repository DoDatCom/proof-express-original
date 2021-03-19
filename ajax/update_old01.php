<?php
	include('../includes/inc.php');
//	Process GET data.
	$pid=$_GET["p"];
	if(isset($_GET["f"])){
		$fid=$_GET["f"];
		$folder=true;
	} else {
		$folder=false;
	}
	$aid=$_GET["w"];
	$pg=$_GET["pg"];
	$user=$_GET["u"];
	require_once('../class.phpmailer.php');
	$url=WEB.'/';
	
	
	$now=date("U",strtotime("now"));
	$strIN=array(' ','#');
	$strOUT=array('_','');
	
//	Retrieve project folder name
	$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$pid);
	while($pQ=mysqli_fetch_assoc($pQuery)){
		$pname=$pQ["name"];
	}
//	Retrieve ad folder name
	$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$aid);
	while($aQ=mysqli_fetch_assoc($aQuery)){
		$aname=$aQ["name"];
	}
//	Check for subfolder
	if($folder==true){
		$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$fid);
		while($fQ=mysqli_fetch_assoc($fQuery)){
			$fname=$fQ["name"];
		}
	}
//	Retrieve next revision in sequence
	$rQuery=mysqli_query($con,"SELECT * FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND name = '".$pg."'");
	while($rQ=mysqli_fetch_assoc($rQuery)){
		$rev=$rQ["rev"] + 1;
		$rid=$rQ["id"];
	}
//	Initialize directory variables
	$dir = ABSDIR."projects/";
	if($folder==true){
		$aDir = $dir.str_replace($strIN,$strOUT,$pname).'/'.str_replace($strIN,$strOUT,$fname).'/'.str_replace($strIN,$strOUT,$aname).'/';
	} else {
		$aDir = $dir.str_replace($strIN,$strOUT,$pname).'/'.str_replace($strIN,$strOUT,$aname).'/';
	}
	exec("chown -R apache:apache ".$aDir);

//	Move files from temporary storage folder to new folder.
	if(isset($_FILES["myfile"])) {
		$ret = array();
		$error =$_FILES["myfile"]["error"];
    	if(!is_array($_FILES["myfile"]['name'])) { //single file
//       	 	$fileName = $_FILES["myfile"]["name"];
//       	 	$in = array('.pdf','.PDF',' ','(',')');
//       	 	$out = array('','','_','','');
//       	 	$imgName = str_replace($in,$out,$fileName);
			$imgName=$pg;
       	 	move_uploaded_file($_FILES["myfile"]["tmp_name"],$aDir.'pdf/'.$imgName.'/'.$imgName.'_v'.$rev.'.pdf');
       	 	$dimString = shell_exec('/bin/pdfinfo '.$aDir.'pdf/'.$imgName.'/'.$imgName.'_v'.$rev.'.pdf');
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
			exec('/bin/gs -dBATCH -dNOPAUSE -r72 -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=80 -g'.$dimScale.' -dPDFFitPage -sOutputFile="'.$aDir.'img/'.$imgName.'/'.$imgName.'_v'.$rev.'.jpg" "'.$aDir.'pdf/'.$imgName.'/'.$imgName.'_v'.$rev.'.pdf" 2>&1');
			$rUpdate=mysqli_query($con,"UPDATE pages SET rev = ".$rev.", last_modify = ".$now." WHERE id = ".$rid);
			$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$user."','Update Page',".$pid.",".$aid.",'".$imgName."',".$rev.",'-',".$now.")");
    	} else { //multiple files
			$fileCount = count($_FILES["myfile"]['name']);
			for($i=0; $i < $fileCount; $i++) {
				$fileName = $_FILES["myfile"]["name"][$i];
				$in = array('.pdf','.PDF',' ');
       	 		$out = array('','','_');
       	 		$imgName = str_replace($in,$out,$fileName);
				move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$pdf_dir.$imgName.'.pdf');
				exec('/bin/gs -dBATCH -dNOPAUSE -r72 -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dJPEGQ=80 -dDOINTERPOLATE -sOutputFile='.$new_week.'/img/0/'.$imgName.'.jpg '.$pdf_dir.'/'.$imgName.'.pdf')or die("GhostScript error");
			}
		}
	}
	
//	Retrieve list of users to be notified.
	$uQuery=mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$pid.":%' AND notify = 1");
	while($m=mysqli_fetch_assoc($uQuery)){
		if($m["email"]!==null && $m["email"]!==''){
			$link=$url.'ad.php?p='.$pid.'&w='.$aid.'&qp='.$imgName.'&r='.$rev.'&t='.$m["sha256"].':'.$m["id"];
			$optOut=$url.'unsubscribe.php?&t='.$m["sha256"].':'.$m["id"];
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->IsHTML(true);
			$mail->SMTPAuth = true;
			$mail->Username = MAILUSER;
			$mail->Host = gethostbyname(MAILHOST);
			$mail->From = MAILFROM;
			$mail->FromName = 'SpartanNash Proof Express';
			$mail->Subject = 'Page Update';
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
			$mail->AddAddress($m["email"]);
			$deliver = $mail->Send() or die("Error sending email: ".$mail->ErrorInfo());
		}
	}
	$ret=$rev;
    echo json_encode($ret);
?>
