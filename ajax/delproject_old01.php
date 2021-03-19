<?php
	include('../includes/inc.php');
	$dir=ABSDIR.'projects/';
//	Remove project folder from server.
	if(file_exists($dir.str_replace(" ","_",$_POST["n"]))) {
		exec('rm -rf '.$dir.str_replace(" ","_",$_POST["n"]).' *');
	}
//	Remove all notes associated with project from MySQL database.
	$delNote=mysqli_query($con,"DELETE FROM notes WHERE project = ".$_POST["p"]);
//	Remove all ads associated with project from MySQL database.
	$delAd=mysqli_query($con,"DELETE FROM ads WHERE project = ".$_POST["p"]);
//	Remove all annotations associated with project from MySQL database.
	$delAnnote=mysqli_query($con,"DELETE FROM annotation WHERE project = ".$_POST["p"]);
//	Remove all pages associated with project from MySQL database.
	$delPage=mysqli_query($con,"DELETE FROM pages WHERE project = ".$_POST["p"]);
//	Remove project from MySQL database.
	$delProject=mysqli_query($con,"DELETE FROM projects WHERE id = ".$_POST["p"]);
	if(!$delProject){
		echo mysqli_errno($delProject);
	} else {
		echo $_POST["n"].' has been deleted.';
	}
?>