<div class="panel-group" id="prooflist">
<?php
	$i=1;
	foreach($projectArray as $proof){
		if (preg_match("/.pdf/i", $proof)) {
    		echo '
    <div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#prooflist" href="#proof'.$i.'">'.$proof.'</a>
			</h4>
		</div>
		<div id="proof'.$i.'" class="panel-collapse collapse">
			<div class="panel-body">No pending updates.</div>
		</div>
	</div>
			';
			$i++;
		}
	echo '
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#prooflist" href="#null">Null</a>
			</h4>
		</div>
		<div id="null" class="panel-collapse collapse">
			<div class="panel-body">Failed</div>
		</div>
	</div>';
	}
?>
</div>