<?php
//	Process new user info
	include('../includes/inc.php');
	$result=mysqli_query($con,"UPDATE users SET ring = CONCAT(ring, '".$_POST["p"]."') WHERE id = ".$_POST["u"]);
	if(!$result){
		echo mysqli_errno($result);
	}
?>