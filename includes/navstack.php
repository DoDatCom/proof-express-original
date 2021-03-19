<div id="navstack" class="col-sm-3" style="overflow-y:auto;">
	<div class="panel-group" id="accordion">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Approvals <span class="badge"></span></a>
				</h4>
			</div>
			<div id="collapse1" class="panel-collapse collapse">
				<div class="panel-body">No pending approvals.</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Revisions <span class="badge">1</span></a>
				</h4>
			</div>
			<div id="collapse2" class="panel-collapse collapse">
				<div class="panel-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>User</th>
								<th>Date/Time</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td align="left">dwilkins</td>
								<td align="left"><?php echo date("n/j/y g:iA T",strtotime("February 10, 2017 3:05PM")); ?></td>
							</tr>
						</tbody>
					</table>
					<p class="updatetext">Updated: <?php echo date("g:iA T",strtotime("now")); ?>&nbsp;<button type="button" class="btn btn-info btn-xs" style="float:right;"><span class="glyphicon glyphicon-refresh"></span> Refresh</button></p>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Download History</a>
				</h4>
			</div>
			<div id="collapse3" class="panel-collapse collapse">
				<div class="panel-body">No downloads.</div>
			</div>
		</div>
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
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse5">Self-Alerts <span class="badge"></span></a>
				</h4>
			</div>
			<div id="collapse5" class="panel-collapse collapse">
				<div class="panel-body">No self-alerts.</div>
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
					<ul class="list-group">
						<li class="list-group-item"><a href="#">Edit Site Security</a></li>
						<li class="list-group-item"><a href="#">Administer Users</a></li>
						<li class="list-group-item"><a href="#">Administer Rights</a></li>
						<li class="list-group-item"><a href="#">My Preferences</a></li>
						<li class="list-group-item"><a href="#">Attentions Admin</a></li>
						<li class="list-group-item"><a href="#">Site Admin</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse7">Support Requests <span class="badge"></span></a>
				</h4>
			</div>
			<div id="collapse7" class="panel-collapse collapse">
				<div class="panel-body">No support requests.</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse8">Download Basket <span class="badge"></span></a>
				</h4>
			</div>
			<div id="collapse8" class="panel-collapse collapse">
				<div class="panel-body">Basket is empty.</div>
			</div>
		</div>
	</div>
</div>