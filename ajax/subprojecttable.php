<?php
	include('../includes/inc.php');
	$id=$_POST["p"];
	$sub=$_POST["s"];
	$tz=$_POST["tz"];
	$query=mysqli_query($con,"SELECT * FROM projects WHERE id = '".$id."'") or die("This folder does not exist");
	while($row=mysqli_fetch_assoc($query)) {
		$name=$row["name"];
		$adQuery=mysqli_query($con,"SELECT * FROM ads WHERE type = '".$sub."' AND status = 'Active' ORDER BY name");
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
			<td colspan="8">This project is empty.</td>
		</tr>
		';
	} else {
		while($ad=mysqli_fetch_assoc($adQuery)){
			if($ad["type"]=="F"){
				echo '
		<tr>
			<td style="text-align:left;"><a href="subproject.php?p='.$id.'&s='.$ad["id"].'"><span class="glyphicon glyphicon-folder-close"></span>&nbsp;'.$ad["name"].'</a></td>
			<td colspan="5"></td>
		</tr>
				';
			} else {
				$pages=mysqli_query($con,"SELECT * FROM pages WHERE project = '".$id."' AND ad = '".$ad["id"]."'");
				$apps=mysqli_query($con,"SELECT * FROM pages WHERE project = '".$id."' AND ad = '".$ad["id"]."' AND status = 'Approved'");
				$pageCnt=mysqli_num_rows($pages);
				$appCnt=mysqli_num_rows($apps);
				$noteCnt=0;
				while($pr=mysqli_fetch_assoc($pages)){
					if($pr["status"]=='Active'){
						$notes=mysqli_query($con,"SELECT id FROM annotation WHERE project = '".$id."' AND wk = '".$ad["id"]."' AND page = '".$pr["name"]."' AND rev = '".$pr["rev"]."' UNION ALL SELECT id FROM notes WHERE project = '".$id."' AND wk = '".$ad["id"]."' AND page = '".$pr["name"]."' AND rev = '".$pr["rev"]."'");
						$noteCnt+=mysqli_num_rows($notes);
					}
				}
				echo '
		<tr>
			<td style="text-align:left;"><a href="ad.php?p='.$id.'&w='.$ad["id"].'"><span class="glyphicon glyphicon-folder-close"></span>&nbsp;'.$ad["name"].'</td>
			<td>'.date("n/j/y",strtotime($ad["due_date"])).'</td>
			<td>'.timestamp($ad["created"],$tz).'</td>
				';
				if($pageCnt==$appCnt) {
					echo '
			<td style="color:green;">'.$appCnt.'/'.$pageCnt.' Approved
					';
				} else {
					echo '
			<td>'.$appCnt.'/'.$pageCnt.' Approved
					';
				}
				if($noteCnt>0){
					echo '<br>'.$noteCnt.' Comments';
				}
				echo '
			</td>
			<td><span class="glyphicons glyphicons-download-alt" onclick="downloadFolder('.$id.','.$ad["id"].',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'"></span></td>
			<td>
				<div class="dropdown">
					<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Actions <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li class="dropdown-header">'.$ad["name"].'</li>
						<li><a onclick="changeDueDate('.$ad["id"].',\''.$name.'\',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Change due date</a></li>
						<li><a onclick="renameFolder('.$ad["id"].',\''.$name.'\',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Rename folder</a></li>
						<li><a onclick="openMoveModal('.$id.','.$ad["id"].',\''.$name.'\',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Move folder</a></li>
						<li class="divider"></li> 
						<li><a onclick="deleteFolder('.$ad["id"].',\''.$name.'\',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Delete folder</a></li>
					</ul>
				</div>
			</td>
		</tr>
				';
			}
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