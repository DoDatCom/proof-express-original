<?php
//	Process new user info
	include('../includes/inc.php');
	$dir=ABSDIR.'projects/'.$_POST["d"].'/'.$_POST["w"];
	$strIN=array(" ",".jpg");
	$strOUT=array("_","");
	
	if(file_exists($dir.'/img/'.$_POST["r"].'/'.$_POST["p"].'.jpg')) {
		exec('chmod -R 0775 '.$dir);
		exec('rm '.$dir.'/img/'.$_POST["r"].'/'.$_POST["p"].'.jpg');
		exec('rm '.$dir.'/pdf/'.$_POST["r"].'/'.$_POST["p"].'.pdf');
		echo 'OK';
	} else {
		echo 'File not found: '.$dir.'/img/'.$_POST["p"].'.jpg';
	}
	
	
	$annote=mysqli_query($con,"SELECT * FROM annotation WHERE page = '".$_POST["p"]."' AND project = ".$_POST["pid"]." AND wk = ".$_POST["wid"]." AND rev = ".$_POST["r"]) or die(mysqli_errno($annote));
	while($row=mysqli_fetch_assoc($annote)) {
		$update=mysqli_query($con,"DELETE FROM annotation WHERE id = ".$row['id']);
	}
	$notes=mysqli_query($con,"SELECT * FROM notes WHERE page = '".$_POST["p"]."' AND project = ".$_POST["pid"]." AND wk = ".$_POST["wid"]." AND rev = ".$_POST["r"]) or die(mysqli_errno($notes));
	while($row=mysqli_fetch_assoc($notes)) {
		$update=mysqli_query($con,"DELETE FROM notes WHERE id = ".$row["id"]);
	}
	$page=mysqli_query($con,"SELECT * FROM pages WHERE name = '".$_POST["p"]."' AND project = ".$_POST["pid"]." AND ad = ".$_POST["wid"]." AND rev = ".$_POST["r"]) or die(mysqli_errno($page));
	while($row=mysqli_fetch_assoc($page)) {
		$update=mysqli_query($con,"DELETE FROM pages WHERE id = ".$row["id"]);
	}	
?>