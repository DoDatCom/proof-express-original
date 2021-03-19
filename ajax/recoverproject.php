<?php
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$pid=$_POST["pid"];
	$pname=projectName($pid);
	
//	Remove project from MySQL database.
	$rQuery=mysqli_query($con,"UPDATE projects SET active = 1, last_activity = '".$now.":".$_POST["u"]."' WHERE id = ".$pid);
	if(!$rQuery){
		echo mysqli_errno($rQuery);
	} else {
		echo $pname.' has been recovered.';
	}
?>