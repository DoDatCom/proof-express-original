<?php

//	#############################################################
//	# Name:		projecttable.php								#
//	# Purpose:	AJAX script to generate the table of ads that	#
//	#			is seen in the project.php page.				#
//	#############################################################

	include('../includes/inc.php');
	$pid=$_POST["p"];
	$pname=projectName($pid);
	$tz=$_POST["tz"];
	$bnw=$_POST["bnw"];
	$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = '".$pid."' and folder = 'root' AND status = 'Active' ORDER BY type DESC, name ASC");
	echo '
<table class="table table-striped">
	<thead>
		<tr id="tabletop">
			<th style="text-align:left;">Project <button class="btn btn-info btn-xs" data-toggle="tooltip" title="New folder" type="button" onclick="openNewModal('.$pid.',\''.$pname.'\')"><span class="glyphicons glyphicons-folder-new"></span></button></th>
			<th style="text-align:center;">Due Date</th>
			<th style="text-align:center;">Proof Created</th>
			<th style="text-align:center;">Last Activity</th>
			<th style="text-align:center;">Download</th>
			<th style="text-align:center;">Folder Actions</th>
		</tr>
	</thead>
	<tbody>
	';
	if(mysqli_num_rows($aQuery)<1) {
		echo '
		<tr>
			<td colspan="8">This project is empty.</td>
		</tr>
			';
	} else {
		while($ad=mysqli_fetch_assoc($aQuery)){
			if($ad["type"]=="F"){
				echo '
		<tr>
			<td style="text-align:left;"><span class="glyphicons glyphicons-folder-open" style="color:#DAA520;"></span>&nbsp;<a href="folder.php?p='.$pid.'&f='.$ad["id"].'">'.$ad["name"].'</a></td>
			<td colspan="4"></td>
				';
				if($bnw=="2c1743a391305fbf367df8e4f069f9f9" || $bnw=="05b048d7242cb7b8b57cfa3b1d65ecea"){
					echo '
			<td>
				<div class="dropdown">
					<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Actions <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li class="dropdown-header">'.$ad["name"].'</li>
						<li><a onclick="renameFolder(\'folder\','.$ad["id"].',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Rename folder</a></li>
						<li><a onclick="moveFolderModal('.$pid.','.$ad["id"].',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Move folder</a></li>
						<li class="divider"></li> 
						<li><a onclick="deleteFolder('.$pid.','.$ad["id"].',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Delete folder</a></li>
					</ul>
				</div>
			</td>
					';
				} else {
					echo '<td></td>';
				}
				echo '</tr>';
			} elseif($ad["type"]=="Z"){
				echo '
		<tr>
			<td style="text-align:left;"><span class="glyphicons glyphicons-folder-open"></span>&nbsp;<a href="archive.php?p='.$pid.'&z='.$ad["id"].'">'.$ad["name"].'</a></td>
			<td colspan="4"></td>
				';
				if($bnw=="2c1743a391305fbf367df8e4f069f9f9" || $bnw=="05b048d7242cb7b8b57cfa3b1d65ecea"){
					echo '
			<td>
				<div class="dropdown">
					<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Actions <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li class="dropdown-header">'.$ad["name"].'</li>
						<li><a onclick="renameFolder(\'archive\','.$ad["id"].',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Rename archive</a></li>
						<li class="divider"></li> 
						<li><a onclick="deleteFolder('.$pid.','.$ad["id"].',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Delete archive</a></li>
					</ul>
				</div>
			</td>
					';
				} else {
					echo '<td></td>';
				}
				echo '</tr>';
			} elseif($ad["type"]=="A") {
				$pageCnt=$ad["pages"];
				$appCnt=$ad["approved"];
				$noteCnt=$ad["comments"];
				echo '
		<tr>
			<td style="text-align:left;"><span class="glyphicons glyphicons-map-marker" style="color:#31B44C;"></span>&nbsp;<a href="ad.php?p='.$pid.'&a='.$ad["id"].'">'.$ad["name"].'</td>
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
			<td><span class="glyphicons glyphicons-download-alt" onclick="downloadFolder('.$pid.',\'&aid='.$ad["id"].'\',\''.$_COOKIE["user"].'\',\''.$_COOKIE["bnw"].'\')" onMouseOver="this.style.cursor=\'pointer\'"></span></td>
			<td>';
				if($bnw=="2c1743a391305fbf367df8e4f069f9f9" || $bnw=="05b048d7242cb7b8b57cfa3b1d65ecea"){
					echo '
				<div class="dropdown">
					<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Actions <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li class="dropdown-header">'.$ad["name"].'</li>
						<li><a onclick="changeDueDate('.$ad["id"].',\''.$pname.'\',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Change due date</a></li>
						<li><a onclick="renameFolder(\'ad\','.$ad["id"].',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Rename ad</a></li>
						<li><a onclick="moveAdModal('.$pid.','.$ad["id"].',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Move ad</a></li>
						<li class="divider"></li> 
						<li><a onclick="deleteAd('.$pid.','.$ad["id"].',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Delete ad</a></li>
					</ul>
				</div>
					';
				}
				echo '
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
?>