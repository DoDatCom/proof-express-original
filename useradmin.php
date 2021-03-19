<?php session_start();
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) {	// Cookie data missing
		header("Location: ".WEB);
	}
	if($_COOKIE["bnw"]==hash('md5','delta') || $_COOKIE["bnw"]==hash('md5','epsilon') || $_COOKIE["bnw"]==hash('md5','zeta')) {	// User rights set to four, five or six
		header("Location: ".WEB."/home.php");
	}
	

?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()">

<?php 
	$title="User Administration";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;">
			</div>
			<div class="col-sm-8">
				<div style="height:20px;"></div>
				<ul class="nav nav-tabs nav-justified">
					<li id="tab1" class="active"><a data-toggle="tab" href="#menu1">Users</a></li>
					<li id="tab2"><a data-toggle="tab" href="#menu2">New User</a></li>
				</ul>

				<div class="tab-content">
					<div id="menu1" class="tab-pane fade in active">
						<div style="height:20px;"></div>
						<div class="row" style="height:50%;">
							<div id="userTable" style="margin:0px auto;"></div>
							<div id="userDetail" class="row"></div>
						</div>
						<input id="savechk" type="hidden" value="false"/>
					</div>
					<div id="menu2" class="tab-pane fade">
						<div style="height:20px;"></div>
						<div class="row" style="height:50%;">
							<div class="well">
  								<div class="row">
									<form class="form-horizontal" id="groupList">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label col-sm-4" for="newfullname">Full Name:</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" id="newfullname">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-4" for="newrole">Role:</label>
													<div class="col-sm-8">
														<select class="form-control" id="newrole">
															<option value="alpha">Administrator</option>
															<option value="beta">Marketing</option>
															<option value="gamma">Designer</option>
															<option value="delta">View / Comment / Approve</option>
															<option value="epsilon">View / Comment</option>
															<option value="zeta" selected>View / Download Only</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-4" for="newmktg">Marketing Access:</label>
													<div class="col-sm-8">
														<select class="form-control" id="newmktg">
															<option value="1">Yes</option>
															<option value="0" selected>No</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-4" for="newproject">Projects:</label>
													<div class="col-sm-8">
														<div class="well well-sm" style="max-height:180px;min-height:34px;overflow-y:auto;background-color:white;">
															<table id="group" style="width:100%;">
																<tbody>
