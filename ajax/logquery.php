<?php
	
	include('../includes/inc.php');
	$t=$_POST["t"];
	$tz=$_POST["tz"];
	
	echo '
<div class="well well-sm">
	<h3>'.$t.'</h3>
</div>
<table class="table table-striped">
	<thead style="display:block;">
		<tr style="display:table;width:100%;">
	';
	if($t=="Log In") {
		echo '
			<th style="width:50%;text-align:left;">User</th>
			<th style="width:50%;text-align:center;">Timestamp</th>
		';
	} elseif($t=="Added New User") {
		echo '
			<th style="width:30%;text-align:left;">User</th>
			<th style="width:40%;text-align:center;">New User Added</th>
			<th style="width:30%;text-align:center;">Timestamp</th>
		';
	} else {
		echo '
			<th style="width:15%;text-align:left;">User</th>
			<th style="width:20%;text-align:center;">Project</th>
			<th style="width:20%;text-align:center;">Ad</th>
			<th style="width:30%;text-align:center;">Page</th>
			<th style="width:15%;text-align:center;">Timestamp</th>
		';
	}
	echo '
		</tr>
	</thead>
	<tbody id="logTable" style="overflow-y:scroll;display:block;height:250px;">
	';
	if($t=="Log In") {
		$query=mysqli_query($con,"SELECT * FROM user_log WHERE activity = '".$t."' ORDER BY time");
		while($row = mysqli_fetch_assoc($query)){
			echo '	
		<tr style="display:table;width:100%;">
			<td style="width:50%;text-align:left;">'.$row["user"].'</td>
			<td style="width:50%;text-align:center;">'.timestamp($row["time"],$tz).'</td>
		</tr>
			';
		}
	} elseif($t=="Added New User") {
		$query=mysqli_query($con,"SELECT * FROM user_log WHERE activity = '".$t."' ORDER BY time");
		while($row = mysqli_fetch_assoc($query)){
			echo '	
		<tr style="display:table;width:100%;">
			<td style="width:30%;text-align:left;">'.$row["user"].'</td>
			<td style="width:40%;text-align:center;">'.$row["note"].'</td>
			<td style="width:30%;text-align:center;">'.timestamp($row["time"],$tz).'</td>
		</tr>
			';
		}
	} else {
		$query=mysqli_query($con,"SELECT * FROM user_log WHERE activity = '".$t."' ORDER BY time");
		while($row = mysqli_fetch_assoc($query)){
			echo '	
		<tr style="display:table;width:100%;">
			<td style="width:15%;text-align:left;">'.$row["user"].'</td>
			<td style="width:20%;text-align:center;">'.$row["project"].'</td>
			<td style="width:20%;text-align:center;">'.$row["ad"].'</td>
			<td style="width:30%;text-align:center;">'.$row["page"].'</td>
			<td style="width:15%;text-align:center;">'.timestamp($row["time"],$tz).'</td>
		</tr>
			';
		}
	}
	echo '	
	</tbody>
</table>
	';
?>


								