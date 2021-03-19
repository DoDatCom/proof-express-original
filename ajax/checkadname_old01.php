<?php
	include('../includes/inc.php');
	$pid=$_POST["pid"];
	$aname=$_POST["aname"];
	if(isset($_POST["fid"])) {
		$fid=$_POST["fid"];
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND folder = '".$fid."' AND name = '".$aname."'");
	} else {
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND type = 'A' AND name = '".$aname."'");
	}
	if(mysqli_num_rows($aQuery)>0){
		echo 'The ad name "'.$aname.'" is already in use.';
	} else {
		echo 'OK';
	}
?>