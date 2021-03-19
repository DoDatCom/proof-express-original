<?php
//	#############################################################
//	# Name:		log_pagedownload.php							#
//	# Purpose:	AJAX script to generate log entries whenever	#
//	#			a user downloads a proof page.					#
//	#############################################################

	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	
	$page=$_POST["page"];
	$pname=$_POST["pname"];
	$aname=$_POST["aname"];
	$rev=$_POST["rev"];
	$user=$_POST["user"];
	
	$vQuery=mysqli_query($con,"SELECT * FROM user_log WHERE activity = 'Download Page' AND project = '".$pname."' AND ad = '".$aname."' AND page = '".$page."' AND rev = ".$rev." AND user = '".$user."'");
	if(mysqli_num_rows($vQuery)>0){
		return;
	} else {
		$newLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$user."','Download Page','".$pname."','".$aname."','".$page."',".$rev.",'-',".$now.")");
	}
?>