<?php
//	Move ad
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$u=$_POST["u"];
	
//	Set project variables according to ID numbers
	$pid=$_POST["pid"];
	$pName=projectName($pid);
	
	$said=$_POST["sa"];
	$aName=adName($said);
	
	if(isset($_POST["df"])){
		$dfid=$_POST["df"];
		if($dfid=='root'){
			$destination='Project root';
			$sql="UPDATE ads SET folder = 'root', last_activity = ".$now." WHERE id = ".$said;
		} else {
			$destination=folderName($dfid);
			$sql="UPDATE ads SET folder = '".$dfid."', last_activity = ".$now." WHERE id = ".$said;
		}
	} elseif(isset($_POST["dz"])){
		$dzid=$_POST["dz"];
		$destination=archiveName($dzid);
		$sql="UPDATE ads SET folder = '".$dzid."', last_activity = ".$now." WHERE id = ".$said;
	}
	$result=mysqli_query($con,$sql);
	if(!$result){
		echo mysqli_errno($result);
	} else {
		$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$u."','Move folder','".$pName."','".$aName."','-','-','Moved to ".$destination."',".$now.")");
		echo $aName.' has been moved to the '.$destination.' folder successfully.';
	}

//echo 'DEBUG:  "'.$sql.'"   "mkdir '.$desDir.'"   "mv -f '.$srcDir.'* '.$desDir.'"   "rm -rf '.$srcDir;
?>