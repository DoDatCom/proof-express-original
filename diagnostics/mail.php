<?php
	include('../includes/inc.php');
?>
<html>
	<head>
		<title>HTML email test</title>
	</head>
	<body>
		<p>HTML email test</p>
		<input id="to" type="text" placeholder="Enter recipient" />
		<button onclick="test()">Click to send test</button>
		<p id="response"></p>
		<script>
			function test(){
				var xmlhttp;
				var params = 'to=' + document.getElementById("to").value;
				xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						document.getElementById("response").value = xmlhttp.responseText;
					}
				};
				xmlhttp.open("POST", "../ajax/mailtest.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}
		</script>
	</body>
</html>