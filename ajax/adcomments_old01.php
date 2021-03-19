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
	
	$pQuery=mysqli_query($con,"SELECT * FROM ads");
	$pCount=mysqli_num_rows($pQuery);
	$pInt=1;
 
	$pQuery=mysqli_query($con,"SELECT * FROM projects ORDER BY name");
	while($pid=mysqli_fetch_assoc($pQuery)){
		send_message($serverTime, $pid["name"].':',intval($pInt/$pCount));
		$tCount=0;
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid["id"]." AND status = 'Active' AND type = 'A'");
		send_message($serverTime, mysqli_num_rows($aQuery).' ads found.','-');
		while($aid=mysqli_fetch_assoc($aQuery)){
			//	Process number of comments
			$nQuery=mysqli_query($con,"SELECT * FROM notes WHERE project = ".$pid["id"]." AND wk = ".$aid["id"]);
			$nCount=mysqli_num_rows($nQuery);
			$anQuery=mysqli_query($con,"SELECT * FROM annotation WHERE project = ".$pid["id"]." AND wk = ".$aid["id"]);
			$aCount=mysqli_num_rows($anQuery);
			$adCount=$nCount+$aCount;
			
			//	Process number of pages
			$pgQuery=mysqli_query($con,"SELECT DISTINCT name FROM pages WHERE project = ".$pid["id"]." AND ad = ".$aid["id"]);
			$pgCount=mysqli_num_rows($pgQuery);
			
			//	Process number of approvals
			$apQuery=mysqli_query($con,"SELECT DISTINCT name FROM pages WHERE project = ".$pid["id"]." AND ad = ".$aid["id"]." AND status = 'Approved'");
			$apCount=mysqli_num_rows($apQuery);
			
			send_message($serverTime, $aid["name"].': '.$apCount.'/'.$pgCount.' Approved - '.$adCount.' comments',intval(($pInt/$pCount)*100));
			
			$comUpdate=mysqli_query($con,"UPDATE ads SET comments = ".$adCount.", approved = ".$apCount.", pages = ".$pgCount." WHERE id = ".$aid["id"]);
			$pInt++;
		}

		
		
	}
 
	send_message($serverTime, 'PROCESS ENDED','100'); 
?>