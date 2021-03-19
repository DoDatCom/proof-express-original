<?php session_start();

	if(!isset($_COOKIE["session"])) {
		header("Location: https://sn.dodatcommunications.com");
	}
	$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");

?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body>

<?php 
	$title="title_projects.png";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="navstack" class="col-sm-3" style="overflow-y:auto;">
<?php include "includes/stack_admin_home.php"; ?>
<?php
//	print_r($_SESSION); 
?>
			</div>
			<div class="col-sm-6">
				<div style="height:20px;"></div>
				<ul class="nav nav-tabs nav-justified">
					<li class="active"><a data-toggle="tab" href="#menu1">Users</a></li>
					<li><a data-toggle="tab" href="#menu2">Groups</a></li>
					<li><a data-toggle="tab" href="#menu3">New User</a></li>
				</ul>

				<div class="tab-content">
					<div id="menu1" class="tab-pane fade in active">
						<div style="height:20px;"></div>
						<div class="row" style="height:50%;">
							<div id="userList" style="margin:0px auto;">
								<table class="table table-striped">
									<thead>
										<tr>
											<th style="width:30%;text-align:left;">User</th>
											<th style="width:50%;text-align:center;">Group Membership</th>
											<th style="width:20%;text-align:center;">Status</th>
										</tr>
									</thead>
									<tbody id="scrolltable" style="overflow-y:scroll;">
<?php 
	$query=mysqli_query($con,"SELECT * FROM users");
	while($row = mysqli_fetch_assoc($query)){
		echo '							<tr id="user'.$row["id"].'">
											<td style="width:30%;text-align:left;"><a onclick="showUsers('.$row["id"].')">'.$row["fullname"].'</a></td>
											<td style="width:50%;text-align:center;">';
		$memName=mysqli_query($con,"SELECT * FROM groups WHERE keychain LIKE '%u".$row["id"].":%'");
		$grpList = "";
		while($grpName = mysqli_fetch_assoc($memName)){
			$grpList .= $grpName["name"].'<br/>';
		}
		echo $grpList.'</td>				<td style="width:20%;text-align:center;">'.$row["status"].'</td>
										</tr>';
	}
?>
									</tbody>
								</table>
							</div>
							<div id="userDetail" class="row"></div>
						</div>
					</div>
					<div id="menu2" class="tab-pane fade">
						<div style="height:20px;"></div>
						<div class="row" style="height:50%;">
							<div id="userList" style="margin:0px auto;">
								<table class="table table-striped">
									<thead>
										<tr>
											<th style="width:60%;text-align:left;">Group</th>
											<th style="width:40%;text-align:center;">Members</th>
										</tr>
									</thead>
									<tbody id="scrolltable" style="overflow-y:scroll;">
<?php 
	$query=mysqli_query($con,"SELECT * from groups");
	while($row = mysqli_fetch_assoc($query)){
		echo '							<tr id="group'.$row["id"].'">
											<td style="width:60%;text-align:left;"><a onclick="showGroups('.$row["id"].')">'.$row["name"].'</a></td>
											<td style="width:40%;text-align:center;">';
		$usrList = '';
		$usrArray = explode(":",$row["keychain"]);
		foreach($usrArray as $usr) {
			if(substr($usr,0,1)=="u") {
				$usrName=mysqli_query($con,"SELECT * FROM users WHERE id = ".substr($usr,1,3));
				while($userName = mysqli_fetch_assoc($usrName)){
					$usrList .= $userName["fullname"].'<br/>';
				}
			}
		}
		echo $usrList.'</td></tr>';
	}
?>
									</tbody>
								</table>
							</div>
							<div id="groupDetail" class="row"></div>
						</div>
					</div>
					<div id="menu3" class="tab-pane fade">
						<div style="height:20px;"></div>
						<div class="row" style="height:50%;">
							<h2>Add New User</h2>
							<div class="well">
  								<div class="row">
									<form class="form-horizontal">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label col-sm-3" for="newfullname">Full Name:</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="newfullname">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newgroup">Groups:</label>
													<div class="col-sm-9">
														<div class="well well-sm" style="max-height:150px;min-height:34px;overflow-y:auto;background-color:white;">
															<table id="group" style="width:100%;">
																<tbody>
<?php
	$grpList=mysqli_query($con,"SELECT * FROM groups");
	while($grp = mysqli_fetch_assoc($grpList)){
		echo '														<tr>
																		<td style="text-align:left;">
																			<div class="checkbox"><label><input type="checkbox" value="g'.$grp["id"].':">'.$grp["name"].'</label>
																		</td>
																	<tr>';
	}
?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label col-sm-3" for="newphone">Phone:</label>
													<div class="col-sm-7">
														<input type="tel" class="form-control" id="newphone">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newemail">Email:</label>
													<div class="col-sm-9">
														<input type="email" class="form-control" id="newemail">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newusername">Username:</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="newusername">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newpass">Password:</label>
													<div class="col-sm-9">
														<input type="password" class="form-control" id="newpass">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newconf">Confirm:</label>
													<div class="col-sm-9">
														<input type="password" class="form-control" id="newconf">
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="submit" class="btn btn-default">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
<?php include "includes/token.php"; ?>
		<script>
			function showUsers(id){
				var xmlhttp;
				var params = 'u=' + id;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText !== "") {
							document.getElementById("userDetail").innerHTML = xmlhttp.responseText;
						} else {
							window.alert(xmlhttp.responseText);
						}
					}
				};
				xmlhttp.open("POST", "ajax/userdetail.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.send(params);
			}
			function showGroups(id){
				var xmlhttp;
				var params = 'g=' + id;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText !== "") {
							document.getElementById("groupDetail").innerHTML = xmlhttp.responseText;
						} else {
							window.alert(xmlhttp.responseText);
						}
					}
				};
				xmlhttp.open("POST", "ajax/groupdetail.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.send(params);
			}
			function newUser(){
				var xmlhttp;
				var newFN = document.getElementById("newfullname").value;
				var params = 'g=' + id;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText !== "") {
							document.getElementById("groupDetail").innerHTML = xmlhttp.responseText;
						} else {
							window.alert(xmlhttp.responseText);
						}
					}
				};
				xmlhttp.open("POST", "ajax/groupdetail.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.send(params);
			}
			function removeUserFromGroup(g,u){
				var xmlhttp;
				var params = 'g=' + g + '&u=' + u;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						window.alert(xmlhttp.responseText);
						window.location.reload(true);
					}
				};
				xmlhttp.open("POST", "ajax/remusrfromgrp.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.send(params);
			}
			function closeUserDetail() {
				document.getElementById("userDetail").innerHTML = "";
			}
			function closeGroupDetail() {
				document.getElementById("groupDetail").innerHTML = "";
			}
		</script>
	</body>
</html>