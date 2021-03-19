<?php
//	#############################################################
//	# Name:		pageviewlog.php									#
//	# Purpose:	AJAX script to generate log entries whenever	#
//	#			a user views a proof page.						#
//	#############################################################

	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	
	$uid=userID($_POST["u"]);
	$pg=$_POST["pg"];
	$rev=$_POST["rev"];
	
	$vQuery=mysqli_query($con,"SELECT * FROM pages WHERE id = ".$pg);
	while($log=mysqli_fetch_assoc($vQuery)){
		$newLog=mysqli_query($con,"UPDATE pages SET viewlog = CONCAT(viewlog,'u".$uid."_".$now."_".$rev.":') WHERE id = ".$pg) or die(mysqli_errno($newLog));
	}
?>