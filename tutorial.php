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
	$title="Tutorial";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;">

<?php
//	print_r($_COOKIE["token"]);
?>
			</div>
			<div class="col-sm-5">
				<h2>Proof Express Tutorial</h2>
				<p class="text-justify">Proof Express is a website application developed by the Image Center for the Ad proof-review cycle. It’s built to facilitate exchange of information and feedback on an ad, and to centralize that information in such a way that all users have access to the latest at all times.</p>
				<p class="text-justify">The idea is: all users who have any input on a particular ad [page] first find it in Proof Express. You can view a PDF of the ad and add your input to the ad online. Everyone who has something to do with that file – advertising, merchandising, production – sees everyone else’s comments, and revisions come in to go along with them. When your responsibility on that file is finished, you <span class="glyphicon glyphicon-ok-sign" style="color:blue;"></span> sign off on it (if you’re only partially responsible) or <span class="glyphicon glyphicon-ok-sign" style="color:green;"></span> approve it (if you’re the final “go-ahead” on the ad).</p>
				<p class="text-justify">To use Proof Express, use any web browser. You first have to log in. This is relatively simple – contact your ImageCenter representative for a username and password (passwords are case-sensitive), then enter those in the form on the main page. Every time you return to Proof Express, you’ll have to re-enter these, so keep them handy.</p> 
				<p class="text-justify">This will bring up your project list. If you’re only connected to one project, it’ll take you straight to it. Clicking a logo or name takes you to the project’s home. This will display news specific to the project (accelerated deadlines, etc.), and the base folder’s contents.</p>
				<p class="text-justify">Click a folder name to browse that folder, then click a file name to go to the discussion of a file. Along the top, a path follows your browsing - to return to a previously viewed folder, click its name in the path. Clicking the <span class="filetypes filetypes-pdf x05" style="color:red;"></span> icon of a file opens the current revision of a file in a new window. A red star <span class="glyphicons glyphicons-star x05" style="color:red;"></span> appears to the left of every file whose current revision your username has not downloaded before – this provides at-a-glance information on what should be new to you.</p>
				<p class="text-justify">Comments are displayed in newest first / oldest last order. Clicking the filename on the path indicator (above) opens the most recent revision of the file in a new window. This is typically the way you’ll view the ad.</p>
				<p class="text-justify">Type your comment out in the field (make it as long as you’d like, there is no practical limit), click Post Comment to add it to the discussion. <span class="glyphicons glyphicons-ok-sign" style="color:blue;"></span> Signing off on a file and <span class="glyphicons glyphicons-ok-sign" style="color:green;"></span> Approving a file are relatively straightforward – click the corresponding link if it is available to you.</p>
				<p class="text-justify">Revisions on the file are listed on the left below the approvals log, clicking the revision number or date opens each one in a new window – this is useful if you’re looking at design-changes to a file and need to see things side-by-side. Again, a  red star indicates a revision of a file that your account has not downloaded previously, indicating a version you haven't seen.</p>
				<p class="text-justify">Since Proof Express is a web application running 24x7 on the ImageCenter servers, all you need to access it is a connection to the NFC company network. This is available in all distribution centers, and on the road via VPN (Nortel Extranet as-of this writing).</p>
				<p class="text-justify">If you’re having any technical difficulties, contact me @ 513-792-6405, or via the tech support link across the top of the Proof Express site. I’ll resolve your issues as soon as I am able to do so.</p>
			</div>
			<div class="col-sm-4" style="text-align:justify;">
				<div class="well">
					<h3>A quick guide to site graphics:</h3>
					<p><span class="glyphicons glyphicons-ok-sign x1" style="color:green;"></span> An approved file is considered "done" - no more revising, no more comments. In order to un-Approve a file, a project lead must be contacted first, by email or telephone. This is so they can make sure the production process gets halted for changes to the ad in question. Read access to approved files is always allowed.</p>
					<p><span class="glyphicons glyphicons-ok-sign x1" style="color:blue;"></span> Sign-off is approval's little cousin. In a situation where 10 people must finalize various portions of an ad, they each sign off on a revision of the file, indicating their area of responsibility is finished.</p>
					<p><span class="filetypes filetypes-pdf x1" style="color:red;"></span> PDF is the most common file type you will see on Proof Express. Clicking this will generally open a new window pointed to the file in question.</p>
					<p><span class="glyphicons glyphicons-lock x1" style="color:darkcyan;"></span> A lock indicates a closed discussion or file. Project leads may lock files or folders when they need time to revise ads in order to catch up to feedback, or when a week's ad is considered done. Read access to locked files is always allowed.</p>
					<p><span class="glyphicons glyphicons-star x1" style="color:red;"></span> A red star indicates a revision of a file that your username has not downloaded yet. It provides at-a-glance information on what is supposed to be new to you.</p>
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