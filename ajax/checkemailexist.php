<?php
	include('../includes/inc.php');
	$result=mysqli_query($con,"SELECT * FROM users WHERE email = '".$_POST['e']."'");
	if(mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_assoc($result)){
			if(preg_match("/@/",$row["email"])) {
				echo $row["id"];
			} else {
				echo 'OK';
			}
		}
  	} else {
  		echo 'OK';
  	}
?>