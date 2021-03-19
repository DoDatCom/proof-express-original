<?php
//	Process login submission
	include('../includes/inc.php');
	$strIN=array("Eastern","Central","Mountain","Pacific");
	$strOUT=array("America/New_York","America/Chicago","America/Denver","America/Los_Angeles");
	
	$result=mysqli_query($con,"SELECT * FROM users WHERE id = '".$_POST['id']."'");
	if(mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_assoc($result)){
			if(hash('sha256',$_POST['opw'])==$row["sha256"]) {
				if($_POST["npw"]=="nil") {
					$update=mysqli_query($con,"UPDATE users SET tzone = '".str_replace($strIN,$strOUT,$_POST["tz"])."', notify = ".$_POST["n"]." WHERE id = ".$row["id"]) or die('Error: '.mysqli_errno($update));
  				} else {
  					$update=mysqli_query($con,"UPDATE users SET sha256 = '".hash('sha256',$_POST['npw'])."',  tzone = '".str_replace($strIN,$strOUT,$_POST["tz"])."', notify = ".$_POST["n"]." WHERE id = '".$row["id"]."'") or die('Error: '.mysqli_errno($update));
  					setcookie("token", hash('sha256',$_POST['npw']), time()-1800);
  					if($update){
  						echo '<p style="color:green;"><b>Preferences updated</b></p>';
  					}
  				}
  			} elseif($_POST["opw"]=='nil' && $_POST["npw"]=='nil') {
  				$update=mysqli_query($con,"UPDATE users SET tzone = '".str_replace($strIN,$strOUT,$_POST["tz"])."', notify = ".$_POST["n"]." WHERE id = ".$row["id"]) or die('Error: '.mysqli_errno($update));
  				if($update){
  					echo '<p style="color:green;"><b>Preferences updated</b></p>';
  				}
  			} else {
  				echo 'Current password not found';
  			}
  		}
  	}
?>