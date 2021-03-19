<?php
	include('includes/inc.php');
	if(isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()" onresize="scrollheight()">

<?php 
	$title="Password Reset";
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
				<h3>Password Reset</h3>
				<form class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-sm-2" for="email">Email:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="email">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
<?php echo '				<button type="button" class="btn btn-info" onclick="submitReset()">Reset password</button>'; ?>
						</div>
					</div>
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
			
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("scrolltable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function submitReset(){
				var xmlhttp;
				var email = document.getElementById("email").value;
				var params = 'e=' + email;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText=="OK"){
							window.alert("A password reset confirmation has been sent to your email. To complete the reset, follow the link in the email.");
							window.location.href = 'home.php';
						}
						if(xmlhttp.responseText=="Not found"){
							window.alert("We're sorry. The email address you have submitted was not found in our records. Please check the email address and try again.");
						}
					}
				};
				xmlhttp.open("POST", "ajax/pwreset.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
	</body>
</html>