<?php

	include('../includes/inc.php');
	$strIN=array(' ','#');
	$strOUT=array('_','');
	
	$ret='';
	$pid=$_POST["p"];
	$pname=projectName($pid);
	$aid=$_POST["a"];
	$aname=adName($aid);
	
	$adRemove=mysqli_query($con,"DELETE FROM ads WHERE id = ".$aid);
	if(!$adRemove){
		$ret.='*'.mysqli_errno($adRemove);
	}
	$pgQuery=mysqli_query($con,"SELECT id,hrtype FROM pages WHERE ad = ".$aid);
	while($pg=mysqli_fetch_assoc($pgQuery)){
		$hr=strtoupper($pg["hrtype"]).'/';
		exec('rm -rf '.$rootDir.$hr.$pg["id"].'.'.$pg["hrtype"]);
	}
	$pgRemove=mysqli_query($con,"DELETE FROM pages WHERE ad = ".$aid);
	if(!$pgRemove){
		$ret.='*'.mysqli_errno($pgRemove);
	}
	$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Delete folder','".$pname."','".$aname."','-','-','-',".date("U",strtotime("now")).")");
	echo $ret;
?>