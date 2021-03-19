<?php

	include('../includes/inc.php');
	$now=date("U",strtotime("now"));
	
//	Convert URI encoding to text
	$pre_in=array("%E2%80%A2");
	$note=rawurldecode($_POST["n"]);
	$uri_in=array("\"","â€¢","Â¢");
	$uri_out=array("&quot;","&bull;","&cent;");
	
	$newNote=mysqli_query($con,'INSERT INTO notes (id,project,archive,wk,page,rev,notes,hl,user,created,modified) VALUES (null,'.$_POST["p"].',"-",'.$_POST["a"].',"'.$_POST["pg"].'",'.$_POST["r"].',"'.str_replace($uri_in,$uri_out,$note).'","221,221,221","'.$_POST["u"].'",'.$now.','.$now.')');
	if(!$newNote){
		die(mysqli_errno($newNote));
	}
	$newLog=mysqli_query($con,'INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,"'.$_POST["u"].'","Note","'.$_POST["pn"].'","'.$_POST["an"].'","'.$_POST["pg"].'",'.$_POST["r"].',"'.str_replace($uri_in,$uri_out,$note).'",'.$now.')');
	if(!$newLog){
		die(mysqli_errno($newLog));
	} else {
		echo 'OK';
	}
?>