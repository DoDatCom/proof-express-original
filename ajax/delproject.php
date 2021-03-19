<?php
	include('../includes/inc.php');
	$pid=$_POST["pid"];
	$pname=projectName($pid);
	$now=date("U",strtotime("now"));
	
//	Remove project from MySQL database.
	$rQuery=mysqli_query($con,"UPDATE projects SET active = 0, last_activity = '".$now.":".$_POST["u"]."' WHERE id = ".$pid);
	if(!$rQuery){
		echo mysqli_errno($rQuery);
	} else {
		echo $pname.' has been deactivated.';
	}
?>