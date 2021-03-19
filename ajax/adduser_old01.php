<?php
	include('../includes/inc.php');
	require_once('../class.phpmailer.php');
	$url=WEB.'/';
	$now=date("U",strtotime("now"));
	
//	Process new user info
	if($_POST["r"]=="alpha"){
		$role="Administrator";
		$ring="admin";
		$bnw=hash('md5','alpha');
	}
	if($_POST["r"]=="beta"){
		$role="Marketing";
		$ring="market";
		$bnw=hash('md5','beta');
	}
	if($_POST["r"]=="gamma"){
		$role="Designer";
		$ring="admin";
		$bnw=hash('md5','gamma');
	}
	if($_POST["r"]=="delta"){
		$role="View / Comment / Approve";
		$ring=$_POST["t"];
		$bnw=hash('md5','delta');
	}
	if($_POST["r"]=="epsilon"){
		$role="View / Comment";
		$ring=$_POST["t"];
		$bnw=hash('md5','epsilon');
	}
	if($_POST["r"]=="zeta"){
		$role="View / Download Only";
		$ring=$_POST["t"];
		$bnw=hash('md5','zeta');
	}
	
	$strIN=array("Eastern","Central","Mountain","Pacific");
	$strOUT=array("America/New_York","America/Chicago","America/Denver","America/Los_Angeles");
	
	$result=mysqli_query($con,"INSERT INTO users (id, fullname, username, sha256, phone, email, role, marketing, bnw, ring, lastlogin,created, tzone, notify, status) VALUES (null,'".$_POST["fn"]."','".$_POST["un"]."','".hash("sha256",$_POST["pw"])."','".$_POST["ph"]."','".$_POST["e"]."','".$role."','".$_POST["m"]."','".$bnw."','".$ring."',".$now.",".$now.",'".str_replace($strIN,$strOUT,$_POST["tz"])."','".$_POST["n"]."','')");
	if($result){
		echo $_POST["fn"].' has been added successfully.';
		$to = $_POST["e"];
		$subject = 'Welcome to SpartanNash Proof Express';
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
										<p>An account has been created for you on Proof Express.<br/>Please use the link and login credentials below to access your ad proofs.<br/>If you have any questions please reach out to your Image Center contact.</p>
										<br/>
										<p><b>Account Information</b></p>
										<table class="table table-bordered" style="width:50%" align="center">
											<thead></thead>
											<tbody>
												<tr>
													<td>Username:</td>
													<td>'.$_POST["un"].'</td>
												</tr>
												<tr>
													<td>Password:</td>
													<td>'.$_POST["pw"].'</td>
												</tr>
											</tbody>
										</table>
										<br/>
										<a href="http://www.proofexpress.com" class="btn btn-info" role="button">Visit Proof Express</a>
										<br/>
										<p><i>Thank you!</i></p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
				</body>
			</html>
		';
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';

		// Additional headers
		$headers[] = 'To: '.$_POST["fn"].' <'.$_POST["e"].'>';
		$headers[] = 'From: Proof Express <proofexpress@spartannash.com>';

		// Mail it
		mail($to, $subject, $message, implode("\r\n", $headers));

	}
	$newLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Added New User','-','-','-',0,'".$_POST["un"]."',".$now.")") or die(mysqli_errno($newLog));
?>