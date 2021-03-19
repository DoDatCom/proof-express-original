<?php
/**
    EventSource is documented at 
    http://dev.w3.org/html5/eventsource/
*/
	include('../includes/inc.php');
	$strIN=array(' ','#');
	$strOUT=array('_','');
 
//a new content type. make sure apache does not gzip this type, else it would get buffered
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
 
/**
    Constructs the SSE data format and flushes that data to the client.
*/
	function send_message($id, $message, $progress){
		$d = array('message' => $message , 'progress' => $progress);
     
		echo "id: $id" . PHP_EOL;
		echo "data: " . json_encode($d) . PHP_EOL;
		echo PHP_EOL;
     
    //PUSH THE data out by all FORCE POSSIBLE
		ob_flush();
		flush();
	}
 
	$serverTime = time();
	
	// Get page total
	
	$pQuery=mysqli_query($con,"SELECT * FROM projects");
	$pCount=mysqli_num_rows($pQuery);
	$pInt=1;
 
	$pQuery=mysqli_query($con,"SELECT * FROM projects ORDER BY name");
	while($pid=mysqli_fetch_assoc($pQuery)){
		send_message($serverTime, $pid["name"].':',intval(($pInt/$pCount)*100));
		$tCount=0;
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid["id"]." AND status = 'Active' AND type = 'A'");
		while($aid=mysqli_fetch_assoc($aQuery)){
			$nQuery=mysqli_query($con,"SELECT * FROM notes WHERE project = ".$pid["id"]." AND wk = ".$aid["id"]);
			$nCount=mysqli_num_rows($nQuery);
			$anQuery=mysqli_query($con,"SELECT * FROM annotation WHERE project = ".$pid["id"]." AND wk = ".$aid["id"]);
			$aCount=mysqli_num_rows($anQuery);
			$adCount=$nCount+$aCount;
			send_message($serverTime, $aid["name"].': '.$nCount.' notes, '.$aCount.' annotations: Total '.$adCount.' comments',intval(($pInt/$pCount)*100));
			$tCount+=$adCount;
		}
		send_message($serverTime, $pid["name"].' total comments: '.$tCount.'<hr/>',intval(($pInt/$pCount)*100));
		$comUpdate=mysqli_query($con,"UPDATE projects SET comments = ".$tCount." WHERE id = ".$pid["id"]);
		
		$pInt++;
	}
 
	send_message($serverTime, 'PROCESS ENDED','100'); 
?>