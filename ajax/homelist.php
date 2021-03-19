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
			$query=mysqli_query($con,"SELECT * FROM projects WHERE id IN ('".$prList."') AND active = 1 ORDER BY name");
		}
		$marker=65;
		while($row = mysqli_fetch_assoc($query)){
			if($row["name"]=="Marketing") {
				echo '
		<tr style="background-color:#9cd07d;">
				';
			} else {
				if(ord($row["name"])>$marker){
					$marker=ord($row["name"]);
					echo '
		<tr id="'.chr($marker).'">
					';
				} else {
					echo '
		<tr>
					';
				}
			}
			echo '
			<td style="width:400px;text-align:left;"><a href="project.php?p='.$row["id"].'">'.$row["name"].'</a></td>';
			if($row["comments"]==1){
				echo '
			<td style="width:100px;text-align:center;">'.$row["comments"].' comment</td>';
			} else {
				echo '
			<td style="width:100px;text-align:center;">'.$row["comments"].' comments</td>';
			}
			echo '
		</tr>
			';
		}
	}
	echo '
	</tbody>
</table>
	';
?>