<?php session_start();

	include('includes/inc.php');
	
	// Redirect to the home page if proper admin credentials are not present
	if(!isset($_COOKIE["bnw"])) { header("Location: ".WEB."/index.php"); }
	if(isset($_COOKIE["bnw"]) && $_COOKIE["bnw"]!=='2c1743a391305fbf367df8e4f069f9f9') { header("Location: ".WEB."/index.php"); }

?>

<!DOCTYPE html>
<html>
	<head>
		<title>SpartanNash Proof Express | Orphan Search</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="icon" href="favicon.png" type="image/png" sizes="16x16"/>
		<link rel="apple-touch-icon-precomposed" href="appletouch.png" type="image/png" sizes="152x152"/>
		<link rel="stylesheet" href="css/uploadfile.css">
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/dodat.css">
		<link rel="stylesheet" href="css/glyphicons.css">
		<link rel="stylesheet" href="css/calendar.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script src="js/quickproof.js"></script>
		<script src="js/sn.js"></script>
		<script src="js/tooltip.js"></script>
		<script src="js/jquery.uploadfile.min.js"></script>
		<style type="text/css" media="screen">
			body { background:#eee; margin:1em; text-align:center; }
			canvas { display:block; margin:1em auto; background:#fff; border:1px solid #ccc }
		</style>
	</head>
	<script>
        var source = 'THE SOURCE';
        function start_task(src,id,stp,log,prog){
			source = new EventSource(src);
             
            //a message is received
			source.addEventListener('message' , function(e){
				var result = JSON.parse( e.data );
				add_log(result.message,log);
				if(result.progress!=="-"){
					document.getElementById(prog).style.width = result.progress + "%";
					document.getElementById(prog).innerHTML = result.progress + "%";
				}
				if(result.eta!=="-"){
					document.getElementById('eta').innerHTML = result.eta;
				}
				if(e.data.search('TERMINATE') != -1){
					add_log('Received TERMINATE closing');
					document.getElementById(prog).className = "progress-bar";
					source.close();
				}
				if(result.message=="PROCESS ENDED"){
					document.getElementById(prog).className = "progress-bar";
					document.getElementById(id).style.visibility = "hidden";
					source.close();
				}
			});
             
			source.addEventListener('error' , function(e){
				document.getElementById(prog).className = "progress-bar";
				document.getElementById(id).className = "btn btn-success";
                 
				//kill the object ?
				source.close();
			});
		}
         
        function stop_task(prog){
        	document.getElementById(prog).className = "progress-bar";
			source.close();
			add_log('STOPPED BY USER');
		}
         
		function add_log(message,log){
			var r = document.getElementById(log);
			r.innerHTML += message + '<br/>';
			r.scrollTop = r.scrollHeight;
		}
		
		function textLog(text){
			var log = document.getElementById(text).innerHTML;
			var xmlhttp;
			var params = 't=' + log;
			xmlhttp=new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4) {
					window.alert(xmlhttp.responseText);
				}
			};
			xmlhttp.open("POST", "ajax/textlog.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send(params);
		}
		
		function downloadInnerHtml(filename, elId, mimeType) {
			var elHtml = document.getElementById(elId).innerHTML;
			elHtml = elHtml.split('<br>').join('\n');
			var link = document.createElement('a');
			mimeType = mimeType || 'text/plain';

			link.setAttribute('download', filename);
			link.setAttribute('href', 'data:' + mimeType  +  ';charset=utf-8,' + encodeURIComponent(elHtml));
			document.body.append(link);
			link.click(); 
			document.body.removeChild(link);
		}
		
		function timeConverter(UNIX_timestamp){
			var a = new Date(UNIX_timestamp);
			var year = a.getFullYear();
			var month = '0' + (a.getMonth() + 1);
			month = month.substr(-2);
			var date = '0' + a.getDate();
			date = date.substr(-2);
			var hour = '0' + a.getHours();
			hour = hour.substr(-2);
			var min = '0' + a.getMinutes();
			min = min.substr(-2);
			var sec = '0' + a.getSeconds();
			sec = sec.substr(-2);
			var time = year + month + date + '_' + hour + min + sec ;
			return time;
		}

		var fileName =  'log_' + timeConverter(Date.now()) + '.txt'; // You can use the .txt extension if you want
	</script>
	<body>
		<h3>Orphan Search</h3>
		<div class="row">
			<div class="col-sm-2">
				<a type="button" class="btn btn-info" href="home.php">Return to Proof Express</a>
			</div>
			<div class="col-sm-8">
				<div class="well">
					<p style="text-align:left">This application will search for missing pages in Proof Express.</p>
				</div>
			</div>
			<div class="col-sm-2"></div>
		</div>
		<div id="step1" class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="well">
					<div class="row">
						<div class="col-sm-6">
							<button id="btn1" type="button" class="btn btn-info" onclick="start_task('ajax/ajax_orphan.php','btn1','step1','log1','prog1')">Convert DB</button>
							<a href="#" id="downloadLink" class="btn btn-info" role="button">Export Log</a>
<!--						<button id="txtlog3" type="button" class="btn btn-default" onclick="textLog('log3')">Export Log</button> -->
							<button id="stop" type="button" class="btn btn-danger" onclick="stop_task('prog1')">Cancel</button>
						</div>
						<div class="col-sm-6">
							<div id="eta" style="background-color:#fff;"></div>
							<div class="progress">
								<div id="prog1" class="progress-bar progress-bar-striped active" role="progressbar" style="width:0%"></div>
							</div>
							<div id="log1" style="max-height:200px;text-align:left;overflow:scroll;background-color:#fff;"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
		</div>
		<script>
			$('#downloadLink').click(function(){
				downloadInnerHtml(fileName, 'log1','text/html');
			});
		</script>
	</body>
</html>