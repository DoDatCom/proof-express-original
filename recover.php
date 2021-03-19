<?php session_start();
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) { header("Location: ".WEB); }
	
	
	$rQuery=mysqli_query($con,"SELECT * FROM projects WHERE active = 0") or die("This folder does not exist");
	
?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()" onresize="scrollheight()">

<?php 
	$title="Recover Project";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;"></div>
			<div class="col-sm-9">
				<div class="row">
					<div class="col-sm-6" style="text-align:left;">
						<ul class="breadcrumb">
							<li><a href="home.php">Return to Project List</a></li>
						</ul>
					</div>
				</div>
				<div id="windowBody">
					<table class="table table-striped">
						<thead>
							<tr id="tabletop">
								<th style="text-align:left;">Project</th>
								<th style="text-align:left;">Deactivated by</th>
								<th style="text-align:left;">Deactivation Date/Time</th>
								<th colspan="2"></th>
							</tr>
						</thead>
						<tbody>
<?php
	if(mysqli_num_rows($rQuery)<1) {
		echo '
							<tr>
								<td colspan="5">There are no projects to recover.</td>
							</tr>
			';
	} else {
		while($r=mysqli_fetch_assoc($rQuery)){
			$act=array();
			$act=explode(":",$r["last_activity"]);
			echo '
							<tr>
								<td style="text-align:left;">'.$r["name"].'</a></td>
								<td style="text-align:left;">'.$act[1].'</td>
								<td style="text-align:left;">'.timestamp($act[0],$_COOKIE["tz"]).'</td>
								<td><button type="button" class="btn btn-info btn-sm" onclick="recProject('.$r["id"].',\''.$r["name"].'\',\''.$_COOKIE["user"].'\')">Recover Project</button></td>
								<td><button type="button" class="btn btn-danger btn-sm" onclick="burnProject('.$r["id"].',\''.$r["name"].'\',\''.$_COOKIE["user"].'\')">Delete Project</button></td>
							</tr>
			';
		}
	}
?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="modal fade" id="wait" role="dialog">
			<div style="height:120px;"></div>
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-body">
						<img src="images/sn_wait_100.gif" />
					</div>
				</div>
			</div>
		</div>
		
<!--		<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#calendar">Calendar Test</button> -->

<?php
	include "includes/js.php";
	include "includes/stack_adm.php";
?>	
		<script>
			function loadPage(){
				windowheight();
				stackHome();
			}
			function windowheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function recProject(p,n,u){
				if(confirm("You are about to recover " + n + " to Proof Express! Are you sure?") == true) {
					var xmlhttp;
					var params = 'pid=' + p + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.alert(xmlhttp.responseText);
							location.replace("home.php")
						}
					};
					xmlhttp.open("POST", "ajax/recoverproject.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function burnProject(p,n,u){
				if(confirm("You are about to PERMANENTLY DELETE " + n + " from Proof Express! RECOVERING THIS PROJECT OR ANY ASSOCIATED CONTENT WILL NOT BE POSSIBLE! Are you sure?") == true) {
					$("#wait").modal();
					var xmlhttp;
					var params = 'pid=' + p + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.alert(xmlhttp.responseText);
							location.replace("home.php");
						}
					};
					xmlhttp.open("POST", "ajax/burnproject.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
		</script>
	</body>
</html>