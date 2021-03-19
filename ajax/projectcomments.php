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
	
// Collect info on all projects
$pQuery=mysqli_query($con,"SELECT * FROM projects");
$pCount=mysqli_num_rows($pQuery);
$pInt=1;
 
while($pid=mysqli_fetch_assoc($pQuery)){
    $cQuery=mysqli_query($con,"SELECT SUM(comments) AS total FROM ads WHERE project = ".$pid["id"]." AND status = 'Active'");
    $c=mysqli_fetch_assoc($cQuery);
    $pProg=intval(($pInt/$pCount)*100);
    $update=mysqli_query($con,"UPDATE projects SET comments = ".$c["total"]." WHERE id = ".$pid["id"]);
    send_message($serverTime,$pid["name"].' - '.$c["total"],$pProg);
    $pInt++;
}
 
send_message($serverTime, 'PROCESS ENDED','100'); 
?>