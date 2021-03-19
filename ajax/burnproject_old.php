<?php
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$rootDir='/usr/local/www/apache24/data/sn.dodatcommunications.com/proofs/';
	$pid=$_POST["pid"];
	$pname=projectName($pid);
	
//	Remove project from MySQL database.
	$rQuery=mysqli_query($con,"DELETE FROM projects WHERE id = ".$pid);
	$aQuery=mysqli_query($con,"DELETE FROM ads WHERE project = ".$pid);
	$anQuery=mysqli_query($con,"DELETE FROM annotation WHERE project = ".$pid);
	$nQuery=mysqli_query($con,"DELETE FROM notes WHERE project = ".$pid);
	$uQuery=mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$pid.":%'");
	while($u=mysqli_fetch_assoc($uQuery)){
		$oldRing=$u["ring"];
		$newRing=str_replace('p'.$pid.':','',$oldRing);
		$ring=mysqli_query($con,"UPDATE users SET ring = '".$newRing."' WHERE id = ".$u["id"]);
	}
	$pgQuery=mysqli_query($con,"SELECT id,hrtype FROM pages WHERE project = ".$pid);
	while($pg=mysqli_fetch_assoc($pgQuery)){
		$hr=strtoupper($pg["hrtype"]).'/';
		exec('rm -rf '.$rootDir.$hr.$pg["id"].'.'.$pg["hrtype"]);
	}
	$pQuery=mysqli_query($con,"DELETE FROM pages WHERE project = ".$pid);
	
	echo $pname.' has been permanently deleted.';
?>