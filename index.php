<?php session_start();

	include "includes/inc.php";
	
	$attempt=0;
	$logResponse="";
	
	if(isset($_GET["logout"]) && $_GET["logout"]=="true") {
		$result=mysqli_query($con,"UPDATE users SET status = '' WHERE sha256 = '".$_COOKIE["token"]."'");
		setcookie("user", "", 1);
		setcookie("session", "", 1);
		setcookie("bnw", "", 1);
		setcookie("ring", "", 1);
		setcookie("mktg", "", 1);
		setcookie("tz", "", 1);
		session_destroy();
		$logResponse="You have logged out successfully.";
	}
	
	if(!isset($_COOKIE["session"]) && isset($_COOKIE["token"])) {
		$result=mysqli_query($con,"UPDATE users SET status = '' WHERE sha256 = '".$_COOKIE["token"]."'");
		session_destroy();
	}
	
	if(isset($_COOKIE["session"])) {
		header("Location: ".WEB."/home.php");
		exit;
	}
	
	if(isset($_POST["u"]) && isset($_POST["p"])) {
		$result=mysqli_query($con,"SELECT * FROM users WHERE username = '".$_POST['u']."' AND sha256 = '".hash('sha256',$_POST['p'])."'");
		if(mysqli_num_rows($result)>0) {
			while($row = mysqli_fetch_assoc($result)){
				setcookie("user", $row["username"]);
				setcookie("session", $row["sha256"]);
				setcookie("token", $row["sha256"]);
				setcookie("bnw", $row["bnw"]);
				setcookie("mktg", $row["marketing"]);
				setcookie("tz",$row["tzone"]);
				if($row["ring"]=="") {
					setcookie("ring", "-");
				} else {
					setcookie("ring", $row["ring"]);
				}
				$login=mysqli_query($con,"UPDATE users SET status = 'Active', lastlogin = NOW() WHERE sha256 = '".hash('sha256',$_POST['p'])."'");
				$userLog=mysqli_query($con,"INSERT INTO user_log (id,user,activity,project,ad,page,rev,note,time) VALUES (null,'".$row["username"]."','Log In',0,0,0,0,'-',".date("U",strtotime("now")).")") or die(mysqli_errno($userLog));
			}
			header("Location: ".WEB."/index.php");
			exit;

  		} else {
  			$attempt=$_POST["a"]+1;
  			if($attempt>2){
  				$logResponse='<font color="red">User name/password incorrect.</font><br/><a href="'.WEB.'/forgot.php">Forgot password?</a>';
  			} else {
  				$logResponse='<font color="red">User name/password incorrect.</font>';
  			}
  		}
  	} else {
  		$attempt=0;
  		$logResponse='';
  	}

//	Redirect to login page if there is no SESSION data.
	
?>
<!DOCTYPE html>
<html lang="en">
	<!-- HTML Head -->
<?php include "includes/head.php"; ?>
	
	<body>

<?php 
	$title="Login";
	include "includes/navbar.php";
?>
		
		
		<div style="height:120px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->
		
		<div class="row">
			<div class="col-sm-4">
<?php
	if(preg_match("/edge/i",$_SERVER["HTTP_USER_AGENT"])) {
		echo '	<div class="well" style="background-color:#FFCD34;">
					<h2>SPECIAL NOTICE:</h2>
					<h4>Accessing ProofExpress using Microsoft Edge is not recommended.</h4>
					<p>Due to certain limitations in this browser, multiple page refreshes will be required in order to view preview images.</p>
				</div>';
	} elseif(preg_match("/trident/i",$_SERVER["HTTP_USER_AGENT"])) {
		echo '	<div class="well" style="background-color:#FFCD34;">
					<h2>SPECIAL NOTICE:</h2>
					<h4>Accessing ProofExpress using Internet Explorer is not recommended.</h4>
					<p>Due to certain limitations in this browser, multiple page refreshes will be required in order to view preview images.</p>
				</div>';
	}
?>
			</div>
			<div class="col-sm-4">
				<div class="panel panel-success">
					<div class="panel-heading">
						<h4 style="color:white;">ProofExpress Login</h4>
					</div>
					<div class="panel-body">
						<b>Enter your user name and password to continue:</b>
						<div class="row">
							<div class="col-sm-1"></div>
							<div class="col-sm-10">
								<form method="post" action="index.php">
									<div class="input-group">
										<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
										<input id="user" type="text" class="form-control" name="u" placeholder="User name">
									</div>
									<br/>
									<div class="input-group">
										<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
										<input id="password" type="password" class="form-control" name="p" placeholder="Password">
									</div>
<?php echo '						<input type="hidden" name="a" value="'.$attempt.'">
									<p align="center">'.$logResponse.'</p>';
?>
									<br/>
									<button type="submit" class="btn btn-default">Submit</button>
								</form>
							</div>
							<div class="col-sm-1"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4"></div>
		</div>
	</body>
</html>