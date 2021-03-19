<?php
//	Process new user info
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$dir=WEB.'projects/';
	$dirIN=array(' ','#');
	$dirOUT=array('_','');
	
	if(isset($_POST["p"])){
		$pid=$_POST["p"];
		$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$pid);
		while($p=mysqli_fetch_assoc($pQuery)){
			$pName=$p["name"];
		}
		$pDir=str_replace($dirIN,$dirOUT,$pName);
		$dir.=$pDir.'/';
	}
	if(isset($_POST["z"])){
		$zid=$_POST["z"];
		$zQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$zid);
		while($z=mysqli_fetch_assoc($zQuery)){
			$zName=$z["name"];
		}
		$zDir=str_replace($dirIN,$dirOUT,$zName);
		$rName=$zName;
		$rDir=$dir;
		$rid=$zid;
		$dir.=$zDir.'/';
	}
	if(isset($_POST["f"])){
		$fid=$_POST["f"];
		$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$fid);
		while($f=mysqli_fetch_assoc($fQuery)){
			$fName=$f["name"];
		}
		$fDir=str_replace($dirIN,$dirOUT,$fName);
		$rName=$fName;
		$rDir=$dir;
		$rid=$fid;
		$dir.=$fDir.'/';
	}
	if(isset($_POST["a"])){
		$aid=$_POST["a"];
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$aid);
		while($a=mysqli_fetch_assoc($aQuery)){
			$aName=$a["name"];
		}
		$aDir=str_replace($dirIN,$dirOUT,$aName);
		$rName=$aName;
		$rDir=$dir;
		$rid=$aid;
		$dir.=$aDir.'/';
	}
	if(file_exists($dir)) {
		exec('mv '.$dir.' '.$rDir.str_replace($dirIN,$dirOUT,$_POST["n"]));
	}
	$result=mysqli_query($con,"UPDATE ads SET name = '".$_POST["n"]."', last_activity = ".$now." WHERE id = ".$rid);
	if(!$result){
		echo mysqli_errno($result);
	}
	$logUpdate=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Rename ".$_POST["t"]."','".$pName."','".$rName."','-','-','-',".$now.")");

	echo 'mv '.$dir.' '.$rDir.str_replace($dirIN,$dirOUT,$_POST["n"]);
?>