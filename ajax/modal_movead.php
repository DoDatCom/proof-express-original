<?php
	
	include('../includes/inc.php');
	$pid=$_POST["pid"];
	if(isset($_POST["fid"])){
		$fid=$_POST["fid"];
		$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND type != 'A' AND id != ".$fid." AND folder = 'root' ORDER BY type DESC, name ASC");
		$folder=true;
	} else {
		$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$pid." AND type != 'A' ORDER BY type DESC, name ASC");
		$folder=false;
	}
	if(mysqli_num_rows($fQuery)>0) {
		echo '
<div class="dropdown">
	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Select destination folder&nbsp;<span class="caret"></span></button>
	<ul class="dropdown-menu" style="padding:10px;right:30px;left:30px;">
		<li onclick="setAdDest(\'\',\'&df=root\',\'#000\',\'Project root\')">Project root</li>
		';
		while($f=mysqli_fetch_assoc($fQuery)){
			if($f["type"]=="Z"){
				echo '
		<li onclick="setAdDest('.$f["id"].',\'&dz='.$f["id"].'\',\'#000\',\''.$f["name"].'\')"><span class="glyphicons glyphicons-folder-open"></span>&nbsp;'.$f["name"].'</li>
				';
				if($folder==true){
					$subQuery=mysqli_query($con,"SELECT * FROM ads WHERE folder = ".$f["id"]." AND id != ".$fid." ORDER BY name ASC");
				} else {
					$subQuery=mysqli_query($con,"SELECT * FROM ads WHERE folder = ".$f["id"]." ORDER BY name ASC");
				}
				if(mysqli_num_rows($subQuery)>0) {
					while($sub=mysqli_fetch_assoc($subQuery)){
						echo '
		<li onclick="setAdDest('.$sub["id"].',\'&dz='.$f["id"].'&df='.$sub["id"].'\',\'#DAA520\',\''.$sub["name"].'\')"><span class="halflings halflings-triangle-right"></span>&nbsp;<span class="glyphicons glyphicons-folder-open" style="color:#DAA520;"></span>&nbsp;'.$sub["name"].'</li>
						';
					}
				}
			} elseif($f["type"]=="F" && $f["folder"]=="root") {
				echo '
		<li onclick="setAdDest('.$f["id"].',\'&df='.$f["id"].'\',\'#DAA520\',\''.$f["name"].'\')"><span class="glyphicons glyphicons-folder-open" style="color:#DAA520;"></span>&nbsp;'.$f["name"].'</li>
				';
			}
		}
		echo '
	</ul>
</div>
		';
	}
?>