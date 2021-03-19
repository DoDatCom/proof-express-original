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
	$title="User Guide";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;">

<?php
//	print_r($_COOKIE["token"]);
?>
			</div>
			<div class="col-sm-2"></div>
			<div class="col-sm-5">
				<div class="well well-sm">
					<h3>To view/download the complete Proof Express User Guide,</h3>
<?php echo '		<h3><a href="'.WEB.'/ProofExpress_UserGuide.pdf">click here</a>.</h3>'; ?>
					<hr/>
					<p class="text-center">For additional questions or technical support please contact:</p>
					</br>
					<div class="row">
						<div class="col-sm-6">
							<p class="text-center"><b>Kevin Engelhard</b></p>
							<p class="text-center"><a href="mailto:kevin.engelhard@spartannash.com">kevin.engelhard@spartannash.com</a></p>
							<p class="text-center">513-792-6424</p>
						</div>
						<div class="col-sm-6">
							<p class="text-center"><b>Krista Walker</b></p>
							<p class="text-center"><a href="mailto:krista.walker@spartannash.com">krista.walker@spartannash.com</a></p>
							<p class="text-center">513-792-6477</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
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
				windowheight();
			}
			function windowheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("stack").style.height = s + "px";
			}
			
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
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
		</script>
	</body>
</html>