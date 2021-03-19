<?php
	include('includes/inc.php');
	if(isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
	
	if(!isset($_GET["t"])) {
		$msg='<h3>Error!</h3><p align="center">This page can only be accessed through an unsubscribe link within a notification email.</p><p align="center">If you are seeing this message as a result of clicking an unsubscribe link from a notification email, please notify tech support immediately.</p>';
	} else {
		$getToken=explode(":",$_GET["t"]);
		$token=$getToken[0];
		$id=$getToken[1];
		$tQuery=mysqli_query($con,"SELECT * FROM users WHERE id = ".$id." AND sha256 = '".$token."'");
		$msg='<h3>Unsubscribe Confirmation</h3><p align="center">To remove your subscription to Proof Express notifications, click the button below.</p>';
	}
?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()" onresize="scrollheight()">

<?php 
	$title="Unsubscribe Confirmation";
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
	if(isset($_GET["t"])) {
		while($row=mysqli_fetch_assoc($tQuery)){
			echo '	<div class="form-group">
						<input type="hidden" id="uid" value="'.$row["id"].'">
						<button type="button" class="btn btn-info" onclick="confirmUnsub()">Unsubscribe</button>
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
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("scrolltable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function confirmUnsub(){
				var uid = document.getElementById("uid").value;
				var xmlhttp;
				var params = "uid=" + uid;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText=="OK"){
							window.alert("You have been removed from Proof Express notifications successfully.");
							window.location.href = 'home.php';
						}
						if(xmlhttp.responseText=="Error"){
							window.alert("We're sorry. An error occurred during the removal process.");
						}
					}
				};
				xmlhttp.open("POST", "ajax/unsub.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
	</body>
</html>