<?php
//	Create new folder
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$dirIN=array(' ','#');
	$dirOUT=array('_','');
	
	if($_POST["arch"]=="true") {
		$type="Z";
	} else {
		$type="F";
	}
	$newdir=ABSDIR.'projects/'.str_replace($dirIN,$dirOUT,$_POST["pname"]).'/'.str_replace($dirIN,$dirOUT,$_POST["f"]);
	
//	Check if directory exists; create it if it doesn't
	if(!file_exists($newdir)) {
		exec('mkdir '.$newdir);
		exec('chmod -R 0775 '.$newdir);
		$newFolder=mysqli_query($con,"INSERT INTO ads (id,name,project,comments,approved,pages,due_date,status,type,folder,created,last_activity) VALUES (null,'".$_POST["f"]."',".$_POST["pid"].",0,0,0,NOW(),'Active','".$type."','root',".$now.",".$now.")");
		if(!$newFolder){
			echo "An error occurred during folder creation.";
		} else {
			$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["uid"]."','New folder','".$_POST["pname"]."','".$_POST["f"]."','-','-','-',".$now.")");
			echo 'OK';
		}
	} else {
		echo "The folder '".$_POST["f"]."' already exists in this project. Please choose a different name.";
	}
?>