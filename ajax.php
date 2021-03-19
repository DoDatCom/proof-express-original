<?php
	session_start();
	include('ajax/mysql.php');
	$act=$_POST["act"];
	$id=$_POST["id"];
	$project=$_POST["p"];
	$wk=$_POST["w"];
	$qp=$_POST["qp"];
	$m=$_POST["m"];
	$hlx=$_POST["hlx"];
	$hly=$_POST["hly"];
	$hlw=$_POST["hlw"];
	$hlh=$_POST["hlh"];
	$text=$_POST["text"];
	$hl=$_POST["hl"];
	$notes=$_POST["notes"];
	$app=$_POST["app"];
	
	$name=$_POST["name"];
	$pass=$_POST["pass"];
	$comp=$_POST["comp"];
	$email=$_POST["email"];
	$admin=$_POST["admin"];
	$cust=$_POST["cust"];
  	
//	Save job note
	if($act=="jobnote"){
		$query='SELECT * FROM proofs WHERE proof = \''.$proof.'\'';
		$result=mysqli_query($con,$query);
		if(mysqli_num_rows($result)==1) {
			while($row = mysqli_fetch_assoc($result)){
				$id = $row["id"];
  				$result=mysqli_query($con,'UPDATE proofs SET notes = \''.$notes.'\', modifier = \''.$_SESSION["user"].'\' WHERE id = '.$id) or die(mysqli_error($con));
  				if($result){
  					$resp='Job note saved.';
  				} else {
  					$resp='Error: '.mysqli_error($con);
  				}
  			}
  		}
  	}
  	
//	Save page note
	if($act=="pagenote"){
		$query="SELECT * FROM pages WHERE proof = '".$proof."' AND version = '".$v."' AND page = '".$p."'";
		$result=mysqli_query($con,$query);
		if(mysqli_num_rows($result)==1) {
			while($row = mysqli_fetch_assoc($result)){
				$id = $row["id"];
				if($notes==''){
					$result=mysqli_query($con,"DELETE FROM pages WHERE id = ".$id) or die(mysqli_error($con));
					$resp='Page note removed.';
				} else {
  					$result=mysqli_query($con,'UPDATE pages SET notes = \''.$notes.'\', user = \''.$_SESSION["user"].'\' WHERE id = '.$id) or die(mysqli_error($con));
  					if($result){
  						$resp='Page note saved.';
  					} else {
  						$resp='Error: '.mysqli_error($con);
  					}
  				}
  			}
  		} else {
  			if($notes==''){
  				$resp='Page note is blank.';
  			} else {
  				$result=mysqli_query($con,"INSERT INTO pages (id, proof, version, page, notes, user, status) VALUES (null,'".$proof."','".$v."','".$p."','".str_replace("\'","\\\'",$notes)."','".$_SESSION["user"]."','Ready')") or die(mysqli_error($con));
  				if($result){
  					$resp='Page note created.';
  				} else {
  					$resp='Error: '.mysqli_error($con);
  				}
  			}
  		}
  	}
  	
//	Delete proof
	if($act=="delproof"){
	// Delete all files in proof folder
	// IMG folder
		$imgArray = array_diff(scandir(__DIR__.'/proofs/'.$proof.'/img/'), array(".", "..", ".DS_Store"));
		natcasesort($imgArray);
		foreach($imgArray as $img) {
			if(strpos($img,'.jpg')==true){
				unlink(__DIR__.'/proofs/'.$proof.'/img/'.$img);
			}
		}
	// TN folder
		$tnArray = array_diff(scandir(__DIR__.'/proofs/'.$proof.'/tn/'), array(".", "..", ".DS_Store"));
		natcasesort($tnArray);
		foreach($tnArray as $tn) {
			if(strpos($tn,'.jpg')==true){
				unlink(__DIR__.'/proofs/'.$proof.'/tn/'.$tn);
			}
		}
	// PDF folder
		$pdfArray = array_diff(scandir(__DIR__.'/proofs/'.$proof.'/pdf/'), array(".", "..", ".DS_Store"));
		natcasesort($pdfArray);
		foreach($pdfArray as $pdf) {
			if(strpos($pdf,'.pdf')==true){
				unlink(__DIR__.'/proofs/'.$proof.'/pdf/'.$pdf);
				unlink(__DIR__.'/proofs/'.$proof.'/pdf/preview.jpg');
			}
		}
	// 	Update MySQL info
		$result=mysqli_query($con,'DELETE FROM proofs WHERE proof = \''.$proof.'\'') or die('Error: '.mysqli_error($con));
		$result=mysqli_query($con,'DELETE FROM pages WHERE proof = \''.$proof.'\'') or die('Error: '.mysqli_error($con));
		$result=mysqli_query($con,'DELETE FROM annotation WHERE proof = \''.$proof.'\'') or die('Error: '.mysqli_error($con));
	//	Delete proof folder
		rmdir(__DIR__.'/proofs/'.$proof.'/img');
		rmdir(__DIR__.'/proofs/'.$proof.'/pdf');
		rmdir(__DIR__.'/proofs/'.$proof.'/tn');
		rmdir(__DIR__.'/proofs/'.$proof);
	//	Server response
		$resp = '"'.$proof.'" has been removed from QuickProof.';
  	}
  	
//	Delete user
	if($act=="deluser"){
		$result=mysqli_query($con,'DELETE FROM users WHERE id = \''.$id.'\'') or die('Error: '.mysqli_error($con));
	//	Server response
		$resp = 'User has been removed from QuickProof.';
  	}
  	
