<?php
	include('includes/inc.php');
	if(isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
	
	if(!isset($_GET["token"])) {
		$msg='<h3>Error!</h3><p align="center">This page can only be accessed through a link within a reset confirmation email.</p><p align="center">If you are seeing this message as a result of clicking a link from a reset confirmation email, please notify tech support immediately.</p>';
	} else {
		$getToken=explode(":",$_GET["token"]);
		$token=$getToken[0];
		$id=$getToken[1];
		$tQuery=mysqli_query($con,"SELECT * FROM users WHERE id = ".$id." AND sha256 = '".$token."'");
		$msg='<h3>Password Reset Confirmation</h3><p align="center">Please confirm the user information below before proceeding.</p>';
	}
?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()" onresize="scrollheight()">

<?php 
	$title="Reset User Credentials";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;">

<?php
//	print_r($_SESSION); 
?>
			</div>
			<div class="col-sm-6">
				<div style="height:20px;"></div>
<?php echo $msg; ?>
				<form class="form-horizontal">
<?php
	if(isset($_GET["token"])) {
		while($row=mysqli_fetch_assoc($tQuery)){
			echo '	<div class="form-group">
						<label class="control-label col-sm-2" for="fullname">Full Name:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="fullname" value="'.$row["fullname"].'">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="user">User Name:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="user" value="'.$row["username"].'">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="nPass">New Password:</label>
						<div class="col-sm-10">
							<input type="password" class="form-control" id="nPass">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="cPass">Confirm Password:</label>
						<div class="col-sm-10">
							<input type="password" class="form-control" id="cPass" onkeyup="checkMatch(\'nPass\',\'cPass\')">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" id="id" value="'.$row["id"].'">
							<button type="button" class="btn btn-info" onclick="confirmReset()">Reset password</button>
						</div>
					</div>';
		}
	}
?>
				</form>
			</div>
			<div class="col-sm-3">
<?php //include "includes/stack_techsupport.php"; ?>
			</div>
		</div>
		<script>
			function loadPage() {
				windowheight();
			}
			function windowheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("scrolltable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function checkMatch(p,c){
//				window.alert("Pass (" + p + ")=" + document.getElementById(p).value + "\nConfirm(" + c + ")=" + document.getElementById(c).value);
				if(document.getElementById(p).value!==document.getElementById(c).value) {
					document.getElementById(c).style.backgroundColor = "red";
				} else {
					document.getElementById(c).style.backgroundColor = "#9CD07D";
				}
			}
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("scrolltable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function confirmReset(){
				var np = document.getElementById("nPass").value;
				var cp = document.getElementById("cPass").value;
				if(np==cp){
					var xmlhttp;
					var id = document.getElementById("id").value;
					var fn = document.getElementById("fullname").value;
					var un = document.getElementById("user").value;
					var params = "id=" + id + "&fn=" + fn + "&un=" + un + "&p=" + np;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							if(xmlhttp.responseText=="OK"){
								window.alert("Your password has been reset successfully.");
								window.location.href = 'home.php';
							}
							if(xmlhttp.responseText=="Error"){
								window.alert("We're sorry. An error occurred during the resetting process.");
							}
						}
					};
					xmlhttp.open("POST", "ajax/pwconf.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				} else {
					window.alert("Password and confirmation do not match.");
				}
			}
		</script>
	</body>
</html>