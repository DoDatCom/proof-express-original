<!DOCTYPE html>
<html lang="en">
<head>
	<title>Filetypes</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/glyphicons.css">
	<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<style type="text/css" media="screen">
		body { background:#eee; margin:1em; text-align:center; }
		canvas { display:block; margin:1em auto; background:#fff; border:1px solid #ccc }
	</style>
</head>
<body>
	<h2>Filetypes</h2>
	<table class="table table-bordered">
<?php
	$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","fonts") or die("Cannot connect to database!");
	$query=mysqli_query($con,"SELECT * FROM filetype");
	$i=0;
	while($row=mysqli_fetch_assoc($query)) {
		if($i % 5==0){ echo '<tr>'; }
		echo '<td><span class="filetypes '.$row["class"].'"></span>
				<p>'.$row["class"].'</p>
				<p>'.$row["friendly"].'</p>
			</td>';
		$i++;
		if($i % 5==0){ echo '</tr>'; }
	}
?>
	</table>
</body>
</html>