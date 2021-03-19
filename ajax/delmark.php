<?php
	session_start();
	include('../includes/inc.php');
	$pid=$_POST["p"];
	$w=$_POST["w"];
	$r=$_POST["r"];
	$qp=$_POST["qp"];
	$m=$_POST["m"];
	
	$query="SELECT * FROM annotation WHERE project = ".$pid." AND wk = ".$w." AND rev = ".$r." AND page = '".$qp."' ORDER BY mark ASC";
	$result=mysqli_query($con,$query);
	if(mysqli_num_rows($result)>0) {
		$i = 0;
		while($row = mysqli_fetch_assoc($result)){
  			$id = $row["id"];
  			$mark = $row["mark"];
  			if($mark==$m){
  				$del=mysqli_query($con,"DELETE FROM annotation WHERE id = ".$id) or die(mysqli_error($con));
//  				$m=200;
  			} else {
  				$del=mysqli_query($con,'UPDATE annotation SET mark = '.$i.', modified='.date("U",strtotime("now")).' WHERE id = '.$id);
  				$i++;
  			}
  		}
  		$resp="Mark removed.";
  		
  		$naCount=mysqli_query($con,"SELECT comments FROM ads WHERE id = ".$w);
		while($a=mysqli_fetch_assoc($naCount)) { $aCt=$a["comments"] - 1; }
		$newACt=mysqli_query($con,"UPDATE ads SET comments = ".$aCt." WHERE id = ".$w);
	
		$npCount=mysqli_query($con,"SELECT comments FROM projects WHERE id = ".$pid);
		while($p=mysqli_fetch_assoc($npCount)) { $pCt=$p["comments"] - 1; }
		$newPCt=mysqli_query($con,"UPDATE projects SET comments = ".$pCt." WHERE id = ".$pid);
  	}
  	
//	Send server response (if any)
	if($resp){
		echo $resp;
	}
?>