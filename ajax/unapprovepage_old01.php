<?php
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$pgid=$_POST["pg"];
	$aid=$_POST["a"];
	
	$nameQuery=mysqli_query($con,"SELECT name FROM pages WHERE id = ".$pgid);
	while($name=mysqli_fetch_assoc($nameQuery)){
		$pgQuery=mysqli_query($con,"UPDATE pages SET status = 'Active', last_modify = ".$now." WHERE ad = ".$aid." AND name = '".$name["name"]."'");
	}
	
	$appCount=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$aid);
	while($aCt=mysqli_fetch_assoc($appCount)){
		$newCt=$aCt["approved"] - 1;
		$aCtQuery=mysqli_query($con,"UPDATE ads SET approved = ".$newCt." WHERE id = ".$aid);
	}
	$logQuery=mysqli_query($con,"SELECT project,ad,name,rev FROM pages WHERE id = ".$pgid);
	while($log=mysqli_fetch_assoc($logQuery)){
		$newLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Unapproval','".projectName($log["project"])."','".adName($log["ad"])."','".$log["name"]."',".$log["rev"].",'-',".$now.")");
		echo 'OK';
	}
?>