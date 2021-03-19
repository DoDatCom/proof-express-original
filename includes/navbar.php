<nav class="navbar navbar-default navbar-fixed-top">
<?php
	echo '
	<div style="background-color:#9CD07D;">
		<a href="'.WEB.'"><img src="images/SN_logo_97.png" style="background-color:#fff;float:left;"/></a>
		<span style="padding:36px 36px 0px;font-size:40px;color:#007054;float:right;"><b>'.$title.'</b></span></div>';
?>
	<div class="navbar-spacer" style="background-color:#9CD07D;"></div>
	<div class="container" style="background-color:#007054;width:100%;height:30px;">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>                        
			</button>
<?php echo '<a class="navbar-brand" href="'.WEB.'"><i>Proof Express</i></a>'; ?>
		</div>
		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="home.php">PROJECTS</a></li>
				<li><a href="userguide.php">USER GUIDE</a></li>
				<li><a href="techsupport.php">TECH SUPPORT</a></li>
				<li><a href="index.php?logout=true">LOG OUT</a></li>
			</ul>
		</div>
	</div>
</nav>