<?php
//	Process new user info
	include('../includes/inc.php');
	
//	Compile folder variables
	$pid=$_POST["pid"];
	$pname=projectName($pid);
	$fid=$_POST["fid"];
	$fname=folderName($fid);
	
	$result=mysqli_query($con,"DELETE FROM ads WHERE id = ".$fid);
	if(!$result){
		echo mysqli_errno($result);
	}
	$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Delete folder','".$pname."','".$fname."','-','-','-',".date("U",strtotime("now")).")");
?>