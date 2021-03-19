<?php
	define('ABSDIR','/var/www/html/data/');
	define('WEB','http://proofexpress.spartannash.com');
	define('OWN','apache:apache');
	define('GS_PATH','/usr/bin/');
	define('PDFINFO_PATH','/usr/bin/');
	define('MAILUSER','ProofExpress@SpartanNash.com');
	define('MAILHOST','spmta1.spartanstore.com');
	define('MAILFROM','ProofExpress@SpartanNash.com');
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
	function archiveName($zid) {
		$con=mysqli_connect("localhost","spartannash","LnZj8QGKExcv7qvP","qp") or die("Cannot connect to database!");
		$zQuery=mysqli_query($con,"SELECT * FROM ads WHERE folder = ".$zid);
		$zname='';
		while($z=mysqli_fetch_assoc($zQuery)){
			$zname=$z["name"];
		}
		return $zname;
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
	function exec_timeout($cmd, $timeout) {
		// File descriptors passed to the process.
		$descriptors = array(
			0 => array('pipe', 'r'),  // stdin
			1 => array('pipe', 'w'),  // stdout
			2 => array('pipe', 'w')   // stderr
		);

		// Start the process.
		$process = proc_open('exec ' . $cmd, $descriptors, $pipes);

		if (!is_resource($process)) {
			throw new \Exception('Could not execute process');
		}

		// Set the stdout stream to none-blocking.
		stream_set_blocking($pipes[1], 0);

		// Turn the timeout into microseconds.
		$timeout = $timeout * 1000000;

		// Output buffer.
		$buffer = '';

		// While we have time to wait.
		while ($timeout > 0) {
			$start = microtime(true);

			// Wait until we have output or the timer expired.
			$read  = array($pipes[1]);
			$other = array();
			stream_select($read, $other, $other, 0, $timeout);

			// Get the status of the process.
			// Do this before we read from the stream,
			// this way we can't lose the last bit of output if the process dies between these functions.
			$status = proc_get_status($process);

			// Read the contents from the buffer.
			// This function will always return immediately as the stream is none-blocking.
			$buffer .= stream_get_contents($pipes[1]);

			if (!$status['running']) {
			// Break from this loop if the process exited before the timeout.
				$buffer='GS_OK';
				break;
			}

			// Subtract the number of microseconds that we waited.
			$timeout -= (microtime(true) - $start) * 1000000;
		}

		// Check if there were any errors.
		$errors = stream_get_contents($pipes[2]);

		if (!empty($errors)) {
			throw new \Exception($errors);
		}

		// Kill the process in case the timeout expired and it's still running.
		// If the process already exited this won't do anything.
		proc_terminate($process, 9);

		// Close all streams.
		fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);

		proc_close($process);

		return $buffer;
	}
?>