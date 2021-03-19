<?php
/*
    EventSource is documented at 
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
     
    //PUSH THE data out by all FORCE POSSIBLE
		ob_flush();
		flush();
	}
 
	$serverTime = time();
	
	// Collect info on all ads
	$aQuery=mysqli_query($con,"SELECT * FROM ads");
	$aCount=mysqli_num_rows($aQuery);
    $aInt=1;
 
	while($aid=mysqli_fetch_assoc($aQuery)){
        //  Clear variables from previous query (if any)
        $pgCurrent='';
        $comment=0;
        //  Collect all pages for each ad, sorted by name then revision
		$pgQuery=mysqli_query($con,"SELECT * FROM pages WHERE ad = ".$aid["id"]." ORDER BY name ASC, rev DESC");
		while($pgid=mysqli_fetch_assoc($pgQuery)){
			//	Determine if the current page has comments
            if($pgid["name"]!==$pgCurrent){
                $nQuery=mysqli_query($con,"SELECT * FROM notes WHERE project = ".$pgid["project"]." AND wk = ".$pgid["ad"]." AND page = '".$pgid["name"]."' AND rev = ".$pgid["rev"]);
                $comment=$comment+mysqli_num_rows($nQuery);
                $anQuery=mysqli_query($con,"SELECT * FROM annotation WHERE project = ".$pgid["project"]." AND wk = ".$pgid["ad"]." AND page = '".$pgid["name"]."' AND rev = ".$pgid["rev"]);
                $comment=$comment+mysqli_num_rows($anQuery);
                $pgCurrent=$pgid["name"];
            }
        }
			
		//	Write updated comment count to ad record
        send_message($serverTime, $aid["name"].': '.$comment.' comments...',intval(($aInt/$aCount)*100));
		$comUpdate=mysqli_query($con,"UPDATE ads SET comments = ".$comment." WHERE id = ".$aid["id"]);
        $aInt++;
//        break;
    }
 
	send_message($serverTime, 'PROCESS ENDED','100'); 
?>