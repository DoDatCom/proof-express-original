<?php
//	Process new user info
	include('../includes/inc.php');
	$result=mysqli_query($con,"UPDATE projects SET name = '".$_POST["n"]."' WHERE id = ".$_POST["p"]);
	if(!$result){
		echo mysqli_errno($result);
	}
?>