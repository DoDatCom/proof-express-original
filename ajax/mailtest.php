<?php

//	#############################################################
//	# Name:		mailtestphp										#
//	# Purpose:	AJAX script to test mail functionality.			#
//	#############################################################

	include('../includes/inc.php');
	
	// Multiple recipients
	$to = $_POST["to"]; // note the comma

	// Subject
	$subject = 'New Email Test';

	// Message
	$message = '
<html>
	<head>
		<meta charset="utf-8">
		<link href="'.WEB.'css/bootstrap.css" rel="stylesheet">
		<link href="'.WEB.'css/dodat.css" rel="stylesheet">
		<style type="text/css" media="screen">
			body { margin:1em; text-align:center; }
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<p align="center"><img src="'.WEB.'images/SN_logo_400.png" class="img-responsive"/></p>
				<div class="well">
					<div class="row">
						<div class="col-sm-12">
							<h3>If you can read this...</h3>
							<br/>
							<h3>the test was a success!</h3>
						</div>
					</div>
				</div>
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
$headers[] = 'To: Test User <'.$_POST["to"].'>';
$headers[] = 'From: Proof Express <proofexpress@spartannash.com>';

// Mail it
mail($to, $subject, $message, implode("\r\n", $headers));
			
?>