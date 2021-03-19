<?php session_start();
	
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) { header("Location: ".WEB); }
	
	if(isset($_GET["p"]) && isset($_GET["f"])) {
		$pid=$_GET["p"];
		$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$pid);
		while($row=mysqli_fetch_assoc($pQuery)) { $pname=$row["name"]; }
		$fid=$_GET["f"];
		$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$fid);
		while($row=mysqli_fetch_assoc($fQuery)) { $fname=$row["name"]; }
	} else {
		header("Location: ".WEB."/home.php"); 
	}
	if(isset($_GET["z"])) {
		$zid=$_GET["z"];
		$zQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$zid);
		while($row=mysqli_fetch_assoc($zQuery)) { $zname=$row["name"]; }
	} else {
		$zid=0;
	}
	
?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
<?php echo '<body onload="loadPage('.$pid.','.$zid.','.$fid.',\''.$_COOKIE["bnw"].'\')" onresize="scrollheight()">'; ?>

<?php 
	$title="Ad Folders";
	include "includes/navbar.php";
?>
		
		<div style="height:118px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div id="stack" class="col-sm-3" style="overflow-y:auto;"></div>
			<div class="col-sm-9">
				<div class="row">
					<div class="col-sm-6" style="text-align:left;">
						<ul class="breadcrumb">
							<li><a href="home.php">Return to Project List</a></li>
<?php
	echo '					<li><a href="project.php?p='.$pid.'">'.$pname.'</a></li>';
	if($zid>0) { echo '		<li><a href="archive.php?p='.$pid.'&z='.$zid.'">'.$zname.'</a></li>'; }
	echo '					<li class="active">'.$fname.'</li>';
?>
						</ul>
					</div>
<?php
	if($zid==0) {
		if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
			echo '	<div class="col-sm-6" style="text-align:right;">
						<button type="button" class="btn btn-info" onclick="newAd()">New Ad</button>
					</div>';
		}
	}
?>
				</div>
				<div id="windowBody"></div>
			</div>
		</div>
		<div class="modal fade" id="moveAdModal" role="dialog">
			<div style="height:120px;"></div>
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 id="moveAdName" class="modal-title"></h4>
					</div>
					<div id="moveAdBody" class="modal-body"></div>
					<div class="modal-footer">
						<div id="moveAdDestName" style="float:left;"></div>
						<input id="moveAdSrcID" type="hidden">
						<input id="moveAdDestID" type="hidden">
						<input id="moveAdDestURL" type="hidden">
<?php echo '			<input id="moveAdUser" type="hidden" value="'.$_COOKIE["user"].'">
						<button type="button" class="btn btn-success" data-dismiss="modal" onclick="moveAd('.$pid.')">Move Ad</button>'; ?>
					</div>
				</div>
			</div>
		</div>
