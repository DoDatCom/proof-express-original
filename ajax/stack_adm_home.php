<?php
	echo '
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Updates <span class="badge"></span></a>
					</h4>
				</div>
				<div id="collapse4" class="panel-collapse collapse">
					<div class="panel-body">No pending updates.</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse6">Administration</a>
					</h4>
				</div>
				<div id="collapse6" class="panel-collapse collapse">
					<div class="panel-body">
						<ul class="list-group">';
	if($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9"){
		echo '				<li class="list-group-item"><a href="useradmin.php">Administer Users</a></li>';
	}
	echo '					<li class="list-group-item"><a href="preferences.php">My Preferences</a></li>
							<li class="list-group-item"><a href="logging.php">Site Logging</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	';
?>	