<?php
	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$pgid=$_POST["pg"];
	$aid=$_POST["a"];

	// Get total number of comments on approved page
	$nQuery=mysqli_query($con,"SELECT project,name,rev FROM pages WHERE id = ".$pgid);
	while($name=mysqli_fetch_assoc($nQuery)){
		$commentcount=0;
		$pgname=$name['name'];
		$pid=$name['project'];
		$rev=$name['rev'];
		// From 'notes' table
		$pgQuery=mysqli_query($con,"SELECT id FROM notes WHERE project = ".$pid." AND wk = ".$aid." AND rev = ".$rev." AND page = '".$pgname."'");
		while($nCount=mysqli_fetch_assoc($pgQuery)){
			$commentcount+=count($nCount);
		}
		// From 'annotation' table
		$anQuery=mysqli_query($con,"SELECT id FROM annotation WHERE project = ".$pid." AND wk = ".$aid." AND rev = ".$rev." AND page = '".$pgname."'");
		while($anCount=mysqli_fetch_assoc($anQuery)){
			$commentcount+=count($anCount);
		}
		if($commentcount>0){
			$prQuery=mysqli_query($con,"SELECT comments FROM projects WHERE id = ".$pid);
			while($prCurCnt=mysqli_fetch_assoc($prQuery)){
				$prCount=$prCurCnt['comments'] - $commentcount;
			}
			$prQuery=mysqli_query($con,"UPDATE projects SET comments = ".$prCount." WHERE id = ".$pid);
			$adQuery=mysqli_query($con,"SELECT comments FROM ads WHERE id = ".$aid);
			while($adCurCnt=mysqli_fetch_assoc($adQuery)){
				$adCount=$adCurCnt['comments'] - $commentcount;
			}
			$adQuery=mysqli_query($con,"UPDATE ads SET comments = ".$adCount." WHERE id = ".$aid);
		}
	}
	
	// Update status (pages)
	$nameQuery=mysqli_query($con,"SELECT name FROM pages WHERE id = ".$pgid);
	while($name=mysqli_fetch_assoc($nameQuery)){
		$pgQuery=mysqli_query($con,"UPDATE pages SET status = 'Approved', last_modify = ".$now." WHERE ad = ".$aid." AND name = '".$name["name"]."'");
	}
	// Update approval counts (ads)
	$appCount=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$aid);
	while($aCt=mysqli_fetch_assoc($appCount)){
		$newCt=$aCt["approved"] + 1;
		$aCtQuery=mysqli_query($con,"UPDATE ads SET approved = ".$newCt." WHERE id = ".$aid);
	}
	//Update log
	$logQuery=mysqli_query($con,"SELECT project,ad,name,rev FROM pages WHERE id = ".$pgid);
	while($log=mysqli_fetch_assoc($logQuery)){
		$newLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$_POST["u"]."','Approval','".projectName($log["project"])."','".adName($log["ad"])."','".$log["name"]."',".$log["rev"].",'-',".$now.")");
		echo 'OK';
	}
?>