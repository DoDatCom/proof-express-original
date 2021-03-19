<?php

//	#############################################################
//	# Name:		changeduedate.php								#
//	# Purpose:	AJAX script to change ad due date in MySQL 	 	#
//	#			'ads' MySQL table.								#
//	#############################################################

	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$result=mysqli_query($con,"UPDATE ads SET due_date = '".date("Y-m-d H:i:s",strtotime($_POST["d"]))."', last_activity = ".$now." WHERE id = ".$_POST["i"]);
	if(!$result){
		echo mysqli_errno($result);
	}
	$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Change due date','".$_POST["p"]."','".$_POST["w"]."','-','-','Date due: ".date("m/d/Y",strtotime($_POST["d"]))."',".$now.")");
?>