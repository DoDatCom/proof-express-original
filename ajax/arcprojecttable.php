<?php
	include('../includes/inc.php');
	$pid=$_POST["p"];
	$folder=$_POST["f"];
	$tz=$_POST["tz"];
	$nameQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = '".$pid."'") or die("This folder does not exist");
	while($row=mysqli_fetch_assoc($nameQuery)) {
		$name=$row["name"];
		$adQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = '".$pid."' AND archive = '".$folder."' ORDER BY name");
	}
	echo '
<table class="table table-striped">
	<thead>
		<tr id="tabletop">
			<th style="text-align:left;">Project</th>
			<th style="text-align:center;">Due Date</th>
			<th style="text-align:center;">Proof Created</th>
			<th style="text-align:center;">Last Activity</th>
			<th style="text-align:center;">Download</th>
			<th style="text-align:center;">Folder Actions</th>
		</tr>
	</thead>
	<tbody>
	';
	if(mysqli_num_rows($adQuery)<1) {
		echo '
		<tr>
			<td colspan="8">This archive is empty.</td>
		</tr>
			';
	} else {
		while($row=mysqli_fetch_assoc($adQuery)){
			echo '
		<tr>
			<td style="text-align:left;"><a href="arcwk.php?p='.$pid.'&f='.$folder.'&a='.$row["id"].'"><span class="glyphicon glyphicon-folder-close"></span>&nbsp;'.$row["name"].'</a></td>
			<td colspan="5"></td>
		</tr>
			';
		}
	}
	echo '
	</tbody>
</table>
	';
?>