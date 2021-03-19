<?php session_start();
	
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
	$query=mysqli_query($con,"SELECT * FROM users WHERE sha256 = '".$_COOKIE["token"]."'") or die("SQL error");
	while($row=mysqli_fetch_assoc($query)) {
		$tzone = $row["tzone"];
		$notify = $row["notify"];
		$id = $row["id"];
	}

?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()">

<?php 
	$title="My Preferences";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;">

<?php
//	print_r($_COOKIE["token"]);
?>
			</div>
			<div class="col-sm-6">
				<div class="well">
					<form id="prefs" class="form-horizontal">
					<div class="row">
						<div class="form-group">
							<label class="control-label col-sm-3" for="tz">Set time zone:</label>
							<div class="col-sm-3">
								<select class="form-control" id="tz">
<?php echo '						<option selected>'.$tzone.'</option>'; ?>
									<option disabled>-------</option>								
									<option>Eastern</option>
									<option>Central</option>
									<option>Mountain</option>
									<option>Pacific</option>
								</select>
							</div>
						</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label class="control-label col-sm-3" for="subscribe">Notifications:</label>
								<div class="col-sm-3">
<?php
	if($notify==0){ echo '			<label class="radio-inline"><input type="radio" name="subscribe" checked>Off</label>'; } else { echo '<label class="radio-inline"><input type="radio" name="subscribe">Off</label>'; }
	if($notify==1){ echo '			<label class="radio-inline"><input type="radio" name="subscribe" checked>On</label>'; } else { echo '<label class="radio-inline"><input type="radio" name="subscribe">On</label>'; }
?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label class="control-label col-sm-3" for="oldpw">Current password:</label>
								<div class="col-sm-3">
									<input type="password" class="form-control" id="oldpw">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label class="control-label col-sm-3" for="newpw">New Password:</label>
								<div class="col-sm-3">
									<input type="password" class="form-control" id="newpw">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label class="control-label col-sm-3" for="confpw">Confirm Password:</label>
								<div class="col-sm-3">
									<input type="password" class="form-control" id="confpw" onkeyup="checkPass(); return false;">
									<span id="confirmMessage" class="confirmMessage"></span>
								</div>
							</div>
						</div>
						<div class="row">
<?php echo '				<input type="hidden" id="recid" value="'.$id.'">'; ?>
							<div class="col-sm-3">
								<button type="button" class="btn btn-primary" onclick="prefEdit()">Update Preferences</button>
							</div>
							<div class="col-sm-3" id="prefSave"></div>
						</div>
					</form>	
				</div>		
			</div>
		</div>
<?php include "includes/js.php"; ?>
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
			}
			function post(to,p){
				var myForm = document.createElement("form");
				myForm.method="post" ;
				myForm.action = to ;
				for (var k in p) {
					var myInput = document.createElement("input") ;
					myInput.setAttribute("name", k) ;
					myInput.setAttribute("value", p[k]);
					myForm.appendChild(myInput) ;
				}
				document.body.appendChild(myForm) ;
				myForm.submit() ;
				document.body.removeChild(myForm) ;
			}
			function checkPass(){
				var pass1 = document.getElementById('newpw');
				var pass2 = document.getElementById('confpw');
				var message = document.getElementById('confirmMessage');
				var goodColor = "#66cc66";
				var badColor = "#ff6666";
				if(pass1.value == pass2.value) {
					pass2.style.backgroundColor = goodColor;
					message.style.color = goodColor;
					message.innerHTML = "Passwords Match!";
				} else {
					pass2.style.backgroundColor = badColor;
					message.style.color = badColor;
					message.innerHTML = "Passwords Do Not Match!";
				}
			}
			function prefEdit() {
				var npw = document.getElementById('newpw').value;
				var cpw = document.getElementById('confpw').value;
				var opw = document.getElementById('oldpw').value;
				var test;
				test = opw.length;
				if(test>0) {
					if(npw!==cpw){
						window.alert(npw + " doesn't match " + cpw);
						return;
					} else {
						var ok;
					}
				} else {
					npw = "nil";
					opw = "nil";
				}
				var xmlhttp;
				var id = document.getElementById('recid').value;
				var tz = document.getElementById('tz').value;
				if(document.getElementsByName("subscribe")[1].checked){
					var n = 1;
				} else {
					var n = 0;
				}	
				var params = 'id=' + id + '&opw=' + opw + '&npw=' + npw + '&tz=' + tz + '&n=' + n;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText == '<p style="color:green;"><b>Preferences updated</b></p>') {
							document.getElementById("prefSave").innerHTML = xmlhttp.responseText;
							setTimeout(clearAJAX, 3000);
						} else {
							window.alert(xmlhttp.responseText);
						}
					}
				};
				xmlhttp.open("POST", "ajax/prefedit.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function clearAJAX() {
				$("#prefSave").fadeOut('slow');
			}
		</script>
	</body>
</html>