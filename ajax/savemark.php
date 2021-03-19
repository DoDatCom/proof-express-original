<?php session_start();

	include('../includes/inc.php');
	$now=date("U",strtotime("now"));

	$pgid=$_POST["pg"];
	$m=$_POST["m"];
	$hlx=$_POST["hlx"];
	$hly=$_POST["hly"];
	$hlw=$_POST["hlw"];
	$hlh=$_POST["hlh"];
	$text=$_POST["text"];
	$user=$_POST["u"];
	$hl=$_POST["hl"];
	if($hl=='undefined') { $hl='255,255,0'; }
	
//	Save annotation mark
	$pgQuery=mysqli_query($con,"SELECT * FROM pages WHERE id = ".$pgid);
	while($pg=mysqli_fetch_assoc($pgQuery)){
		$aQuery=mysqli_query($con,"SELECT * FROM annotation WHERE project = '".$pg["project"]."' AND wk = '".$pg["ad"]."' AND rev = ".$pg["rev"]." AND page = '".$pg["name"]."' AND mark = '".$m."'");
		if(mysqli_num_rows($aQuery)>0) {
			while($a = mysqli_fetch_assoc($aQuery)){
  				$id = $a["id"];
					$aUpdate=mysqli_query($con,"UPDATE annotation SET hlx = ".$hlx.", hly = ".$hly.", hlw = ".$hlw.", hlh = ".$hlh.", text = '".$text."', hl = '".$hl."', user = '".$user."', modified = ".$now." WHERE id = ".$id);
  				if($result){
  					$resp="Mark updated!";
  				} else {
  					$resp="Mark not updated!";
  				}
  			}
  		} else {
  			$aQuery=mysqli_query($con,"INSERT INTO annotation (id, project, wk, rev, page, mark, text, hl, hlx, hly, hlw, hlh, user,created,modified) VALUES (null,".$pg["project"].",'".$pg["ad"]."',".$pg["rev"].",'".$pg["name"]."',".$m.",'".$text."','".$hl."',".$hlx.",".$hly.",".$hlw.",".$hlh.",'".$user."',".$now.",".$now.")");
  			if($result){
  				$resp="Mark created!";
  			} else {
  				$resp="Mark not created!";
  			}
		}
	
		$newLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$user."','Annotations','".projectName($pg["project"])."','".adName($pg["project"])."','".$pg["name"]."',".$pg["rev"].",'".$text."',".$now.")");
		if(!$newLog) {$resp=mysqli_errno($newLog);}
	
		$naCount=mysqli_query($con,"SELECT comments FROM ads WHERE id = ".$pg["ad"]);
		while($a=mysqli_fetch_assoc($naCount)) { $aCt=$a["comments"] + 1; }
		$newACt=mysqli_query($con,"UPDATE ads SET comments = ".$aCt." WHERE id = ".$pg["ad"]);
	
		$npCount=mysqli_query($con,"SELECT comments FROM projects WHERE id = ".$pg["project"]);
		while($p=mysqli_fetch_assoc($npCount)) { $pCt=$p["comments"] + 1; }
		$newPCt=mysqli_query($con,"UPDATE projects SET comments = ".$pCt." WHERE id = ".$pg["project"]);
  	
//	Send server response (if any)
		if($resp){
			echo $resp;
		}
	}
?>