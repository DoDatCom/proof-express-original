<?php

//	#############################################################
//	# Name:		autoarchive.php									#
//	# Purpose:	AJAX script to move ads to an archive folder 	#
//	#			automatically based on the ad date.				#
//	#############################################################

	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$rootDir=ABSDIR.'projects/';
	$strIN=array(' ','#');
	$strOUT=array('_','');
	$debug='';

//	#############################################################
//	# Compile an array of folder IDs to exclude from search.	#
//	# These should be files that are already in an archive.		#
//	#############################################################
//	Acquire list of archive folders.
	$zQuery=mysqli_query($con,"SELECT * FROM ads WHERE type = 'Z'");
	if(mysqli_num_rows($zQuery)>0){
		$zList=array();
		while($z=mysqli_fetch_assoc($zQuery)){
			$zList[]=$z["id"];
		}
//		Acquire list of folders within archive folders.
		foreach($zList as $z){
			$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE type = 'F' AND folder = ".$z);
			if(mysqli_num_rows($fQuery)>0){
				$fList=array();
				while($f=mysqli_fetch_assoc($fQuery)){
					$fList[]=$f["id"];
				}
			}
		}	
	}
	
//	Acquire list of ads with expiring ad dates.
	$adQuery=mysqli_query($con,"SELECT * FROM ads WHERE due_date < CURRENT_TIMESTAMP");
	
	if(mysqli_num_rows($adQuery)>0){
		while($ad=mysqli_fetch_assoc($adQuery)){
			if(!in_array($ad["folder"],$zList) && !in_array($ad["folder"],$fList) && $ad["type"]=="A"){	// Process only ads that are not already in an archive.
//				Scan directory for an existing archive folder.
				$pDir=$rootDir.str_replace($strIN,$strOUT,projectName($ad['project']));
				$dir=$pDir;
				$arcdir = array_diff(scandir($pDir), array('.','..','.DS_Store','.TemporaryItems'));
				if(!in_array("Archive_".date("Y"),$arcdir)){
//					Create a new archive folder if one doesn't already exist.
					exec("mkdir ".$pDir."/Archive_".date("Y"));
					exec("chown -R www:wheel ".$pDir."/Archive_".date("Y"));
					exec("chmod -R 0775 ".$pDir."/Archive_".date("Y"));
//					Add new archive folder info to the MySQL database.
					$zInsert=mysqli_query($con,"INSERT INTO ads (id,name,project,rev,due_date,status,type,folder,archive,created,last_activity) VALUES (null,'Archive ".date("Y")."',".$ad["project"].",1,NOW(),'Active','Z','root',null,".$now.",".$now.")") or die(mysqli_errno($zInsert));
				}
//				Get the ID for the project's archive folder.
				$zIDQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$ad["project"]." AND name = 'Archive ".date("Y")."'");
				if(mysqli_num_rows($zIDQuery)==1){
					$zid=$zIDQuery["id"];
				}
				$zDir=$pDir.'/Archive_'.date("Y");
				
//				Determine if the ad is within a folder.
				if($ad["folder"]!=='root'){
					$fid=$ad["folder"];
					$zDir.='/'.str_replace($strIN,$strOUT,folderName($ad['folder']));
					$dir.='/'.str_replace($strIN,$strOUT,folderName($ad['folder']));
					exec("mkdir ".$pDir."/Archive_".date("Y"));
					exec("chown -R www:wheel ".$pDir."/Archive_".date("Y"));
					exec("chmod -R 0775 ".$pDir."/Archive_".date("Y"));
				}
//				Get folder info for ad.
				
				if($ad["folder"]!=='root'){
					$fid=$ad["folder"];
					$zDir.='/'.str_replace($strIN,$strOUT,folderName($ad['folder']));
					$dir.='/'.str_replace($strIN,$strOUT,folderName($ad['folder']));
				}
				$zDir.='/'.str_replace($strIN,$strOUT,adName($ad['id']));
				$dir.='/'.str_replace($strIN,$strOUT,adName($ad['id']));
			
//				Move ad / folder to archive.
				if($ad["type"]=='Z'){
					$debug.='<h3>'.$dir.' '.$ad["type"].'</h3><br/>';
				} else {
					$debug.=$dir.' '.$ad["folder"].'<br/>';
				}
				if($ad["folder"]!=='root'){
					$zUpdate="UPDATE ads SET folder = '".$sqlFolder."', archive = ".$sqlArch.", last_activity = ".$now." WHERE id = ".$said;
				exec("mv ".$dir." ".$zDir);
				
					$sql="UPDATE ads SET folder = '".$sqlFolder."', archive = ".$sqlArch.", last_activity = ".$now." WHERE id = ".$said;
			}
			break;	// Sample for debugging.
		}
	}
	echo $debug;
	print_r($zList);
	return;	
		
