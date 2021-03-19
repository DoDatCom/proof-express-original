<?php session_start();
	include('includes/inc.php');
	if(!isset($_COOKIE["session"])) { header("Location: ".WEB); }
	
	
	if(isset($_GET["p"])) {
		$pid=$_GET["p"];
		$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$pid) or die("This project does not exist");
		while($p=mysqli_fetch_assoc($pQuery)) {
			$pname=$p["name"];
			$pDir=str_replace(' ','_',$pname);
		}
	}
	if(isset($_GET["z"])) {
		$zid=$_GET["z"];
		$zQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$zid) or die("This archive does not exist");
		while($z=mysqli_fetch_assoc($zQuery)) {
			$zname=$z["name"];
			$zDir=str_replace(' ','_',$zname);
		}
	}
	
?>

<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
<?php echo '<body onload="loadPage('.$pid.','.$zid.',\''.$_COOKIE["bnw"].'\')" onresize="scrollheight()">'; ?>

<?php 
	$title="Project Archive";
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
							<li><?php echo '<a href="project.php?p='.$pid.'">'.$pname.'</a>'; ?></li>
							<li class="active"><?php echo $zname; ?></li>
						</ul>
					</div>
				</div>
				<div id="windowBody"></div>
			</div>
		</div>
<!--		<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#calendar">Calendar Test</button> -->
		<div class="modal fade" id="calendar" role="dialog">
			<div style="height:120px;"></div>
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Calendar Test</h4>
					</div>
					<div class="modal-body">
						 <div class="month">
							<ul>
								<li class="prev"><span class="glyphicons glyphicons-chevron-left"></span></li>
								<li class="next"><span class="glyphicons glyphicons-chevron-right"></span></li>
								<li>July 2017</li>
							</ul>
						</div>
						<ul class="weekdays">
							<li>Su</li>
							<li>Mo</li>
							<li>Tu</li>
							<li>We</li>
							<li>Th</li>
							<li>Fr</li>
							<li>Sa</li>
						</ul>
						<ul class="days">
							<li></li>
							<li></li>
							<li></li>
							<li></li>
							<li></li>
							<li></li>
							<li>1</li>
							<li>2</li>
							<li>3</li>
							<li>4</li>
							<li>5</li>
							<li>6</li>
							<li>7</li>
							<li>8</li>
							<li>9</li>
							<li>10</li>
							<li>11</li>
							<li>12</li>
							<li>13</li>
							<li>14</li>
							<li>15</li>
							<li>16</li>
							<li>17</li>
							<li>18</li>
							<li>19</li>
							<li><span class="active">20</span></li>
							<li>21</li>
							<li>22</li>
							<li>23</li>
							<li>24</li>
							<li>25</li>
							<li>26</li>
							<li>27</li>
							<li>28</li>
							<li>29</li>
							<li>30</li>
							<li>31</li>
							<li></li>
							<li></li>
							<li></li>
							<li></li>
							<li></li>
						</ul> 
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
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
		<div class="modal fade" id="moveFolderModal" role="dialog">
			<div style="height:120px;"></div>
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 id="moveFolderName" class="modal-title"></h4>
					</div>
					<div id="moveFolderBody" class="modal-body"></div>
					<div class="modal-footer">
						<div id="moveFolderDestName" style="float:left;"></div>
						<input id="moveFolderSrcID" type="hidden">
						<input id="moveFolderDestID" type="hidden">
						<input id="moveFolderDestURL" type="hidden">
<?php echo '			<input id="moveFolderUser" type="hidden" value="'.$_COOKIE["user"].'">
						<button type="button" class="btn btn-success" data-dismiss="modal" onclick="moveFolder('.$pid.')">Move Folder</button>'; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="newFolderModal" role="dialog">
			<div style="height:120px;"></div>
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Create New Folder</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="newFolderName">Enter a name for the new folder:</label>
							<input type="text" class="form-control" id="newFolderName">
							<input type="hidden" id="newpid">
							<input type="hidden" id="newpname">
