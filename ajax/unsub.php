<?php

	include('../includes/inc.php');
	$userList=mysqli_query($con,"SELECT * FROM users WHERE id = '".$_POST['uid']."'");
	if(mysqli_num_rows($userList)>0) {
		while($row = mysqli_fetch_assoc($userList)){
			$update=mysqli_query($con,"UPDATE users SET notify = 0 WHERE id = ".$_POST["uid"]) or die(mysqli_errno($update));
			if($update) {
				echo 'OK';
			}
		}
	} else {
		echo 'Error';
	} 
?>