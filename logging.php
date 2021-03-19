<?php session_start();

	include "includes/inc.php";
	
	if(!isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
	if($_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="63bcabf86a9a991864777c631c5b7617" || $_COOKIE["bnw"]=="3cd38ab30e1e7002d239dd1a75a6dfa8" || $_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518") {
		header("Location: ".WEB);
	}

?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()" onresize="scrollheight()">

<?php 
	$title="Site Logging";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;"></div>
			<div class="col-sm-9"> <!-- Main Page Area -->
				<div class="row"> <!-- row -->
					<h3>Site Logging</h3>
					<div class="col-sm-3">
						<label for="logType">Log Type:</label>
						<select class="form-control" id="logType">
<?php
	$logTypeQuery=mysqli_query($con,"SELECT DISTINCT activity FROM user_log ORDER BY activity");
	while($row = mysqli_fetch_assoc($logTypeQuery)){
		echo '				<option>'.$row["activity"].'</option>';
	}
?>
      					</select>
      					<br/>
<?php echo'				<button type="button" class="btn btn-info" onclick="logQuery(\''.$_COOKIE["tz"].'\')">Run query</button>'; ?>
      				</div>
					<div id="logWindow" class="col-sm-9"></div>
				</div> <!-- end of row -->
			</div><!-- end of Main Page Area -->
		</div> <!-- end of entire page -->

<?php
	include "includes/js.php";
	include "includes/stack_adm.php";
?>
		<script>
			function loadPage(){
				stackHome();
				windowheight();
			}
			function windowheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("logTable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("logTable").style.height = h + "px";
				document.getElementById("stack").style.height = s + "px";
			}
			function logQuery(z){
				var xmlhttp;
				var params = 't=' + document.getElementById("logType").value + '&tz=' + z;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("logWindow").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/logquery.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
	</body>
</html>