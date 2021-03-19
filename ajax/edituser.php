<?php

	include('../includes/inc.php');
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
	
	$result=mysqli_query($con,"UPDATE users SET fullname = '".$_POST["fn"]."', phone = '".$_POST["ph"]."', email = '".$_POST["e"]."', role = '".$role."', bnw = '".$bnw."', ring = '".$ring."', tzone = '".str_replace($strIN,$strOUT,$_POST["tz"])."', notify = ".$_POST["n"]." WHERE id = ".$_POST["u"]) or die(mysqli_errno($result));
	if($result){
		echo $_POST["fn"].' has been saved successfully.';
	}
?>