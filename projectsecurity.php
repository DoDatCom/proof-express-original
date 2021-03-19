<?php session_start();
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
	
	
	if(isset($_GET["p"])) {
		$pid=$_GET["p"];
		$query=mysqli_query($con,"SELECT * FROM projects WHERE id = '".$pid."'") or die("This folder does not exist");
		while($row=mysqli_fetch_assoc($query)) {
			$pname=$row["name"];
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
<?php echo '<body onload="loadPage('.$pid.')">'; ?>

<?php 
	$title="Project Security";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;"></div>
			<div class="col-sm-6">
<?php echo '	<h3>User List for '.$pname.'</h3>'; ?>
				<div id="userTable"></div>
			</div>
			<div class="col-sm-3">
				<div style="height:40px"></div>
				<div class="panel-group" id="options">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="modal" data-target="#userList" onclick="clearSearch()">Add Users to Project</a>
							</h4>
						</div>
					</div>
<?php
	if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9") {
		echo '		<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#options" href="#rename">Rename Project</a>
							</h4>
						</div>
						<div id="rename" class="panel-collapse collapse">
							<div class="panel-body">
								<p>Type the name to replace '.$pname.':</p>
								<input id="newProjName" type="text">
								<button type="button" class="btn btn-success" onclick="renameProject('.$pid.',\''.$pname.'\')">Rename</button>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a onclick="delProject('.$pid.',\''.$pname.'\',\''.$_COOKIE["user"].'\')">Deactivate Project</a>
							</h4>
						</div>
					</div>';
	}
?>
				</div><!-- pannel-group -->
			</div><!-- col-sm-3 -->
		</div><!-- row -->
		<div id="userList" class="modal fade" role="dialog">
				<div style="height:150px;"></div>
				<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Add User</h4>
					</div>
					<div class="modal-body" style="max-height:280px;min-height:34px;overflow-y:auto;background-color:white;">
						<table id="scrolltable" style="width:100%;"></table>
					</div>
					<div class="modal-footer">
						<input id="userSearch" type="text" class="form-control" placeholder="Quick search" onkeyup="userSearch()">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
<?php echo '			<button type="button" class="btn btn-success" data-dismiss="modal" onclick="addUsers('.$pid.')">Add</button>'; ?>
					</div>
				</div>
				</div>
			</div>
<?php include "includes/token.php";
	if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
		include "includes/stack_adm.php";
	} elseif($_COOKIE["bnw"]=="63bcabf86a9a991864777c631c5b7617" || $_COOKIE["bnw"]=="3cd38ab30e1e7002d239dd1a75a6dfa8" || $_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518") {
		include "includes/stack_gen_projects.php";
	}
?>
		<script>
			function loadPage(p){
				userList(p);
				userSearch();
				stackProjects(p);
			}
			function removeUser(u,p,n){
				if (window.confirm("This will remove " + n + " from this project.\nAre you sure?")) { 
					var xmlhttp;
					var params = 'p=' + p + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							if(xmlhttp.responseText!==''){
								window.alert(xmlhttp.responseText);
							}
							userList(p);
						}
					};
					xmlhttp.open("POST", "ajax/remusrfromgrp.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				} else {
					return;
				}
			}
			function addUsers(p){
				var userCode = '';
				for (var i=0; i<document.getElementsByName("addUser").length; i++) {
					if(document.getElementsByName("addUser")[i].checked == true) {
						userCode += document.getElementsByName("addUser")[i].value;
					}
				}
				var xmlhttp;
				var params = 'p=' + p + '&u=' + userCode;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText){
							window.alert(xmlhttp.responseText);
						}else{
							location.reload();
						}
					}
				};
				xmlhttp.open("POST", "ajax/addprojectusers.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function userList(p){
				var xmlhttp;
				var params = 'p=' + p;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("userTable").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/projectlist.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function userSearch() {
				var xmlhttp;
				var us = document.getElementById("userSearch").value;
				var params = 'us=' + us;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText !== "") {
							document.getElementById("scrolltable").innerHTML = xmlhttp.responseText;
							document.getElementById("userSearch").style.backgroundColor = "white";
						} else {
							document.getElementById("userSearch").style.backgroundColor = "red";
						}
					}
				};
				xmlhttp.open("POST", "ajax/usersearch_modal.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function renameProject(p,n){
				var newName = document.getElementById("newProjName").value;
				if(confirm("You are about to change " + n + " to " + newName + "! Are you sure?") == true) {
					var xmlhttp;
					var params = 'p=' + p + '&o=' + n + '&n=' + newName;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							location.reload();
						}
					};
					xmlhttp.open("POST", "ajax/renameproject.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function delProject(p,n,u){
				if(confirm("You are about to DEACTIVATE " + n + " from Proof Express! Are you sure?") == true) {
					var xmlhttp;
					var params = 'pid=' + p + '&n=' + n + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.alert(xmlhttp.responseText);
							location.replace("home.php");
						}
					};
					xmlhttp.open("POST", "ajax/delproject.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function clearSearch(){
				document.getElementById("userSearch").value='';
				userSearch();
			}
		</script>
	</body>
</html>