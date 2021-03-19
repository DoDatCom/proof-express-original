<?php
	include('../includes/inc.php');
	$now = date("U",strtotime("now"));
	$rootDir = '/var/www/html/data/proofs/';
	$pid = $_POST["pid"];
	$pname = projectName($pid);
	
	//	Remove project from MySQL database.
	$rQuery = mysqli_query($con,"DELETE FROM projects WHERE id = ".$pid);

	//	Remove all ads associated with project from MySQL database.
	$aQuery = mysqli_query($con,"DELETE FROM ads WHERE project = ".$pid);

	//	Remove all annotations associated with project from MySQL database.
	$anQuery = mysqli_query($con,"DELETE FROM annotation WHERE project = ".$pid);

	//	Remove all notes associated with project from MySQL database.
	$nQuery = mysqli_query($con,"DELETE FROM notes WHERE project = ".$pid);

	//	Remove keyring data associated with project from users in MySQL database.
	$uQuery = mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$pid.":%'");
	while ($u = mysqli_fetch_assoc($uQuery)) {
		$oldRing = $u["ring"];
		$newRing = str_replace('p'.$pid.':','',$oldRing);
		$ring = mysqli_query($con,"UPDATE users SET ring = '".$newRing."' WHERE id = ".$u["id"]);
	}

	//	Remove all image files associated with project from proof directories.
	$pgQuery = mysqli_query($con,"SELECT id,hrtype FROM pages WHERE project = ".$pid);
	while ($pg = mysqli_fetch_assoc($pgQuery)) {
		$hr = strtoupper($pg["hrtype"]).'/';
		exec('rm -rf '.$rootDir.$hr.$pg["id"].'.'.$pg["hrtype"]);
		exec('rm -rf '.$rootDir.'IMG/'.$pg["id"].'.jpg');
	}
	
	//	Remove all image files associated with project from MySQL database.
	$pQuery = mysqli_query($con,"DELETE FROM pages WHERE project = ".$pid);
	
	echo $pname.' has been permanently deleted.';
?>
