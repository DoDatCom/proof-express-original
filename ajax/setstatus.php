<?php

/*  EventSource is documented at 
http://dev.w3.org/html5/eventsource/
*/

include('../includes/inc.php');
$strIN=array(' ','#');
$strOUT=array('_','');
 
//  a new content type. make sure apache does not gzip this type, else it would get buffered
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
 
/*
Constructs the SSE data format and flushes that data to the client.
*/
function send_message($id, $message, $progress){
	$d = array('message' => $message , 'progress' => $progress);
	echo "id: $id" . PHP_EOL;
	echo "data: " . json_encode($d) . PHP_EOL;
	echo PHP_EOL;
     
    //  PUSH THE data out by all FORCE POSSIBLE
    ob_flush();
    flush();
}
 
$serverTime = time();
	
// Collect info on all ads
$zQuery=mysqli_query($con,"SELECT * FROM ads WHERE type = 'Z'");
$zCount=mysqli_num_rows($zQuery);
$zInt=1;
 
while($zid=mysqli_fetch_assoc($zQuery)) {
	$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE folder = ".$zid["id"]);
	while($a=mysqli_fetch_assoc($aQuery)) {
		$zProg=intval(($zInt/$zCount)*100);
		if($a["type"]=="F") {
			$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE folder = ".$a["id"]);
			while($f=mysqli_fetch_assoc($fQuery)) {
				$update=mysqli_query($con,"UPDATE ads SET status = 'Archive' WHERE id = ".$f["id"]);
				send_message($serverTime,$f["name"],$zProg);
			}
		}
		$update=mysqli_query($con,"UPDATE ads SET status = 'Archive' WHERE id = ".$a["id"]);
		send_message($serverTime,$a["name"],$zProg);
		$zInt++;
	}
}
 
send_message($serverTime, 'PROCESS ENDED','100'); 
?>