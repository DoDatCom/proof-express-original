<?php
	include('../includes/inc.php');
	$pg=$_POST["pg"];
	$rev=$_POST["rev"];
	$tz=$_POST["tz"];
	
	$viewLog=array();
	$view=array();
	
//	Retrieve log field from page id.
	$vQuery=mysqli_query($con,"SELECT * FROM pages WHERE id = ".$pg);
	while($v=mysqli_fetch_assoc($vQuery)){
		$log=$v["viewlog"];
	}
	echo '
<table style="width:100%;">
	<thead>
		<tr>
			<th style="text-align:left;">User</th>
			<th style="text-align:right;">Timestamp</th>
		</tr>
	</thead>
	<tbody>
	';
	
//	Parse log field.
	$viewLog=explode(":",$log);
	foreach($viewLog as $row){
		if($row!==''){
			$view=explode("_",$row);
			if($view[2]==$rev){
				echo '
		<tr>
        	<td style="text-align:left;"><small>'.userName(str_replace("u","",$view[0])).'</small></td>
        	<td style="text-align:right;"><small>'.timestamp($view[1],$tz).'</small></td>
		</tr>
				';
			}
		}
	}
	echo '
	</tbody>
</table>
	';
?>