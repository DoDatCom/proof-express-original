<?php session_start();
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) { header("Location: ".WEB); }
	
	
	if(isset($_GET["p"])) {
		$pid=$_GET["p"];
		$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = '".$pid."'") or die("This folder does not exist");
		while($p=mysqli_fetch_assoc($pQuery)) {
			$pname=$p["name"];
		}
	}
	if(isset($_GET["f"])) {
		$fid=$_GET["f"];
		$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = '".$fid."'") or die("This folder does not exist");
		while($f=mysqli_fetch_assoc($fQuery)) {
			$fname=$f["name"];
		}
		$folder=true;
	} else {
		$folder=false;
	}
	if(isset($_GET["a"])) {
		$aid=$_GET["a"];
		$adQ=mysqli_query($con,"SELECT * FROM ads WHERE id = '".$aid."'") or die("This folder does not exist");
		while($a=mysqli_fetch_assoc($adQ)) {
			$aname=$a["name"];
		}
	}
	
?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body onload="stackHome()">

<?php 
	$title="Upload New Ad";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;"></div>
			<div class="col-sm-9">
				<div class="row">
					<div class="col-sm-12" style="text-align:left;">
						<ul class="breadcrumb" style="margin-bottom: 0px;">
							<li><a href="index.php">Return to Project List</a></li>
<?php
	echo '				<li><a href="project.php?p='.$pid.'">'.$pname.'</a></li>'; 
	if($folder==true){
		echo '			<li><a href="folder.php?p='.$pid.'&f='.$fid.'">'.$fname.'</a></li>'; 
	}
?>
							<li class="active">New Ad</li>
						</ul>
					</div>
				</div>
				<div class="row">
					<h4 id="weekCode"></h4>
				</div>
				<div class="row">
					<div class="col-sm-4"></div>
					<div class="col-sm-8">
<?php
	echo '				<input type="hidden" id="project" value="'.str_replace(" ","_",$pname).'">';
	if($folder==true){
		echo '			<input type="hidden" id="folder" value="'.str_replace(" ","_",$fname).'">';
	}
	echo '				<input type="hidden" id="id" value="'.$pid.'">';
?>
						<div id="mulitplefileuploader">Upload</div>
						<div id="status"></div>
					</div>
				</div>
			</div>
		</div>
<?php 
	if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
		include "includes/stack_adm.php";
	} elseif($_COOKIE["bnw"]=="63bcabf86a9a991864777c631c5b7617" || $_COOKIE["bnw"]=="3cd38ab30e1e7002d239dd1a75a6dfa8" || $_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518") {
		include "includes/stack_gen.php";
	}
	
//	Inject folder option into JavaScript code.
	if($folder==true){
		$AJAXparams = 'pid='.$pid.'&fid='.$fid.'&aid='.$aid.'&aname='.$aname.'&tz='.$_COOKIE["tz"];
		$adText = 'Upload files to '.$pname.' / ';
		$ulURL = '?pid='.$pid.'&fid='.$fid.'&aid='.$aid.'&aname='.$aname.'&u='.$_COOKIE["user"];
	} else {
		$AJAXparams = 'pid='.$pid.'&aid='.$aid.'&aname='.$aname.'&tz='.$_COOKIE["tz"];
		$adText = 'Upload files to ';
		$ulURL = '?pid='.$pid.'&aid='.$aid.'&aname='.$aname.'&u='.$_COOKIE["user"];
	}
	echo '
		<script>
			function windowheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("navstack").style.height = s + "px";
			}
			function scrollheight() {
				var h = window.innerHeight - 290;
				var s = window.innerHeight - 169;
				document.getElementById("windowBody").style.height = h + "px";
				document.getElementById("navstack").style.height = s + "px";
			}
			$(document).ready(function() {
				var settings = {
					url: \'ajax/upload.php'.$ulURL.'\',
					method: "POST",
					allowedTypes:"jpg,png,gif,pdf,zip",
					fileName: "myfile",
					multiple: true,
					onSuccess:function(files,data,xhr) {
						$("#status").html("<font color=\'green\'>Upload successful</font>");
					},
					onError: function(files,status,errMsg) {		
						$("#status").html("<font color=\'red\'>Upload Failed</font>");
					}
				}
				$("#mulitplefileuploader").uploadFile(settings);
			});
		</script>
	';
?>
	</body>
</html>