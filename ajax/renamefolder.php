<?php
//	Process new user info
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$dir=ABSDIR.'projects/';
	$dirIN=array(' ','#');
	$dirOUT=array('_','');
	
	$result=mysqli_query($con,"UPDATE ads SET name = '".$_POST["n"]."', last_activity = ".$now." WHERE id = ".$_POST["a"]);
	$pQuery=mysqli_query($con,"SELECT project FROM ads WHERE id = ".$_POST["a"]);
	while($pid=mysqli_fetch_assoc($pQuery)){
		$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Rename ".$_POST["t"]."','".projectName($pid["project"])."','".$_POST["n"]."','-','-','-',".$now.")");
	}
?>