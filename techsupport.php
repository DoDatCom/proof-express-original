<?php session_start();
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
	

?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()" onresize="scrollheight()">

<?php 
	$title="Tech Support";
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
				<form class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-sm-2" for="subject">Subject <i>(optional)</i>:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="subject">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="issue">Issue Description:</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="8" id="issue"></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
<?php echo '				<button type="button" class="btn btn-default" onclick="submitTechSupport(\''.$_COOKIE["user"].'\')">Submit</button>'; ?>
						</div>
					</div>
				</form>
			</div>
			<div class="col-sm-3">
<?php //include "includes/stack_techsupport.php"; ?>
			</div>
		</div>
<?php include "includes/token.php"; ?>
<?php 
	if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
		include "includes/stack_adm.php";
	} elseif($_COOKIE["bnw"]=="63bcabf86a9a991864777c631c5b7617" || $_COOKIE["bnw"]=="3cd38ab30e1e7002d239dd1a75a6dfa8" || $_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518") {
		include "includes/stack_gen.php";
	}
?>
		<script>
			function loadPage() {
				stackHome();
				windowheight();
			}
			function windowheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("scrolltable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
				
				checkCookie();
			}
			
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("scrolltable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function submitTechSupport(un){
				var xmlhttp;
				var subject = document.getElementById("subject").value;
				var issue = document.getElementById("issue").value;
				var params = 'un=' + un + '&s=' + subject + '&i=' + issue;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						window.alert(xmlhttp.responseText);
						window.location.href = 'home.php';
					}
				};
				xmlhttp.open("POST", "ajax/techsuppemail.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
	</body>
</html>