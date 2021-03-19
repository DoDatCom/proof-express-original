<?php
//	Move ad folder to archive
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$dirIN=array(' ','#');
	$dirOUT=array('_','');
	$u=$_POST["u"];
	
//	Set project variables according to ID numbers
	$pid=$_POST["pid"];
	$pName=projectName($pid);
	
	$sfid=$_POST["sf"];
	$sfName=folderName($sfid);
	
	$dzid=$_POST["dz"];
	if($dzid=='root'){
		$destination='Project root';
	} else {
		$dzName=archiveName($dzid);
		$destination=$dzName;
	}
	
	$sql="UPDATE ads SET folder = '".$dzid."', last_activity = ".$now." WHERE id = ".$sfid;			
	
	$result=mysqli_query($con,$sql);
	if(!$result){
		echo mysqli_errno($result);
	} else {
		$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$u."','Move folder','".$pName."','".$sfName."','-','-','Moved to ".$destination."',".$now.")");
		echo $sfName.' has been moved to the '.$destination.' folder successfully.';
	}


//echo 'DEBUG:  "'.$sql.'"   "mkdir '.$desDir.'"   "mv -f '.$srcDir.'* '.$desDir.'"   "rm -rf '.$srcDir;
?>