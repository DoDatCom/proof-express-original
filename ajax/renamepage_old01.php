<?php
//	Process new user info
	include('../includes/inc.php');
	$dir=ABSDIR.'projects/'.$_POST["d"].'/'.$_POST["w"];
	$dirIN=array(' ','#');
	$dirOUT=array('_','');
	$strIN=array(" ",".jpg");
	$strOUT=array("_","");
	
	$i = 1;
	while($i <= $_POST["r"]) {
		if(file_exists($dir.'/img/'.$i.'/'.$_POST["p"].'.jpg')) {
			exec('chmod -R 0775 '.$dir);
			exec('mv '.$dir.'/img/'.$i.'/'.$_POST["p"].'.jpg '.$dir.'/img/'.$i.'/'.str_replace($strIN,$strOUT,$_POST["n"]).'.jpg');
			exec('mv '.$dir.'/pdf/'.$i.'/'.$_POST["p"].'.pdf '.$dir.'/pdf/'.$i.'/'.str_replace($strIN,$strOUT,$_POST["n"]).'.pdf');
		} else {
			echo 'File not found: '.$dir.'/img/'.$i.'/'.$_POST["p"].'.jpg';
		}
		$i++;
	}
	
	
	$annote=mysqli_query($con,"SELECT * FROM annotation WHERE page = '".$_POST["p"]."' AND project = ".$_POST["pid"]." AND wk = ".$_POST["wid"]." AND rev = ".$_POST["r"]) or die(mysqli_errno($annote));
	while($a=mysqli_fetch_assoc($annote)) {
		$aRename=mysqli_query($con,"UPDATE annotation SET page = '".str_replace($dirIN,$dirOUT,$_POST['n'])."' WHERE id = ".$a['id']) or die(mysqli_errno($aRename));;
	}
	$notes=mysqli_query($con,"SELECT * FROM notes WHERE page = '".$_POST["p"]."' AND project = ".$_POST["pid"]." AND wk = ".$_POST["wid"]." AND rev = ".$_POST["r"]) or die(mysqli_errno($notes));
	while($b=mysqli_fetch_assoc($notes)) {
		$bRename=mysqli_query($con,"UPDATE notes SET page = '".str_replace($dirIN,$dirOUT,$_POST['n'])."' WHERE id = ".$b["id"]) or die(mysqli_errno($bRename));
	}
	$page=mysqli_query($con,"SELECT * FROM pages WHERE name = '".$_POST["p"]."' AND project = ".$_POST["pid"]." AND ad = ".$_POST["wid"]." AND rev = ".$_POST["r"]) or die(mysqli_errno($page));
	while($c=mysqli_fetch_assoc($page)) {
		$cRename=mysqli_query($con,"UPDATE pages SET name = '".str_replace($dirIN,$dirOUT,$_POST['n'])."', last_modify = ".date("U",strtotime("now"))." WHERE id = ".$c["id"]) or die(mysqli_errno($cRename));;
	}
	echo 'OK';
?>