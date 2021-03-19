<?php
//	Process new user info
	include('../includes/inc.php');
	$users=explode(':',$_POST["u"]);
	foreach($users as $user){
		$result=mysqli_query($con,"UPDATE users SET ring = CONCAT(ring, 'p".$_POST["p"].":') WHERE id = ".$user);
	}
?>