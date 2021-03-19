<?php

//	#############################################################
//	# Name:		foldertable.php									#
//	# Purpose:	AJAX script to generate the table of ads that	#
//	#			is seen in the folder.php page.					#
//	#############################################################

	include('../includes/inc.php');
	$pid=$_POST["p"];
	$pname=projectName($pid);
	$zid=$_POST["z"];
	$fid=$_POST["f"];
	$tz=$_POST["tz"];
	$bnw=$_POST["b"];
	$query=mysqli_query($con,"SELECT * FROM projects WHERE id = '".$pid."'") or die("This folder does not exist");
	while($row=mysqli_fetch_assoc($query)) {
		$name=$row["name"];
		$adQuery=mysqli_query($con,"SELECT * FROM ads WHERE project = '".$pid."' and folder = ".$fid." ORDER BY name ASC");
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
			<td colspan="8">This folder is empty.</td>
		</tr>
			';
	} else {
		while($ad=mysqli_fetch_assoc($adQuery)){
			$pageCnt=$ad["pages"];
			$appCnt=$ad["approved"];
			$noteCnt=$ad["comments"];
			$adURL='p='.$pid;
			if($zid>0) {
				$adURL.='&z='.$zid;
			}
			$adURL.='&f='.$fid;
			echo '
		<tr>
			<td style="text-align:left;"><a href="ad.php?'.$adURL.'&a='.$ad["id"].'"><span class="glyphicons glyphicons-map-marker" style="color:#31B44C;"></span>&nbsp;'.$ad["name"].'</td>
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
			';
			if($zid>0){
				echo '
			<td><span class="glyphicons glyphicons-download-alt" onclick="downloadFolder('.$pid.',\'&zid='.$zid.'&fid='.$fid.'&aid='.$ad["id"].'\',\''.$_COOKIE["user"].'\',\''.$_COOKIE["bnw"].'\')" onMouseOver="this.style.cursor=\'pointer\'"></span></td>
				';
			} else {
				echo '
			<td><span class="glyphicons glyphicons-download-alt" onclick="downloadFolder('.$pid.',\'&fid='.$fid.'&aid='.$ad["id"].'\',\''.$_COOKIE["user"].'\',\''.$_COOKIE["bnw"].'\')" onMouseOver="this.style.cursor=\'pointer\'"></span></td>
				';
			}
			echo '
			<td>';
			if($bnw=="2c1743a391305fbf367df8e4f069f9f9" || $bnw=="05b048d7242cb7b8b57cfa3b1d65ecea"){
				echo '
				<div class="dropdown">
					<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Actions <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li class="dropdown-header">'.$ad["name"].'</li>';
				if($zid==0) {
					echo '
						<li><a onclick="changeDueDate('.$ad["id"].',\''.$name.'\',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Change due date</a></li>
					';
				}
				echo '
						<li><a onclick="renameFolder(\'ad\',\'&'.$adURL.'&a='.$ad["id"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Rename ad</a></li>
						<li><a onclick="moveAdModal('.$pid.','.$fid.','.$ad["id"].',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Move ad</a></li>
						<li class="divider"></li> 
						<li><a onclick="deleteFolder('.$ad["id"].',\''.$name.'\',\''.$ad["name"].'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Delete folder</a></li>
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