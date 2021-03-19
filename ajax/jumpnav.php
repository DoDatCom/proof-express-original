<?php session_start();

	include('../includes/inc.php');
	
	if($_POST["ring"]=="-") {
		echo '
<div class="col-sm-12">
	<ul class="pagination pagination-sm">
		<li>-</li>
	</ul>
</div>
		';
	} else {
		echo '
<div class="col-sm-12">
	<ul class="pagination pagination-sm">
		';
		if($_POST["ring"]=="admin"){
			$query=mysqli_query($con,"SELECT * from projects WHERE active = 1 ORDER BY name");
		} else {
			$ring=str_replace("p","",$_POST["ring"]);
			$prList=array_map('intval',explode(":",$ring));
			$prList=implode("','",$prList);
			$query=mysqli_query($con,"SELECT * FROM projects WHERE id IN ('".$prList."') AND active = 1 ORDER BY name");
		}
		$alpha=64;
		$marker=64;
		$first=false;
		while($row = mysqli_fetch_assoc($query)){
			if($first==false && ord($row["name"])<65){
				echo '
		<li><a href="#top">#</a></li>
				';
				$first=true;
			}
			if(ord($row["name"])>$marker){
				$marker=ord($row["name"]);
				$alpha++;
				while($marker>$alpha){
					echo '<li class="disabled"><a href="#'.chr($alpha).'">'.chr($alpha).'</a></li>';
					$alpha++;
				}
				echo '
		<li><a href="#'.chr($marker).'">'.chr($marker).'</a></li>
				';
			}
		}
		while($alpha<90){
			$alpha++;
			echo '<li class="disabled"><a href="#'.chr($alpha).'">'.chr($alpha).'</a></li>';
		}
	}
	echo '
	</ul>
</div>
	';
?>