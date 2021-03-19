<?php
//	Process login submission
	include('../includes/inc.php');
	$query="SELECT * FROM users WHERE username = '".$_POST['u']."' AND sha256 = '".hash('sha256',$_POST['p'])."'";
	$result=mysqli_query($con,$query);
	if(mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_assoc($result)){
			$_SESSION["user"] = $row["username"];
  			echo hash('sha256',time());
  		}
  	}
?>