//	Set project variables according to ID numbers
	$pid=$_POST["pid"];
	$pName=projectName($pid);
	$pDir=str_replace($strIN,$strOUT,$pName);
	
	$said=$_POST["sa"];
	$saQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$said);
	while($sa=mysqli_fetch_assoc($saQuery)){
		$saName=$sa["name"];
		$saDir=str_replace($strIN,$strOUT,$saName);
		if($sa["folder"]=='root') {
			$srcDir=$rDir.$pDir.'/'.$saDir.'/';
		} else {
			$sfQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$sa["folder"]);
			while($sf=mysqli_fetch_assoc($sfQuery)){
				$sfName=$sf["name"];
				$sfDir=str_replace($strIN,$strOUT,$sfName);
				if($sf["archive"]!==null){
					$szQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$sf["archive"]);
					while($sz=mysqli_fetch_assoc($szQuery)){
						$szName=$sz["name"];
						$szDir=str_replace($strIN,$strOUT,$szName);
						$srcDir=$rDir.$pDir.'/'.$szDir.'/'.$sfDir.'/'.$saDir.'/';
					}
				} else {
					$srcDir=$rDir.$pDir.'/'.$sfDir.'/'.$saDir.'/';
				}
			}
		}
	}
	$sqlFolder='root';
	$sqlArch='null';
	$destination=$pName;
	$desDir=$rDir.$pDir.'/';
	if(isset($_POST["dz"])){
		$dzid=$_POST["dz"];
		$dzQuery=mysqli_query($con,"SELECT * FROM ads where id = ".$dzid);
		while($dz=mysqli_fetch_assoc($dzQuery)){
			$dzName=$dz["name"];
			$dzDir=str_replace(' ','_',$dzName);
			$sqlFolder=$dz["id"];
			$destination=$dzName;
		}
		$desDir.=$dzDir.'/';
	}
	if(isset($_POST["df"])){
		$dfid=$_POST["df"];
		$dfQuery=mysqli_query($con,"SELECT * FROM ads where id = ".$dfid);
		while($df=mysqli_fetch_assoc($dfQuery)){
			$dfName=$df["name"];
			$dfDir=str_replace(' ','_',$dfName);
			if(isset($_POST["dz"])){
				$sqlArch=$sqlFolder;
			}
			$sqlFolder=$df["id"];
			$destination=$dfName;
		}
		$desDir.=$dfDir.'/';
	}
	$desDir.=$saDir.'/';
	
	$sql="UPDATE ads SET folder = '".$sqlFolder."', archive = ".$sqlArch.", last_activity = ".$now." WHERE id = ".$said;			
	
//	Check if destination directory exists; create it if it doesn't
	if(!file_exists($desDir)) {
		exec('mkdir '.$desDir);
	}
	exec('chmod -R 0775 '.$desDir);
	if(file_exists($srcDir)) {
		exec('mv -f '.$srcDir.'/* '.$desDir);
		exec('rm -rf '.$srcDir);
	}
	$result=mysqli_query($con,$sql);
	if(!$result){
		echo mysqli_errno($result);
	} else {
		$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$u."','Move folder','".$pName."','".$saName."','-','-','Moved to ".$destination."',".$now.")");
		echo $saName.' has been moved to the '.$destination.' folder successfully.';
	}


//echo 'DEBUG:  "'.$sql.'"   "mkdir '.$desDir.'"   "mv -f '.$srcDir.'* '.$desDir.'"   "rm -rf '.$srcDir;
?>