<?php
	$projList=mysqli_query($con,"SELECT * FROM projects ORDER BY name");
	while($proj = mysqli_fetch_assoc($projList)){
		echo '														<tr>
																		<td style="text-align:left;">
																			<div class="checkbox"><label><input type="checkbox" name="newproject" value="p'.$proj["id"].':">'.$proj["name"].'</label>
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
													<div class="col-sm-6">
														<input type="tel" class="form-control" id="newphone">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newemail">Email:</label>
													<div class="col-sm-9">
														<input type="email" class="form-control" id="newemail" onfocusout="checkEmailExist()">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newtz">Timezone:</label>
													<div class="col-sm-5">
														<select class="form-control" id="newtz">
															<option>Eastern</option>
															<option>Central</option>
															<option>Mountain</option>
															<option>Pacific</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newusername">Username:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" id="newusername" onfocusout="checkUserExist()">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newPass">Password:</label>
													<div class="col-sm-6">
														<input type="password" class="form-control" id="newPass">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-3" for="newConf">Confirm:</label>
													<div class="col-sm-6">
														<input type="password" class="form-control" id="newConf" onkeyup="checkMatch('newPass','newConf')">
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="button" class="btn btn-default" onclick="addUser()">Submit</button>
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
<?php
	include "includes/token.php";
	include "includes/stack_adm.php";
	echo '
		<script>
			function addUser(){
				if(document.getElementById("newfullname").value=="") {
					window.alert("User name is blank.");
					return;
				}
				var t = "";
				for (var i=0; i<document.getElementsByName("newproject").length; i++) {
					if(document.getElementsByName("newproject")[i].checked == true) {
						t += document.getElementsByName("newproject")[i].value;
					}
				}
				var fn = document.getElementById("newfullname").value;
				var r = document.getElementById("newrole").value;
				var ph = document.getElementById("newphone").value;
				var e = document.getElementById("newemail").value;
				var tz = document.getElementById("newtz").value;
				var un = document.getElementById("newusername").value;
				var pw = document.getElementById("newPass").value;
				var cf = document.getElementById("newConf").value;
				var mk = document.getElementById("newmktg").value;
				if(e==null){
					var n = 0;
				} else {
					var n = 1;
				}
				if(pw!==cf){
					window.alert("Password and confirmation do not match!");
				} else {
					var xmlhttp;
					var params = "fn=" + fn + "&r=" + r + "&m=" + mk + "&un=" + un + "&pw=" + pw + "&ph=" + ph + "&e=" + e + "&t=" + t + "&tz=" + tz + "&n=" + n + "&u='.$_COOKIE["user"].'";
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.alert(xmlhttp.responseText);
							userList();
							document.getElementById("menu1").className="tab-pane fade in active";
							document.getElementById("menu2").className="tab-pane fade";
							document.getElementById("userDetail").innerHTML="";
						}
					};
					xmlhttp.open("POST", "ajax/adduser.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
		</script>';
?>
		<script>
			function loadPage(){
				stackHome();
				userList();
			}
			function showUsers(id){
				if(document.getElementById("savechk").value=="true"){
					var savechk = confirm("Changes to this user have not been saved! Proceed without saving changes?");
					if (savechk == true) {
						document.getElementById("savechk").value="false";
					} else {
						return;
					}
				}
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
				xmlhttp.send(params);
			}
			function editGroup(){				
				var g = document.getElementById("group").value;
				var x = document.getElementById("admua").value;
				var m = document.getElementById("marketing").value;
				var v = document.getElementById("view").value;
				var d = document.getElementById("download").value;
				var c = document.getElementById("comment").value;
				var a = document.getElementById("approve").value;
				var xmlhttp;
				var params = 'g=' + g + '&x=' + x + '&m=' + m + '&v=' + v + '&d=' + d + '&c=' + c + '&a=' + a;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.alert(xmlhttp.responseText);
							userList();
						}
					};
					xmlhttp.open("POST", "ajax/adduser.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			function removeUserFromGroup(u,p){
				if (window.confirm("This will remove " + document.getElementById("projp" + p).value + " from the project list for " + document.getElementById("fullname").value + ".\nAre you sure?")) { 
					var xmlhttp;
					var params = 'p=' + p + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							if(xmlhttp.responseText!==''){
								window.alert(xmlhttp.responseText);
							}
							showUsers(u);
						}
					};
					xmlhttp.open("POST", "ajax/remusrfromgrp.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				} else {
					return;
				}
			}
			function closeUserDetail() {
				document.getElementById("userDetail").innerHTML = "";
			}
			function closeGroupDetail() {
				document.getElementById("groupDetail").innerHTML = "";
			}
			function get_selected_checkboxes_array(){
				var groupcode = '';
				for (var i=0; i<document.getElementsByName("newgroup").length; i++) {
					if(document.getElementsByName("newgroup")[i].checked == true) {
						groupcode += document.getElementsByName("newgroup")[i].value;
					}
				}
				window.alert(groupcode);
			}
			function checkMatch(p,c){
//				window.alert("Pass (" + p + ")=" + document.getElementById(p).value + "\nConfirm(" + c + ")=" + document.getElementById(c).value);
				if(document.getElementById(p).value!==document.getElementById(c).value) {
					document.getElementById(c).style.backgroundColor = "red";
				} else {
					document.getElementById(c).style.backgroundColor = "#9CD07D";
				}
			}
			function btnSaveEdit(u){
				if(document.getElementById("btnSave").className == "btn btn-info disabled") { return; }
				var t = '';
				for (var i=0; i<document.getElementsByName("project").length; i++) {
					t += document.getElementsByName("project")[i].value;
				}
				var fn = document.getElementById("fullname").value;
				var ph = document.getElementById("phone").value;
				var e = document.getElementById("email").value;
				var n = document.getElementById("notify").value;
				var r = document.getElementById("role").value;
				var tz = document.getElementById("tz").value;
				var xmlhttp;
				var params = 'u=' + u + '&fn=' + fn + '&ph=' + ph + '&e=' + e + '&n=' + n + '&r=' + r + '&tz=' + tz;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						window.alert(xmlhttp.responseText);
						document.getElementById("btnSave").className = "btn btn-info disabled";
						document.getElementById("savechk").value = "false";
						userList();
					}
				};
				xmlhttp.open("POST", "ajax/edituser.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function chgPW(u){
				var p = document.getElementById("chgPass").value;
				var c = document.getElementById("chgConf").value;
				if(p!==c){
					window.alert("Password and confirmation do not match!");
				} else {
					var xmlhttp;
					var params = 'u=' + u + '&p=' + p;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.alert(xmlhttp.responseText);
						}
					};
					xmlhttp.open("POST", "ajax/chgpw.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function addProj(u){
				var projCode = '';
				for (var i=0; i<document.getElementsByName("addProject").length; i++) {
					if(document.getElementsByName("addProject")[i].checked == true) {
						projCode += document.getElementsByName("addProject")[i].value;
					}
				}
				var xmlhttp;
				var params = 'u=' + u + '&p=' + projCode;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							if(xmlhttp.responseText){
								window.alert(xmlhttp.responseText);
							}else{
								showUsers(u);
							}
						}
					};
					xmlhttp.open("POST", "ajax/addproject.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			function checkEmailExist(){
				var e = document.getElementById("newemail").value;
				if(e=='') {
					if (window.confirm("The email field is blank.\nThis user will not be able to receive\nnotifications from QuickProof.\nAre you sure?")) { 
						document.getElementById("newemail").value = "-";
						e = "-";
					} else {
						return;
					}
				} 
				var xmlhttp;
				var params = 'e=' + e;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText!=="OK"){
							if (window.confirm("This email address already exists.\nWould you like to find the user\nthe email address belongs to?")) { 
								document.getElementById("menu1").className = "tab-pane fade in active";
								document.getElementById("menu2").className = "tab-pane fade";
								document.getElementById("tab2").className = "";
								document.getElementById("tab1").className = "active";
								showUsers(xmlhttp.responseText);
							}
						}
					}
				};
				xmlhttp.open("POST", "ajax/checkemailexist.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function checkUserExist(){
				var e = document.getElementById("newusername").value;
				if(e=='') {
					window.alert("The username is blank.");
				} else {
					var xmlhttp;
					var params = 'e=' + e;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							if(xmlhttp.responseText!=="OK"){
								if (window.confirm("This username already exists.\nWould you like to find the user\nthe username belongs to?")) { 
									document.getElementById("menu1").className = "tab-pane fade in active";
									document.getElementById("menu2").className = "tab-pane fade";
									document.getElementById("tab2").className = "";
									document.getElementById("tab1").className = "active";
									showUsers(xmlhttp.responseText);
								}
							}
						}
					};
					xmlhttp.open("POST", "ajax/checkuserexist.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function deleteUser(u,n){
				if(window.confirm("You are about to PERMANENTLY REMOVE\n" + n + " from Proof Express.\n\nAre you sure?")) { 
					var xmlhttp;
					var params = 'u=' + u + '&n=' + n;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.alert(xmlhttp.responseText);
							userList();
						}
					};
					xmlhttp.open("POST", "ajax/deluser.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function userList(){
				var xmlhttp;
				var params = '';
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("userTable").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/userlist.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
	</body>
</html>