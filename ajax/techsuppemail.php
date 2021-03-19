<?php
	include('../includes/inc.php');
	require_once('../class.phpmailer.php');
	$url=WEB.'/';
	
	$userList=mysqli_query($con,"SELECT * FROM users WHERE username = '".$_POST['un']."'");
	while($row = mysqli_fetch_assoc($userList)){
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->SMTPAuth = true;
		$mail->Username = MAILUSER;
		$mail->Host = gethostbyname(MAILHOST);
		$mail->From = MAILFROM;
		$mail->FromName = 'SpartanNash Proof Express';
		$mail->Subject = $_POST['s'];
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
							<p>A Proof Express Tech Support Request has been made.</p>
							<div class="well">
								<p align="left">Issue:</p>
								<p align="left"><b>'.$_POST['i'].'</b></p>
							</div>
							<p><small><i>Request made by '.$row['fullname'].', '.date('n/d/Y @ g:iA T',strtotime("NOW")).'</i></small></p>
							<hr/>
							<p>To respond to this support request, contact the user at '.$row['email'].'.</p>
							<br/>
							<p><i>Thank you!</i></p>
						</div>
						<div class="col-sm-2"></div>
					</div>
				</body>
			</html>
		';
		$mail->AddAddress('dwilkins@dodatcommunications.com');
		$deliver = $mail->Send() or die("Error sending email: ".$mail->ErrorInfo());
		if($deliver) {
			echo 'Your support request has been sent successfully.';
		}
	}
?>