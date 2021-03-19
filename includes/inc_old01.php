<?php
	define('ABSDIR','/var/www/html/data/');
	define('WEB','http://spartanc1.spartanstore.com');
	define('MAILUSER','');
	define('MAILHOST','spmta1.spartanstore.com');
	define('MAILFROM','support@spartanstore.com');
	$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
	
	function timestamp($t,$z) {
		date_default_timezone_set($z);
		if(preg_match("/:/",$t)){
			$localtz = strftime("%m/%d/%Y %l:%M%p %Z",strtotime($t));
		} else {
			$localtz = strftime("%m/%d/%Y %l:%M%p %Z",strtotime("@".$t));
		}
		return $localtz;
	}
	function logstamp($t,$z) {
		date_default_timezone_set($z);
		if(preg_match("/:/",$t)){
			$localtz = strftime("%m/%d/%Y",strtotime($t))."<br/>".strftime("%l:%M%p %Z",strtotime($t));
		} else {
			$localtz = strftime("%m/%d/%Y",strtotime("@".$t))."<br/>".strftime("%l:%M%p %Z",strtotime("@".$t));
		}
		return $localtz;
	}
	function projectName($pid) {
		$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
		$pQuery=mysqli_query($con,"SELECT * FROM projects WHERE id = ".$pid);
		$pname='';
		while($p=mysqli_fetch_assoc($pQuery)){
			$pname=$p["name"];
		}
		return $pname;
	}
	function adName($aid) {
		$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
		$aQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$aid);
		$aname='';
		while($a=mysqli_fetch_assoc($aQuery)){
			$aname=$a["name"];
		}
		return $aname;
	}
	function folderName($fid) {
		$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
		$fQuery=mysqli_query($con,"SELECT * FROM ads WHERE id = ".$fid);
		$fname='';
		while($f=mysqli_fetch_assoc($fQuery)){
			$fname=$f["name"];
		}
		return $fname;
	}
	function subFolderName($fzid) {
		$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
		$fzQuery=mysqli_query($con,"SELECT * FROM ads WHERE folder = ".$fzid);
		$fzname='';
		while($fz=mysqli_fetch_assoc($fzQuery)){
			$fzname=$fz["name"];
		}
		return $fzname;
	}
	function userName($uid) {
		$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
		$uQuery=mysqli_query($con,"SELECT * FROM users WHERE id = ".$uid);
		while($u=mysqli_fetch_assoc($uQuery)){
			$uname=$u["username"];
		}
		return $uname;
	}
	function userID($uname) {
		$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
		$nQuery=mysqli_query($con,"SELECT * FROM users WHERE username = '".$uname."'");
		while($n=mysqli_fetch_assoc($nQuery)){
			$nid=$n["id"];
		}
		return $nid;
	}
?>
