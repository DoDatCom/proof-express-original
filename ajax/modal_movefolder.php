<?php
	
	include('../includes/inc.php');
	$pid=$_POST["pid"];
//	Confirm presence of archive folder; create folder if it doesn't exist
	$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND type = 'Z' ORDER BY name ASC");
	if(mysqli_num_rows($fQuery)>0) {
		echo '
<div class="dropdown">
	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Select destination folder&nbsp;<span class="caret"></span></button>
	<ul class="dropdown-menu" style="padding:10px;right:30px;left:30px;">
		<li onclick="setFolderDest(\'\',\'&dz=root\',\'#000\',\'Project root\')">Project root</li>
		';
		while($f=mysqli_fetch_assoc($fQuery)){
			echo '
		<li onclick="setFolderDest('.$f["id"].',\'&dz='.$f["id"].'\',\'#000\',\''.$f["name"].'\')"><span class="glyphicons glyphicons-folder-open" style="color:#000;"></span>&nbsp;'.$f["name"].'</li>
			';
		}
		echo '
	</ul>
</div>
		';
	}
?>