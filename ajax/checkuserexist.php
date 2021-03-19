<?php
	include('../includes/inc.php');
	$result=mysqli_query($con,"SELECT * FROM users WHERE username = '".$_POST['e']."'");
	if(mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_assoc($result)){
			echo $row["id"];
		}
  	} else {
  		echo 'OK';
  	}
?>