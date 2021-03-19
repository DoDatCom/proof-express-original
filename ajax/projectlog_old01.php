<?php
	include('../includes/inc.php');
	$p=$_POST["p"];
	if($_POST["w"] !==0) {$w=$_POST["w"];}
	$a=$_POST["a"];
	$tz=$_POST["tz"];
	
//	Retrieve project names from project id.
	$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$p);
	while($row=mysqli_fetch_assoc($pQuery)){
		$project=$row["name"];
	}
	
//	Retrieve ad names from ad id.
	if($_POST["w"] !==0){
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$w);
		while($row=mysqli_fetch_assoc($aQuery)){
			$ad=$row["name"];
		}
	}	
	
	echo '
<table style="width:100%;">
	<thead>
		<tr>
			<th style="text-align:left;">User</th>
	';
	if($a!=='ZIP Downloads'){
		echo '
			<th style="text-align:left;">Page</th>
		';
	}
	if(preg_match('/Up/i',$a) || $a=="Add Page"){
		echo '
			<th style="text-align:left;">Rev</th>
		';
	}
	echo'
			<th style="text-align:left;">Timestamp</th>
		</tr>
	</thead>
	<tbody>
	';
	if($_POST["w"] !==0) {
		$logQuery=mysqli_query($con,"SELECT * FROM user_log WHERE project = '".$project."' AND ad = '".$ad."' AND activity = '".$a."' ORDER BY time");
	} else {
		$logQuery=mysqli_query($con,"SELECT * FROM user_log WHERE project = '".$project."' AND activity = '".$a."' ORDER BY time");
	}
	while($log=mysqli_fetch_assoc($logQuery)){
		echo '
		<tr>
        	<td style="text-align:left;"><small>'.$log["user"].'</small></td>
        ';
        if($a!=='ZIP Downloads'){
			echo '
			<td style="text-align:left;"><small>'.$log["page"].'</small></td>
			';
		}
		if(preg_match('/Up/i',$a) || $a=="Add Page"){
			echo '
			<td style="text-align:left;"><small>'.$log["rev"].'</small></td>
			';
		}
		echo '
			<td style="text-align:left;"><small>'.logstamp($log["time"],$tz).'</small></td>
		</tr>';
		if($a!=='ZIP Downloads'){
			echo '
		<tr>
			<td colspan="3" style="border-bottom:1px solid gray;text-align:left;">'.$log["note"].'</td>
		</tr>
			';
		}
	}
	echo '
	</tbody>
</table>
	';
/*	function timestamp($t,$z) {
		date_default_timezone_set($z);
		$localtz = strftime("%m/%d/%Y %l:%M%p %Z",strtotime("@".$t));
		return $localtz;
	}*/
?>