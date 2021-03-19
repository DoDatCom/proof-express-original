<?php
/**
    EventSource is documented at 
    http://dev.w3.org/html5/eventsource/
*/
	$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
	$rootDir='/var/www/html/data/';
//	$rootDir='/usr/local/www/apache24/data/sn.dodatcommunications.com/';
	$strIN=array(' ','#');
	$strOUT=array('_','');
 
//a new content type. make sure apache does not gzip this type, else it would get buffered
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
 
/**
    Constructs the SSE data format and flushes that data to the client.
*/
	function send_message($id, $message, $progress, $eta){
		$d = array('message' => $message , 'progress' => $progress , 'eta' => $eta);
     
		echo "id: $id" . PHP_EOL;
		echo "data: " . json_encode($d) . PHP_EOL;
		echo PHP_EOL;
     
    //PUSH THE data out by all FORCE POSSIBLE
		ob_flush();
		flush();
	}
 
	$serverTime = time();
	
	send_message($serverTime, 'MISSING PAGE SEARCH START','-','-');
	send_message($serverTime, 'Acquiring list of missing pages...','-','-');
	
	// Get page total
	
	$pgQuery=mysqli_query($con,"SELECT id FROM pages WHERE hrtype IS NULL");
	$pgTotal=mysqli_num_rows($pgQuery);
	$pQuery=mysqli_query($con,"SELECT id,name,project,ad,rev FROM pages WHERE hrtype IS NULL");
	$pTotal=mysqli_num_rows($pQuery);
	$pgInt=$pgTotal-$pTotal;
	$pInt=1;
	$etaStart = date("U",strtotime("now"));
	send_message($serverTime, 'Acquiring list of missing pages: COMPLETE','-','-');
	
	while($pgid=mysqli_fetch_assoc($pQuery)){
		$pid=$pgid["project"];
		$aid=$pgid["ad"];
		$adQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$aid);
		while($a=mysqli_fetch_assoc($adQuery)){
			if($a["folder"]=="root"){
				$dir=str_replace($strIN,$strOUT,$a["name"].'/');
			} else {
				$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$a["folder"]);
				while($f=mysqli_fetch_assoc($fQuery)){
					if($f["folder"]=="root"){
						$dir=str_replace($strIN,$strOUT,$f["name"].'/'.$a["name"].'/');
					} else {
						$zQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$f["folder"]);
						while($z=mysqli_fetch_assoc($zQuery)){
							$dir=str_replace($strIN,$strOUT,$z["name"].'/'.$f["name"].'/'.$a["name"].'/');
						}
					}
				}
			}
		}
		$projQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$pid);
		if(mysqli_num_rows($projQuery)<1){
			send_message($serverTime, 'PROJECT DOES NOT EXIST (ID='.$pid.')','-','-');
		} elseif(mysqli_num_rows($adQuery)<1){
			send_message($serverTime, 'AD DOES NOT EXIST (ID='.$aid.')','-','-');
		} else {
			while($p=mysqli_fetch_assoc($projQuery)){
				$pDir=str_replace($strIN,$strOUT,$p["name"].'/');
			}
			$getDir=$rootDir.'projects/'.$pDir.$dir;
			
			for($i=1;$i<20;$i++){
				if(file_exists($getDir.'pdf/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.pdf')){
					$found=1;
				} elseif(file_exists($getDir.'hires/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
					$found=1;
				} elseif(file_exists($getDir.'img/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
					$found=1;
				} else {
					send_message($serverTime, 'MISSING: '.$pDir.$dir.'img/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg',$pgPercent,$etaText);
				}
				if($pgid["rev"]==$i){
					break;
				}
			}
			$pgPercent=intval(($pgInt/$pgTotal)*100);
			$pgEnd=$pTotal-$pInt;
			$etaEnd=(($now-$etaStart)/$pInt)*$pgEnd;
			$secs=$etaEnd % 60;
			$etaText='ETA: '.intval($etaEnd/60).' minutes, '.$secs.' seconds';
		}
		$pInt++;
		$pgInt++;
	}
 
	send_message($serverTime, 'PROCESS ENDED',100,'FINISHED'); 
?>