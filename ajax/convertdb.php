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
	
	send_message($serverTime, 'CONVERSION START','-','-');
	send_message($serverTime, 'Acquiring list of pages to update...','-','-');
	
	// Get page total
	
	$pgQuery=mysqli_query($con,"SELECT id FROM pages WHERE hrtype IS NULL");
	$pgTotal=mysqli_num_rows($pgQuery);
	$pQuery=mysqli_query($con,"SELECT id,name,project,ad,rev FROM pages WHERE hrtype IS NULL LIMIT 1000");
	$pTotal=mysqli_num_rows($pQuery);
	$pgInt=$pgTotal-$pTotal;
	$pInt=1;
	$etaStart = date("U",strtotime("now"));
	send_message($serverTime, 'Acquiring list of pages to update: COMPLETE','-','-');
	send_message($serverTime, 'Updating '.$pTotal.' of '.$pgTotal.' unprocessed pages.','-','-'); // Archive
	
	if(!is_dir($rootDir.'proofs')){
		mkdir($rootDir.'proofs');
	}
	if(!is_dir($rootDir.'proofs/IMG')){
		mkdir($rootDir.'proofs/IMG');
	}
	if(!is_dir($rootDir.'proofs/PDF')){
		mkdir($rootDir.'proofs/PDF');
	}
	if(!is_dir($rootDir.'proofs/JPG')){
		mkdir($rootDir.'proofs/JPG');
	}
	exec('chmod -R 0775 '.$rootDir.'proofs');
	
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
		while($p=mysqli_fetch_assoc($projQuery)){
			$pDir=str_replace($strIN,$strOUT,$p["name"].'/');
		}
		$getDir=$rootDir.'projects/'.$pDir.$dir;
		
		for($i=1;$i<20;$i++){
			$file=0;
			if($pgid["rev"]==$i){
				$now=date("U",strtotime("now"));
				if(file_exists($getDir.'pdf/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.pdf')){
					exec('cp '.$getDir.'pdf/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.pdf '.$rootDir.'proofs/PDF/'.$pgid["id"].'.pdf');
					$pdfQuery=mysqli_query($con,"UPDATE pages SET last_modify = ".$now.", hrtype = 'pdf' WHERE id = ".$pgid["id"]);
				}
				if(file_exists($getDir.'hires/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
					exec('cp '.$getDir.'hires/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg '.$rootDir.'proofs/JPG/'.$pgid["id"].'.jpg');
					$jpgQuery=mysqli_query($con,"UPDATE pages SET last_modify = ".$now.", hrtype = 'jpg' WHERE id = ".$pgid["id"]);
				}
				if(file_exists($getDir.'img/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
					exec('cp '.$getDir.'img/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg '.$rootDir.'proofs/IMG/'.$pgid["id"].'.jpg');
				}
				$pgPercent=intval(($pgInt/$pgTotal)*100);
				
				$pgEnd=$pTotal-$pInt;
				$etaEnd=(($now-$etaStart)/$pInt)*$pgEnd;
				$secs=$etaEnd % 60;
				$etaText='ETA: '.intval($etaEnd/60).' minutes, '.$secs.' seconds';
				send_message($serverTime, 'UPDATED '.$pgid["name"].', version '.$i,$pgPercent,$etaText);
			} elseif(file_exists($getDir.'img/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
				$now=date("U",strtotime("now"));
				if(file_exists($getDir.'pdf/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.pdf')){
					$newPg=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$pgid["name"]."',".$pid.",".$aid.",".$i.",'Active',".$now.",".$now.",".$now.",'','pdf')");
				}
				if(file_exists($getDir.'hires/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
					$newPg=mysqli_query($con,"INSERT INTO pages (id,name,project,ad,rev,status,created,last_access,last_modify,viewlog,hrtype) VALUES (null,'".$pgid["name"]."',".$pid.",".$aid.",".$i.",'Active',".$now.",".$now.",".$now.",'','jpg')");
				}
				$newID=mysqli_query($con,"SELECT id FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND name = '".$pgid["name"]."' AND rev = ".$i);
				while($new=mysqli_fetch_assoc($newID)){
					if(file_exists($getDir.'pdf/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.pdf')){
						exec('cp '.$getDir.'pdf/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.pdf '.$rootDir.'proofs/PDF/'.$new["id"].'.pdf');
						$pdfQuery=mysqli_query($con,"UPDATE pages SET last_modify = ".$now.", hrtype = 'pdf' WHERE id = ".$pgid["id"]);
					}
					if(file_exists($getDir.'hires/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
						exec('cp '.$getDir.'hires/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg '.$rootDir.'proofs/JPG/'.$new["id"].'.jpg');
						$jpgQuery=mysqli_query($con,"UPDATE pages SET last_modify = ".$now.", hrtype = 'jpg' WHERE id = ".$pgid["id"]);
					}
					if(file_exists($getDir.'img/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg')){
						exec('cp '.$getDir.'img/'.str_replace($strIN,$strOUT,$pgid["name"].'/'.$pgid["name"]).'_v'.$i.'.jpg '.$rootDir.'proofs/IMG/'.$new["id"].'.jpg');
					}
					$pgPercent=intval(($pgInt/$pgTotal)*100);
					$pgEnd=$pTotal-$pInt;
					$etaEnd=(($now-$etaStart)/$pInt)*$pgEnd;
					$secs=$etaEnd % 60;
					$etaText='ETA: '.intval($etaEnd/60).' minutes, '.$secs.' seconds';
					send_message($serverTime, 'ADDED '.$pgid["name"].', version '.$i,$pgPercent,$etaText);
				}
			}
		}
		$pInt++;
		$pgInt++;
	}
 
	send_message($serverTime, 'PROCESS ENDED',100,'FINISHED'); 
?>
