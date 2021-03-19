<?php
//	Process new user info
	include('../includes/inc.php');
	
	$pgid=$_POST["pg"];
	
	$dirIN=array('#');
	$dirOUT=array('');
	$name=str_replace($dirIN,$dirOUT,$_POST["n"]);
	
	$nameQuery=mysqli_query($con,"SELECT name,project,ad FROM pages WHERE id = ".$pgid);	// Select the existing name info for the page
	while($row=mysqli_fetch_assoc($nameQuery)) {
		$nCheck=mysqli_query($con,"SELECT * FROM pages WHERE name = '".$name."' AND project = ".$row["project"]." AND ad = ".$row["ad"]);	// Check if there is a page with a name matching the new name
		if(mysqli_num_rows($nCheck)>0){
			echo 'EXISTS';
		} else {
			$aQuery=mysqli_query($con,"SELECT id FROM annotation WHERE page = '".$row["name"]."' AND project = ".$row["project"]." AND wk = ".$row["ad"]);
			while($a=mysqli_fetch_assoc($aQuery)) {
				$aUpdate=mysqli_query($con,"UPDATE annotation SET page = '".$name."' WHERE id = ".$a["id"]);
			}
			$nQuery=mysqli_query($con,"SELECT id FROM notes WHERE page = '".$row["name"]."' AND project = ".$row["project"]." AND wk = ".$row["ad"]);
			while($n=mysqli_fetch_assoc($nQuery)) {
				$nUpdate=mysqli_query($con,"UPDATE notes SET page = '".$name."' WHERE id = ".$n["id"]);
			}
			$pQuery=mysqli_query($con,"SELECT id FROM pages WHERE name = '".$row["name"]."' AND project = ".$row["project"]." AND ad = ".$row["ad"]);
			while($p=mysqli_fetch_assoc($pQuery)) {
				$pUpdate=mysqli_query($con,"UPDATE pages SET name = '".$name."' WHERE id = ".$p["id"]);
			}
			echo 'OK';
		}
	}
?>