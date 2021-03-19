<?php
//	Generate user detail table
	include('../includes/inc.php');
	$query="SELECT * FROM users WHERE id = '".$_POST['u']."'";
	$result=mysqli_query($con,$query);
	if(mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_assoc($result)){
  			echo '<div class="well">
  				<div class="row">
					<div class="col-sm-11">
						<form class="form-horizontal">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label col-sm-4" for="fullname">Full Name:</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" id="fullname" value="'.$row["fullname"].'" oninput="btnSaveEn('.$_POST["u"].')">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="role">Role:</label>
										<div class="col-sm-8">
											<select class="form-control" id="role" onchange="btnSaveEn('.$_POST["u"].')">
												<option value="alpha"';
			if($row["bnw"]==hash('md5','alpha')){echo ' selected';}
			echo '									>Administrator</option>
												<option value="beta"';
			if($row["bnw"]==hash('md5','beta')){echo ' selected';}
			echo '									>Marketing</option>
												<option value="gamma"';
			if($row["bnw"]==hash('md5','gamma')){echo ' selected';}
			echo '									>Designer</option>
												<option value="delta"';
			if($row["bnw"]==hash('md5','delta')){echo ' selected';}
			echo '									>View / Comment / Approve</option>
												<option value="epsilon"';
			if($row["bnw"]==hash('md5','epsilon')){echo ' selected';}
			echo '									>View / Comment</option>
												<option value="zeta"';
			if($row["bnw"]==hash('md5','zeta')){echo ' selected';}
			echo '									>View / Download Only</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="proj">Projects:';
			if($row["bnw"]!==hash('md5','alpha') && $row["bnw"]!==hash('md5','gamma')) {
				echo '					<button id="btnAddProj" type="button" class="btn btn-info" data-toggle="modal" data-target="#listProjects">Add Project</button>';
			}
			echo '						</label>
										<div class="col-sm-8">
											<div class="well well-sm" style="max-height:150px;min-height:34px;overflow-y:auto;background-color:white;">
												<table id="proj" style="width:100%;">
													<tbody>';
			if($row["ring"]=="admin"){
				echo '									<tr><td style="text-align:left;">All access</td><tr>';
			} else {
				$addProjTaken=$row["ring"];
				$grpList = explode(":",$row["ring"]);
				foreach($grpList as $group) {
					$memQuery=mysqli_query($con,"SELECT * FROM projects WHERE id ='".str_replace('p','',$group)."'");
					while($mem = mysqli_fetch_assoc($memQuery)){
						echo '							<tr>
															<td style="text-align:left;"><input id="proj'.str_replace(':','',$group).'" type="hidden" name="project" value="'.$mem["name"].'">'.$mem["name"].'</td>
															<td style="float:right;"><span class="glyphicon glyphicon-remove" style="color:red;" onclick="removeUserFromGroup('.$_POST["u"].','.$mem["id"].')"></span></td>
														<tr>';
					}
				}
			}
			echo '									</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group">
										<label class="control-label col-sm-4" for="phone">Phone:</label>
										<div class="col-sm-8">
											<input type="tel" class="form-control" id="phone" value="'.$row["phone"].'" oninput="btnSaveEn('.$_POST["u"].')">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="email">Email:</label>
										<div class="col-sm-8">
											<input type="email" class="form-control" id="email" value="'.$row["email"].'" oninput="btnSaveEn('.$_POST["u"].')">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-8" for="email">Notifications? (0=no/1=yes):</label>
										<div class="col-sm-4">
											<input type="text" class="form-control" id="notify" value="'.$row["notify"].'" oninput="btnSaveEn('.$_POST["u"].')">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="tz">Timezone:</label>
										<div class="col-sm-8">
											<select class="form-control" id="tz" oninput="btnSaveEn('.$_POST["u"].')">
												<option';
			if($row["tzone"]=='America/New_York'){echo ' selected';}
			echo'									>Eastern</option>
												<option';
			if($row["tzone"]=='America/Chicago'){echo ' selected';}
			echo'									>Central</option>
												<option';
			if($row["tzone"]=='America/Denver'){echo ' selected';}
			echo'									>Mountain</option>
												<option';
			if($row["tzone"]=='America/Los_Angeles'){echo ' selected';}
			echo'									>Pacific</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="username">Username:</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" id="username" value="'.$row["username"].'" oninput="btnSaveEn('.$_POST["u"].')"> 
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="chgPass"></label>
										<div class="col-sm-8">
											<button id="btnChgPass" type="button" class="btn btn-info" data-toggle="modal" data-target="#chgPWModal">Change Password</button>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10">
									<button id="btnSave" type="button" class="btn btn-info disabled" onclick="btnSaveEdit('.$_POST["u"].')">Save Changes</button>
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-1">
						<button class="close" onclick="closeUserDetail()">&times;</button>
					</div>
				</div>
			</div>
			<div id="listProjects" class="modal fade" role="dialog">
				<div style="height:150px;"></div>
				<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Add Project</h4>
					</div>
					<div class="modal-body" style="max-height:280px;min-height:34px;overflow-y:auto;background-color:white;">
						<table id="group" style="width:100%;">
							<tbody>';
			$projList=mysqli_query($con,"SELECT * FROM projects ORDER BY name");
			while($proj = mysqli_fetch_assoc($projList)){
				if(!preg_match("/p".$proj["id"].":/",$addProjTaken)) {
					echo '		<tr>
									<td style="text-align:left;">
										<div class="checkbox"><label><input type="checkbox" name="addProject" value="p'.$proj["id"].':">'.$proj["name"].'</label>
									</td>
								<tr>';
					}
				}
			echo '			</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<input id="search" type="text" class="form-control" placeholder="Quick search" onkeyup="qSearch()">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-success" data-dismiss="modal" onclick="addProj('.$_POST["u"].')">Add</button>
					</div>
				</div>
				</div>
			</div>
			<div id="chgPWModal" class="modal fade" role="dialog">
				<div style="height:150px;"></div>
				<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Change Password</h4>
					</div>
					<div class="modal-body" style="max-height:280px;min-height:34px;overflow-y:auto;background-color:white;">
						<div class="form-group">
							<label class="control-label col-sm-4" for="chgPass">Password:</label>
							<div class="col-sm-8">
								<input type="password" class="form-control" id="chgPass">
							</div>
						</div>
						<br/>
						<div class="form-group">
							<label class="control-label col-sm-4" for="chgConf">Confirm:</label>
							<div class="col-sm-8">
								<input type="password" class="form-control" id="chgConf" onkeyup="checkMatch(\'chgPass\',\'chgConf\')">
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-success" data-dismiss="modal" onclick="chgPW('.$_POST["u"].')">Change</button>
					</div>
				</div>
				</div>
			</div>';
  		}
  	}
?>