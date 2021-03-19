<?php
	session_start();
	include('../includes/inc.php');
	$project=$_POST["p"];
	$user=$_POST["u"];
	
//	Retrieve project name	
	$name=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$project);
	if(mysqli_num_rows($name)>0) {
		while($row = mysqli_fetch_assoc($name)){
			$projectName = $row["name"];
		}
	}
	
//	Remove group data from user table
	$ring=mysqli_query($con,"SELECT * FROM users WHERE id = ".$user);
	while($oldRing = mysqli_fetch_assoc($ring)){
		$newRing = str_replace("p".$project.":","",$oldRing["ring"]);
	}
	$result=mysqli_query($con,'UPDATE users SET ring = "'.$newRing.'" WHERE id = '.$user);
  	if(!$result){
  		echo 'Error updating data.';
	}
?>