//	Update user info
	if($act=="edituser"){
		$result=mysqli_query($con,'UPDATE users SET name = \''.$name.'\', pass = \''.$pass.'\', company = \''.$comp.'\', email = \''.$email.'\', admin = \''.$admin.'\', customer = \''.$cust.'\'WHERE id = '.$id) or die("Error: ".mysqli_error($con));
	}
  	
//	Add new user
	if($act=="newuser"){
		$result=mysqli_query($con,'INSERT INTO users (id, name, pass, company, email, admin, customer) VALUES (null,\''.$name.'\',\''.$pass.'\',\''.$comp.'\',\''.$email.'\',\''.$admin.'\',\''.$cust.'\')') or die(mysqli_error($con));
	//	Server response
		$resp = $name.' has been added in QuickProof.';
  	}

//	Customer approval
	if($act=="approve"){
		require_once('class.phpmailer.php');
		$url='http://www.dodatcommunications.com/';
		
	//	Retrieve creator ID from database		
		$getCID=mysqli_query($con,"SELECT * FROM proofs WHERE proof = '".$proof."' AND version = '".$v."'") or die("Proof read error. ".mysqli_error($con));
		
	// Generate notation info for email
		while($row = mysqli_fetch_assoc($getCID)){
			$id = $row["id"];
			$cid = $row["cid"];
			if($app=="approved"){
				$appMsg='
					<tr class="success">
						<td>'.$proof.'</td>
						<td>'.$p.'</td>
						<td>'.$v.'</td>
						<td>Approved!</td>
					</tr>';
				$status='Approved';
			} elseif($app=="appwithchg"){
				$appMsg='
					<tr class="warning">
						<td>'.$proof.'</td>
						<td>'.$p.'</td>
						<td>'.$v.'</td>
						<td>Approved with changes</td>
					</tr>';
				$status='Approved w/changes';
			} elseif($app=="newproof"){
				$appMsg='
					<tr class="danger">
						<td>'.$proof.'</td>
						<td>'.$p.'</td>
						<td>'.$v.'</td>
						<td>New proof needed</td>
					</tr>';
				$status='New proof needed';
			}
		}
		
	// Retrieve creator's email address from database	
		$getEmail=mysqli_query($con,"SELECT * FROM users WHERE id = '".$cid."'") or die("User read error. ".mysqli_error($con));
		while($row = mysqli_fetch_assoc($getEmail)){
			$email = $row["email"];
		}
		
	//	Retrieve page-specifice markup information from database
		$getNotes=mysqli_query($con,"SELECT * FROM annotation WHERE proof = '".$proof."' AND version = '".$v."' AND page = '".$p."' ORDER BY mark") or die("Annotation read error. ".mysqli_error($con));
		$i = 0;
		while($row = mysqli_fetch_assoc($getNotes)){
			$markText[$i] = $row["text"];
			$i++;
		}

	//	Build email body
		$body = '<html>
					<head>
						<meta charset="utf-8">
						<link href="'.$url.'css/bootstrap.css" rel="stylesheet">
						<link href="'.$url.'css/dodat.css" rel="stylesheet">
						<style type="text/css" media="screen">
							body { background:#ccc; margin:1em; text-align:center; }
						</style>
					</head>
					<body>
						<div class="row">
							<div class="col-sm-4">
								<p align="center"><img src="'.$url.'qp/images/DoDat_QuickProof_Logo.png" class="img-responsive"/></p>
							</div>
							<div class="col-sm-8">
								<div class="well">
									<p>'.$_SESSION["user"].' has updated the following proof:</p>
									
									<table class="table">
										<tr>
											<th>Proof name</th>
											<th>Page #</th>
											<th>Version</th>
											<th>Action requested</th>
										</tr>';
		$body.= $appMsg.'</table>';
		if($app=="newproof" || $app=="appwithchg") {
			$body.='<hr/><p style="color:#000;">The following changes have been requested:</p><ul style="padding-left:20%;padding-right:20%;">';
			$h=0;
			while($h<$i){
				$body.='<li style="text-align:left;">'.$markText[$h].'</li>';
				$h++;
			}
		}
		$body.='<hr/><p>To view this proof,</p><a href="'.$url.'qp/proof.php?proof='.$proof.'&v='.$v.'&p='.$p.'&s=0&uid='.$cid.'" class="btn btn-info" role="button">Click here!</a>
			</div></div></div></body></html>
		';
			
	//	Compile and send email
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->SMTPAuth = true;
		$mail->Username = 'support@dodatcommunications.com';
		$mail->Host = gethostbyname('smtp.W14D.comcast.net');
		$mail->From = 'support@dodatcommunications.com';
		$mail->FromName = 'DoDat QuickProof';
		$mail->Subject = 'Customer Proof Update ('.$proof.')';
		$mail->Body = $body;
		$mail->AddAddress($email);
		if($mail->Send()){ $resp = "Proof status has been updated, and a notice has been sent to the artist."; }
		
		$updateProof=mysqli_query($con,'UPDATE proofs SET status = \''.$status.'\', modifier = \''.$_SESSION["user"].'\' WHERE id = '.$id) or die("Update failed. ".mysqli_error($con));
	}
  	
//	Send server response (if any)
	if($resp){
		echo $resp;
	}
?>