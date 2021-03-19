<?php
	include('../includes/inc.php');
//	Process GET data.
	$pg=$_POST["pg"];
	$url=$_POST["url"];
	$now=date("U",strtotime("now"));
	$strIN=array(' ','#');
	$strOUT=array('_','');
	
	$pgQuery=mysqli_query($con,"SELECT * FROM pages WHERE id = ".$pg);
	While($pgid=mysqli_fetch_assoc($pgQuery)){
		$aQuery=mysqli_query($con,"SELECT id FROM annotation WHERE page = '".$pgid["name"]."' AND project = ".$pgid["project"]." AND wk = ".$pgid["ad"]." AND rev = ".$pgid["rev"]);
		while($a=mysqli_fetch_assoc($aQuery)) {
			$aDel=mysqli_query($con,"DELETE FROM annotation WHERE id = ".$a['id']);
		}
		$nQuery=mysqli_query($con,"SELECT id FROM notes WHERE page = '".$pgid["name"]."' AND project = ".$pgid["project"]." AND wk = ".$pgid["ad"]." AND rev = ".$pgid["rev"]);
		while($n=mysqli_fetch_assoc($nQuery)) {
			$nDel=mysqli_query($con,"DELETE FROM notes WHERE id = ".$n['id']);
		}
		$pDel=mysqli_query($con,"DELETE FROM pages WHERE id = ".$pg);
		$pgCtQuery=mysqli_query($con,"SELECT pages FROM ads WHERE id = ".$pgid["ad"]);
		while($pgCt=mysqli_fetch_assoc($pgCtQuery)){
			$newCt=$pgCt["pages"] - 1;
			$newPgCt=mysqli_query($con,"UPDATE ads SET pages = ".$newCt." WHERE id = ".$pgid["ad"]);
		}
		if(file_exists(ABSDIR.'proofs/PDF/'.$pg.'.pdf')){ exec('rm -rf '.ABSDIR.'proofs/PDF/'.$pg.'.pdf'); }
		if(file_exists(ABSDIR.'proofs/JPG/'.$pg.'.jpg')){ exec('rm -rf '.ABSDIR.'proofs/JPG/'.$pg.'.jpg'); }
		if(file_exists(ABSDIR.'proofs/IMG/'.$pg.'.jpg')){ exec('rm -rf '.ABSDIR.'proofs/IMG/'.$pg.'.jpg'); }
		$userLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Deleted page',".$pgid["project"].",".$pgid["ad"].",".$pgid["name"].",".$pgid["rev"].",'-',".date("U",strtotime("now")).")");
	}

	echo $url;
?>