<?php echo '				<input type="hidden" id="newuid" value="'.$_COOKIE["user"].'">'; ?>
						</div>
						<div class="checkbox">
							<label><input id="newarch" type="checkbox" value="1">Set as archive folder</label>
						</div>
					</div>
					<div class="modal-footer">
<?php echo '			<button type="button" class="btn btn-default" data-dismiss="modal" onclick="newFolder()">Create Folder</button>'; ?>
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
				var params = "p='.$pid.'&tz='.$_COOKIE["tz"].'&bnw='.$_COOKIE["bnw"].'";
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
				var params = "p='.$pid.'&tz='.$_COOKIE["tz"].'&bnw='.$_COOKIE["bnw"].'";
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
			function loadPage(p,z,bnw){
				windowheight();
				stackProjects(p);
				archiveTable(p,z,"'.$_COOKIE["tz"].'","'.$_COOKIE["bnw"].'");
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
			function openNewModal(pid,pname){
				$("#newFolderModal").modal();
				document.getElementById("newpid").value = pid;
				document.getElementById("newpname").value = pname;
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
//						window.alert(xmlhttp.responseText);
					}
				};
				xmlhttp.open("POST", "ajax/renamefolder.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function moveAdModal(pid,aid,aname,uid){
				var xmlhttp;
				var params = 'pid=' + pid;
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
			function moveFolderModal(pid,fid,fname,uid){
				var xmlhttp;
				var params = 'pid=' + pid;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("moveFolderName").innerHTML = '<span class="glyphicons glyphicons-map-marker" style="color:#31B44C;"></span>&nbsp;Move ' + fname;
						document.getElementById("moveFolderBody").innerHTML = xmlhttp.responseText;
						document.getElementById("moveFolderSrcID").value = fid;
						$("#moveFolderModal").modal();
					}
				};
				xmlhttp.open("POST", "ajax/modal_movefolder.php", true);
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
			function moveFolder(pid){
				var sid = document.getElementById("moveFolderSrcID").value;
				var url = document.getElementById("moveFolderDestURL").value;
				var u = document.getElementById("moveFolderUser").value;
				var xmlhttp;
				var params = 'pid=' + pid + url + '&sf=' + sid + '&u=' + u;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						window.alert(xmlhttp.responseText);
						window.location.reload(true);
					}
				};
				xmlhttp.open("POST", "ajax/movefolder.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function deleteAd(p,a,n,u){
				if(window.confirm("You are about to delete the " + n + " ad.\nAre you sure?")){
					var xmlhttp;
					var params = 'p=' + p + '&a=' + a + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.location.reload(true);
						}
					};
					xmlhttp.open("POST", "ajax/deletead.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function deleteFolder(pid,fid,fname,u){
				if(window.confirm("You are about to delete the " + fname + " folder.\nAre you sure?")){
					var xmlhttp;
					var params = 'pid=' + pid + '&fid=' + fid + '&u=' + u;
					xmlhttp=new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4) {
							window.location.reload(true);
						}
					};
					xmlhttp.open("POST", "ajax/deletefolder_p.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send(params);
				}
			}
			function archiveTable(p,z,tz,bnw){
				var xmlhttp;
				var params = 'p=' + p + '&z=' + z + '&tz=' + tz + '&bnw=' + bnw;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("windowBody").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "ajax/archivetable.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
			function setAdDest(fid,url,c,fname){
				document.getElementById("moveAdDestID").value = fid;
				document.getElementById("moveAdDestURL").value = url;
				document.getElementById("moveAdDestName").innerHTML = '<span class="glyphicons glyphicons-folder-open" style="color:' + c + ';padding:2px;"></span>&nbsp;' + fname;
			}
			function setFolderDest(fid,url,c,fname){
				document.getElementById("moveFolderDestID").value = fid;
				document.getElementById("moveFolderDestURL").value = url;
				document.getElementById("moveFolderDestName").innerHTML = '<span class="glyphicons glyphicons-folder-open" style="color:' + c + ';padding:2px;"></span>&nbsp;' + fname;
			}
		</script>
	</body>
</html>