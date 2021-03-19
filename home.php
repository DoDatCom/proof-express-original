<?php session_start();
	
	include "includes/inc.php";
	if(!isset($_COOKIE["session"])) {
		header("Location: ".WEB);
	}
	if(isset($_COOKIE["bnw"]) && $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92") { // Marketing
		header("Location: ".WEB."/project.php?p=294");
	}

?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="loadPage()" onresize="scrollheight()">

<?php 
	$title="Projects";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;"></div>
			<div class="col-sm-9"> <!-- Main Page Area -->
<?php
	if($_COOKIE["ring"]=='admin') {
		echo '	<div id="jumpNav" class="row"></div>';
	}
?>
				<div class="row"><!-- start of scrolling table -->
					<div class="col-sm-9"><!-- start of table -->
						<div id="windowBody" style="width:518px;margin:0px auto;"></div>
					</div> <!-- end of table -->
					<div class="col-sm-2"> <!-- start of sidebar -->
<!--					<input id="search" type="text" class="form-control" placeholder="Quick search" onkeyup="projectSearch()"> -->
<?php
	if(isset($_COOKIE["bnw"]) && $_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9") { // Administrator
		echo '			<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#newProject">New Project</button>
						<a type="button" class="btn btn-info btn-block" href="recover.php">Recover Project</a>';
	}
?>
					</div> <!-- end of sidebar -->
					<div class="col-sm-1"></div>
				</div> <!-- end of scrolling table -->
			</div><!-- end of Main Page Area -->
		</div> <!-- end of entire page -->
		<div id="newProject" class="modal fade" role="dialog">
			<div style="height:150px;"></div>
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('projectName').value = '';">&times;</button>
						<h4 class="modal-title">New Project</h4>
					</div>
					<div class="modal-body" style="max-height:280px;min-height:34px;overflow-y:auto;background-color:white;">
						<div class="form-group">
							<label class="control-label col-sm-4" for="projectName">Provide a name for the new project:</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="projectName">
							</div>
						</div>
					</div>
					<div class="modal-footer">
<?php echo '			<button type="button" class="btn btn-success" onclick="checkProjectName(\''.$_COOKIE["user"].'\')">Confirm</button>'; ?>
					</div>
				</div>
			</div>
		</div>
<?php 
	include "includes/js.php";
	if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
		include "includes/stack_adm.php";
	} elseif($_COOKIE["bnw"]=="63bcabf86a9a991864777c631c5b7617" || $_COOKIE["bnw"]=="3cd38ab30e1e7002d239dd1a75a6dfa8" || $_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518") {
		include "includes/stack_gen.php";
	}

	echo '
		<script>
			function listHome(){
				var xmlhttp;
				var params = "ring='.$_COOKIE["ring"].'&mktg='.$_COOKIE["mktg"].'";
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("windowBody").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/homelist.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function jumpNav(){
				var xmlhttp;
				var params = "ring='.$_COOKIE["ring"].'&mktg='.$_COOKIE["mktg"].'";
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("jumpNav").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/jumpnav.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
		
	';
?>
		
		<script type="text/javascript">
			function loadPage(){
				stackHome();
				listHome();
				jumpNav();
				windowheight();
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
			function post(to,p) {
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
			function checkProjectName(u){
				var xmlhttp;
				var params = 'pname=' + document.getElementById("projectName").value + '&u=' + u;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText=="OK"){
							document.getElementById("projectName").value = "";
							$('#newProject').modal('hide');
							location.reload(true);
						} else {
							window.alert(xmlhttp.responseText);
						}
					}
				};
				xmlhttp.open("POST", "ajax/checkprojname.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
	</body>
</html>