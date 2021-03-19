<?php session_start();

	include('../includes/inc.php');
	
	if($_POST["ring"]=="-") {
		echo '
<div style="height:50px"></div>
<div class="well">
	<span class="glyphicons glyphicons-warning-sign x2"></span>
	<br/>
	<p>Oops, something went wrong!<br/><br/>Your account is currently not associated with any project. Please contact the Image Center to have you account configured.<br/><br/><b>(513) 792-6424</b></p>
</div>
		';
	} else {
		echo '
<table class="table table-striped">
	<thead style="display:block;">
		<tr>
			<th style="width:400px;text-align:left;">Customer</th>
			<th style="width:100px;text-align:center;">Alerts</th>
		</tr>
	</thead>
	<tbody id="scrolltable" style="display:block;overflow-y:auto;height:500px;">
		<tr id="top"></tr>
		';
		if($_POST["ring"]=="admin"){
			$query=mysqli_query($con,"SELECT * from projects WHERE active = 1 ORDER BY name");
		} else {
			$ring=str_replace("p","",$_POST["ring"]);
			$prList=array_map('intval',explode(":",$ring));
			$prList=implode("','",$prList);
			$query=mysqli_query($con,"SELECT * FROM projects WHERE id IN ('".$prList."') ADN active = 1 ORDER BY name");
		}
		$marker=65;
		while($row = mysqli_fetch_assoc($query)){
			$noteCnt=0;
			$adList=mysqli_query($con,"SELECT * FROM ads WHERE project = ".$row["id"]);
			while($ad=mysqli_fetch_assoc($adList)){
				$pageList=mysqli_query($con,"SELECT * FROM pages WHERE project = ".$row["id"]." AND ad = ".$ad["id"]." AND status = 'Active'");
				while($page=mysqli_fetch_assoc($pageList)){
					$noteQuery=mysqli_query($con,"SELECT id FROM annotation WHERE project = '".$row["id"]."' AND wk = '".$ad["id"]."' AND page = '".$page["name"]."' AND rev = ".$page["rev"]." UNION ALL SELECT id FROM notes WHERE project = ".$row["id"]." AND wk = ".$ad["id"]." AND page = '".$page["name"]."' AND rev = ".$page["rev"]) or die(mysqli_errno($noteQuery));
					$noteCnt+=mysqli_num_rows($noteQuery);
				}
			}
			if(substr($row["name"],0,1)>=chr($marker)){
				if($row["name"]=="Marketing") {
					echo '
		<tr id="'.substr($row["name"],0,1).'" style="background-color:#9cd07d;">
					';
				} else {
					echo '
		<tr id="'.substr($row["name"],0,1).'">
					';
				}
				echo '
			<td style="width:400px;text-align:left;"><a href="project.php?p='.$row["id"].'">'.$row["name"].'</a></td>
			<td style="width:100px;text-align:center;">'.$noteCnt.' comments</td>
		</tr>
				';
				$marker=ord($row["name"]) + 1;
			} else {
				if($row["name"]=="Marketing") {
					echo '
		<tr style="background-color:#9cd07d;">
					';
				} else {
					echo '
		<tr>
					';
				}
				echo '
			<td style="width:400px;text-align:left;"><a href="project.php?p='.$row["id"].'">'.$row["name"].'</a></td>
			<td style="width:100px;text-align:center;">'.$noteCnt.' comments</td>
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