<?php 
	if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
		echo '
		<script>
			function stackProjects(){
				var xmlhttp;
				var params = "p='.$pid.'&bnw='.$_COOKIE["bnw"].'&tz='.$_COOKIE["tz"].'";
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("stack").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/stack_adm_projects.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
		';
	} elseif($_COOKIE["bnw"]=="63bcabf86a9a991864777c631c5b7617" || $_COOKIE["bnw"]=="3cd38ab30e1e7002d239dd1a75a6dfa8" || $_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518") {
		echo '
		<script>
			function stackProjects(){
				var xmlhttp;
				var params = "p='.$pid.'&tz='.$_COOKIE["tz"].'";
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("stack").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/stack_gen_projects.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
		';
	}
	echo '
		<script>
			function loadPage(p,z,f,b){
				windowheight();
				stackProjects(p);
				folderTable(p,z,f,b,"'.$_COOKIE["tz"].'");
			}
			function newAd(){
				var adname = prompt("Please enter a folder name for this ad:");
				switch(adname) {
					case \'\':
						alert("Sorry. Folder name cannot be blank.");
						return;
					case null:
						return;
					default:
						var xmlhttp;
						var params = "pid='.$pid.'&fid='.$fid.'&aname=" + adname + "&tz='.$_COOKIE["tz"].'";
						xmlhttp=new XMLHttpRequest();
						xmlhttp.onreadystatechange = function() {
							if (xmlhttp.readyState == 4) {
								var resp=xmlhttp.responseText;
								if(resp.substr(0,2)!=="OK"){
									window.alert(resp.substr(3));
									window.location.reload(true);
									return;
								} else {
									window.location.href = "'.WEB.'/upload.php?p='.$pid.'&f='.$fid.'&a=" + resp.substr(3);
								}
							}
						};
						xmlhttp.open("POST", "ajax/checkadname.php", true);
						xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xmlhttp.send(params);
				}
			}
		</script>
	';
?>	
		<script>
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
			function downloadFolder(p,url,u,b){
				var xmlhttp;
				var params = 'pid=' + p + url + '&u=' + u + '&b=' + b;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.responseText=="EMPTY"){
							window.alert("The selected folder contains no files.");
						} else {
				//			window.alert(xmlhttp.responseText);
							window.location.href = xmlhttp.responseText;
						}
					}
				};
				xmlhttp.open("POST", "ajax/downloadzip.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function changeDueDate(i,p,w,u){
				var d = prompt("Please enter a new due date:");
				switch(d) {
					case '':
						alert("Sorry. Date cannot be blank.");
						window.location.reload(true);
						return;
					case null:
						window.location.reload(true);
						return;
				}
				var xmlhttp;
				var params = 'i=' + i + '&p=' + p + '&w=' + w + '&d=' + d + '&u=' + u;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						window.location.reload(true);
					}
				};
				xmlhttp.open("POST", "ajax/changeduedate.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function renameFolder(t,url,u){
				var n = prompt("Please enter a new " + t + " name:");
				if(n==null || n==""){
					return;
				}
				switch(n) {
					case '':
						alert("Sorry. The " + t + " name cannot be blank.");
						window.location.reload(true);
						break;
					case null:
						break;
				}
				var xmlhttp;
				var params = 't=' + t + url + '&n=' + n + '&u=' + u;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						window.location.reload(true);
					}
				};
				xmlhttp.open("POST", "ajax/renamefolder.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function moveAdModal(pid,fid,aid,aname,uid){
				var xmlhttp;
				var params = 'pid=' + pid + '&fid=' + fid;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("moveAdName").innerHTML = '<span class="glyphicons glyphicons-map-marker" style="color:#31B44C;"></span>&nbsp;Move ' + aname;
						document.getElementById("moveAdBody").innerHTML = xmlhttp.responseText;
						document.getElementById("moveAdSrcID").value = aid;
						$("#moveAdModal").modal();
					}
				};
				xmlhttp.open("POST", "ajax/modal_movead.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function moveAd(pid){
				var sid = document.getElementById("moveAdSrcID").value;
				var url = document.getElementById("moveAdDestURL").value;
				var u = document.getElementById("moveAdUser").value;
				var xmlhttp;
				var params = 'pid=' + pid + url + '&sa=' + sid + '&u=' + u;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						window.alert(xmlhttp.responseText);
						window.location.reload(true);
					}
				};
				xmlhttp.open("POST", "ajax/movead.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function deleteFolder(i,p,w,u){
				if(window.confirm("You are about to delete the " + w + " folder.\nAre you sure?")){
					var xmlhttp;
					var params = 'i=' + i + '&p=' + p + '&w=' + w + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.location.reload(true);
						}
					};
					xmlhttp.open("POST", "ajax/deletefolder.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function folderTable(p,z,f,b,tz){
				var xmlhttp;
				var params = 'p=' + p + '&z=' + z + '&f=' + f + '&b=' + b + '&tz=' + tz;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("windowBody").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/foldertable.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function setAdDest(fid,url,c,fname){
				document.getElementById("moveAdDestURL").value = url;
				document.getElementById("moveAdDestID").value = fid;
				document.getElementById("moveAdDestName").innerHTML = '<span class="glyphicons glyphicons-folder-open" style="color:' + c + ';padding:2px;"></span>&nbsp;' + fname;
			}
			function setFolderDest(fid,c,fname){
				document.getElementById("moveFolderDestURL").value = url;
				document.getElementById("moveFolderDestID").value = fid;
				document.getElementById("moveFolderDestName").innerHTML = '<span class="glyphicons glyphicons-folder-open" style="color:' + c + ';padding:2px;"></span>&nbsp;' + fname;
			}
		</script>
	</body>
</html>
