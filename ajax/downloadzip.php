<?php
	include('../includes/inc.php');
	$dirIN=array(' ','#');
	$dirOUT=array('_','');
	$dir='';
	
	if(isset($_POST['pid'])){
		$pid=$_POST['pid'];
		$pname=projectName($pid);
		$pDir=str_replace($dirIN,$dirOUT,$pname);
		$dir.=$pDir.'/';
	}
	if(isset($_POST['zid'])){
		$zid=$_POST['zid'];
		$zname=adName($zid);
		$zDir=str_replace($dirIN,$dirOUT,$zname);
		$dir.=$zDir.'/';
	}
	if(isset($_POST['fid'])){
		$fid=$_POST['fid'];
		$fname=adName($fid);
		$fDir=str_replace($dirIN,$dirOUT,$fname);
		$dir.=$fDir.'/';
	}
	if(isset($_POST['aid'])){
		$aid=$_POST['aid'];
		$aname=adName($aid);
		$aDir=str_replace($dirIN,$dirOUT,$aname);
		$dir.=$aDir.'/';
	}

	if(!is_dir(ABSDIR.'proofs/ZIP')){
		mkdir(ABSDIR.'proofs/ZIP');
	}

//	Remove existing ZIP files prior to processing
	$zipScan = array_diff(scandir(ABSDIR.'proofs/ZIP'), array('.','..','.DS_Store','.TemporaryItems'));
	foreach($zipScan as $z){
		if($z==$pDir.'-'.$aDir.'.zip'){
			exec('rm -rf '.ABSDIR.'proofs/ZIP/'.$pDir.'-'.$aDir.'.zip');
		}
	}
//	Begin ZIP file creation process
	$zip = new ZipArchive;
	$res = $zip->open(ABSDIR.'proofs/ZIP/'.$pDir.'-'.$aDir.'.zip', ZipArchive::CREATE);
	if($res === TRUE) {
//	Create array containing hires folders
		if($_POST["b"]=='2c1743a391305fbf367df8e4f069f9f9'){
			$pgQuery=mysqli_query($con,"SELECT id,hrtype,name,rev FROM pages WHERE ad = ".$aid);
			while($pg=mysqli_fetch_assoc($pgQuery)){
				$zip->addFile(ABSDIR.'proofs/'.strtoupper($pg["hrtype"]).'/'.$pg["id"].'.'.$pg["hrtype"], strtoupper($pg["hrtype"]).'/'.$pg["name"].'_v'.$pg["rev"].'.'.$pg["hrtype"]);
			}
		} else {
			$revCurrent='';
			$pgQuery=mysqli_query($con,"SELECT id,hrtype,name,rev FROM pages WHERE ad = ".$aid." ORDER BY name ASC, rev DESC");
			while($pg=mysqli_fetch_assoc($pgQuery)){
				if($pg["name"]!==$revCurrent){
					$zip->addFile(ABSDIR.'proofs/'.strtoupper($pg["hrtype"]).'/'.$pg["id"].'.'.$pg["hrtype"], strtoupper($pg["hrtype"]).'/'.$pg["name"].'_v'.$pg["rev"].'.'.$pg["hrtype"]);
					$revCurrent=$pg["name"];
				}
			}
		}

//	All files are added, so close the zip file.
    	$zip->close();
    	exec('chmod -R 0775 "'.ABSDIR.'proofs/ZIP/'.$pDir.'-'.$aDir.'.zip');
    	echo WEB.'/proofs/ZIP/'.$pDir.'-'.$aDir.'.zip';
    	$newLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','ZIP Downloads','".$pname."','".$aname."','-',0,'-',".date("U",strtotime("now")).")");
	} else {
    	echo 'EMPTY';
    }
?>