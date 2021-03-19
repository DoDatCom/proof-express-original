<?php
//	Change password
	include('../includes/inc.php');
	$result=mysqli_query($con,"UPDATE users SET sha256 = '".hash('sha256',$_POST["p"])."' WHERE id = '".$_POST['u']."'");
	if($result) {
		echo 'Password changed.';
	}
?>