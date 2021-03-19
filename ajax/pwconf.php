<?php

	include('../includes/inc.php');
	$userList=mysqli_query($con,"SELECT * FROM users WHERE id = ".$_POST['id']);
	if(mysqli_num_rows($userList)>0) {
		while($row = mysqli_fetch_assoc($userList)){
			$update=mysqli_query($con,"UPDATE users SET fullname = '".$_POST["fn"]."', username = '".$_POST["un"]."', sha256 = '".hash('sha256',$_POST["p"])."' WHERE id = ".$_POST['id']) or die(mysqli_errno($update));
			if($update) {
				echo 'OK';
			}
		}
	} else {
		echo 'Error';
	} 
?>