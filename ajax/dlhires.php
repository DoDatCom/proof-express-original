<?php
	include('../includes/inc.php');
	
	if(!is_dir(ABSDIR.'proofs/DL')){
		mkdir(ABSDIR.'proofs/DL');
	}
	
	$dlQuery=mysqli_query($con,"SELECT name,rev,hrtype FROM pages WHERE id = ".$_POST["pg"]);
	while($row = mysqli_fetch_assoc($dlQuery)) {
		$filename = $row["name"].'_v'.$row["rev"].'.'.$row["hrtype"];
		if(file_exists(ABSDIR.'proofs/DL/'.$filename)){
			exec('rm -rf '.ABSDIR.'proofs/DL/'.$filename);
		}
		exec('cp '.ABSDIR.'proofs/'.strtoupper($row["hrtype"]).'/'.$_POST["pg"].'.'.$row["hrtype"].' '.ABSDIR.'proofs/DL/'.$filename);
		echo WEB.'/proofs/DL/'.$filename;
	}
?>