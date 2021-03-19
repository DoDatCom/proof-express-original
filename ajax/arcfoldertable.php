<?php
	include('../includes/inc.php');
	$pid=$_POST["p"];
	$tz=$_POST["tz"];
	$nameQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = '".$pid."'") or die("This folder does not exist");
	while($row=mysqli_fetch_assoc($nameQuery)) {
		$name=$row["name"];
		$folderQuery=mysqli_query($con,"SELECT DISTINCT archive FROM ads WHERE project = '".$pid."' and status = 'Archive' ORDER BY archive");
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
	if(mysqli_num_rows($folderQuery)<1) {
		echo '
		<tr>
			<td colspan="8">This archive is empty.</td>
		</tr>
			';
	} else {
		while($row=mysqli_fetch_assoc($folderQuery)){
			echo '
		<tr>
			<td style="text-align:left;"><a href="arcproject.php?p='.$pid.'&f='.$row["archive"].'"><span class="glyphicon glyphicon-folder-close"></span>&nbsp;'.$row["archive"].'</a></td>
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