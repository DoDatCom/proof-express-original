<?php

//	#############################################################
//	# Name:		addnote.php										#
//	# Purpose:	AJAX script to add a general note to the 	 	#
//	#			'notes' MySQL table.							#
//	#############################################################

	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	$pid=$_POST["pid"];
	$aid=$_POST["aid"];
	
//	Convert URI encoding to text
	$pre_in=array("%E2%80%A2");
	$note=rawurldecode($_POST["n"]);
	$uri_in=array("\"","â€¢","Â¢");
	$uri_out=array("&quot;","&bull;","&cent;");
	
	$newNote=mysqli_query($con,'INSERT INTO notes (id,project,archive,wk,page,rev,notes,hl,user,created,modified) VALUES (null,'.$pid.',"-",'.$aid.',"'.$_POST["pg"].'",'.$_POST["r"].',"'.str_replace($uri_in,$uri_out,$note).'","221,221,221","'.$_POST["u"].'",'.$now.','.$now.')');
	if(!$newNote){
		die(mysqli_errno($newNote));
	}
	
	$naCount=mysqli_query($con,"SELECT comments FROM ads WHERE id = ".$aid);
	while($a=mysqli_fetch_assoc($naCount)) { $aCt=$a["comments"] + 1; }
	$newACt=mysqli_query($con,"UPDATE ads SET comments = ".$aCt." WHERE id = ".$aid);
	
	$npCount=mysqli_query($con,"SELECT comments FROM projects WHERE id = ".$pid);
	while($p=mysqli_fetch_assoc($npCount)) { $pCt=$p["comments"] + 1; }
	$newPCt=mysqli_query($con,"UPDATE projects SET comments = ".$pCt." WHERE id = ".$pid);
	
	$newLog=mysqli_query($con,'INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,"'.$_POST["u"].'","Note","'.$_POST["pname"].'","'.$_POST["aname"].'","'.$_POST["pg"].'",'.$_POST["r"].',"'.str_replace($uri_in,$uri_out,$note).'",'.$now.')');
	if(!$newLog){
		die(mysqli_errno($newLog));
	} else {
		echo 'OK';
	}
?>