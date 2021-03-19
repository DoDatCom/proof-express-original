<?php
//	Delete user
	include('../includes/inc.php');
	$result=mysqli_query($con,"DELETE FROM users WHERE id = ".$_POST["u"]);
	if($result){
		echo $_POST["n"].' has been removed.';
	}
?>