<?php
	include('../includes/inc.php');
	$p=$_POST["p"];
	$tz=$_POST["tz"];
	$bnw=$_POST["bnw"];
	if(isset($_POST["w"]) && $_POST["w"] !==0) {$w=$_POST["w"];}
	
//	Retrieve project names from project id.
	$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$p);
	while($row=mysqli_fetch_assoc($pQuery)){
		$project=$row["name"];
	}
	
//	Retrieve ad names from ad id.
	if(isset($_POST["w"]) && $_POST["w"] !==0){
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$w);
		while($row=mysqli_fetch_assoc($aQuery)){
			$ad=$row["name"];
		}
	}
	
	echo '
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Updates <span class="badge"></span></a>
					</h4>
				</div>
				<div id="collapse4" class="panel-collapse collapse">
					<div class="panel-body">No pending updates.</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse6">Administration</a>
					</h4>
				</div>
				<div id="collapse6" class="panel-collapse collapse">
					<div class="panel-body">
						<ul class="list-group">
	';
	if($bnw=="2c1743a391305fbf367df8e4f069f9f9"){
		echo '
							<li class="list-group-item"><a href="projectsecurity.php?p='.$p.'">Edit Project Security</a></li>
							<li class="list-group-item"><a href="useradmin.php">Administer Users</a></li>
		';
	}
	echo '						
							<li class="list-group-item"><a href="preferences.php">My Preferences</a></li>
							<li class="list-group-item"><a href="#">Site Admin</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#logging">Logging</a>
					</h4>
				</div>
				<div id="logging" class="panel-collapse collapse">
					<div class="panel-body">
						 <div class="form-group">
							<label for="logType">Select Log Type:</label>';
	if(isset($_POST["w"]) && $_POST["w"] !==0){
		echo '				<select class="form-control" id="logType" onchange="showLog('.$p.','.$w.',\''.$tz.'\')" onfocusout="showLog('.$p.','.$w.',\''.$tz.'\')">';
		$logQuery=mysqli_query($con,"SELECT DISTINCT activity FROM user_log WHERE project = '".$project."' AND ad = '".$ad."' ORDER BY activity");
	} else {
		echo '				<select class="form-control" id="logType" onchange="showLog('.$p.',0,\''.$tz.'\')" onfocusout="showLog('.$p.',0,\''.$tz.'\')">';
		$logQuery=mysqli_query($con,"SELECT DISTINCT activity FROM user_log WHERE project = '".$project."' ORDER BY activity");
	}
	while($type=mysqli_fetch_assoc($logQuery)){
		echo '					<option>'.$type["activity"].'</option>';
	}
	echo '						<option disabled="disabled">-----</option>
								<option>Page Views</option>
							</select>
						</div>
						<div id="userLog" style="max-height:400px;overflow-y:auto;"></div>
					</div>
				</div>
			</div>
		</div>
	';
?>	