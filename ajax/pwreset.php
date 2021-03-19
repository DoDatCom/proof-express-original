<?php
	include('../includes/inc.php');
	require_once('../class.phpmailer.php');
	$url=WEB.'/';
	
	
	$userList=mysqli_query($con,"SELECT * FROM users WHERE email = '".$_POST['e']."'");
	if(mysqli_num_rows($userList)>0) {
		while($row = mysqli_fetch_assoc($userList)){
			// Multiple recipients
			$to = $_POST["e"]; // note the comma

			// Subject
			$subject = 'Proof Express Reset';

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
				<p>A request to reset your Proof Express login credentials has been submitted.</p>
				<p>This request was submitted '.date("l, F j, Y \a\t g:iA T",strtotime("now")).'.</p>
				<p>If you did <b>not</b> initialize this request, please disregard this message.</p>
				<hr/>
				<p>To complete this reset request, <a href="'.WEB.'/reset.php?token='.$row["sha256"].':'.$row["id"].'">click here.</a></p>
				<br/>
				<p><i>Thank you!</i></p>
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
			$headers[] = 'To: '.$row["fullname"].' <'.$_POST["e"].'>';
			$headers[] = 'From: Proof Express <proofexpress@spartannash.com>';

			// Mail it
			mail($to, $subject, $message, implode("\r\n", $headers));
			if($deliver) {
				echo 'OK';
			}
		}
	} else {
		echo 'Not found';
	} 
?>