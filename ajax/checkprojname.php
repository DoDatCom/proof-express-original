<?php
	include('../includes/inc.php');
	$pname=$_POST["pname"];
	$now=date("U",strtotime("now"));
	$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE name = '".$pname."'");
	if(mysqli_num_rows($pQuery)>0) {
		echo 'The project named '.$pname.' already exists in our database. Please select a different name.';
	} else {
		$newQuery=mysqli_query($con,"INSERT INTO projects (id, name, active,last_activity,comments) VALUES (null,'".$pname."',1,'".$now.":".$_POST["u"]."',0)");
		echo 'OK';
	}
?>