<?php
	include('../includes/inc.php');
	$pid=$_POST["pid"];
	$aname=$_POST["aname"];
	if(isset($_POST["fid"])) {
		$fid=$_POST["fid"];
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND folder = '".$fid."' AND name = '".$aname."'");
	} else {
		$fid='root';
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND type = 'A' AND name = '".$aname."'");
	}
	if(mysqli_num_rows($aQuery)>0){
		echo 'ER:The ad name "'.$aname.'" is already in use.';
	} else {
		$now=date("U",strtotime("now"));
		$dueDate=date("Y/m/d",strtotime("now"));
		$newQ=mysqli_query($con,"INSERT INTO ads (id,name,project,comments,approved,pages,due_date,status,type,folder,created,last_activity) VALUES (null,'".$aname."',".$pid.",0,0,0,TIMESTAMPADD(WEEK,2,'".$dueDate."'),'New','A','".$fid."',".$now.",".$now.")");
		$adQ=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND folder = '".$fid."' AND name = '".$aname."'");
		while($newAd=mysqli_fetch_assoc($adQ)){
			echo 'OK:'.$newAd["id"];
		}
	}
?>