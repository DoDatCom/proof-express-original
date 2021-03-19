<?php
	include('../includes/inc.php');
	$pid=$_POST["pid"];
	$aid=$_POST["aid"];
	$folder=$_POST["f"];
	$pg=$_POST["pg"];
	$rev=$_POST["rev"];
	$tz=$_POST["tz"];
	$pageQuery=mysqli_query($con,"SELECT * FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND status != 'Complete' ORDER BY name");
	
	$stringIN=array("\"","\n");
	$stringOUT=array("\\\"","<br/>");
	$stringTXT=array("\\\"","\\r");
	
	echo '
<div class="panel-group" id="prooflist">
	';
	
	$i=1;
	$pdfstr=array(".JPG",".jpg");
	$pdfrep=array("","");
	
	while($page=mysqli_fetch_assoc($pageQuery)) { //foreach($projectArray as $proof){
		if($page["name"]==$pg) {
			echo '
	<div class="panel panel-default">
		<div class="panel-active">
			';
			$aQuery=mysqli_query($con,"SELECT text,hl,user,created FROM annotation WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$rev."' UNION ALL SELECT notes,hl,user,created FROM notes WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$rev."' ORDER BY created");
			$aCount=mysqli_num_rows($aQuery);
			if($page["status"]=='Approved') {
				echo '
				<h4 class="panel-title"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].'<span class="glyphicons glyphicons-ok-sign" style="float:right;" data-toggle="tooltip" title="Approved"></span></h4>
				';
			} else {
				echo '
				<h4 class="panel-title"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].'</h4>
				';
			}
			echo '
		</div>
		<div id="'.$page["name"].'" class="panel-collapse collapse in" aria-expanded="true">
			<div class="panel-body">';
			while($aText = mysqli_fetch_assoc($aQuery)){
				echo '
				<p style="background-color:rgba('.$aText["hl"].',0.5);text-align:left;padding:0px 5px;">'.str_replace($stringIN,$stringOUT,$aText["text"]).'<br/><small><i>'.$aText["user"].' - '.timestamp($aText["created"],$tz).'</i></small></p>
				';
  			}
  			echo '
  			</div>
		</div>
	</div>
			';
		} else {
			echo '
	<div class="panel panel-default">	
		<div class="panel-heading">
			<h4 class="panel-title">
			';
			$aQuery=mysqli_query($con,"SELECT text,hl,user,created FROM annotation WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$page["rev"]."' UNION ALL SELECT notes,hl,user,created FROM notes WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$page["rev"]."' ORDER BY created");
			$aCount=mysqli_num_rows($aQuery);
			if($page["status"]=='Approved') {
    			echo'
    			<a href="arcwk.php?p='.$pid.'&a='.$aid.'&f='.$folder.'&qp='.$page["name"].'&r='.$page["rev"].'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].'<span class="glyphicons glyphicons-ok-sign" style="float:right;" data-toggle="tooltip" title="Approved"></span></a>
    			';
			} elseif($aCount == 0){
    			echo'
    			<a href="arcwk.php?p='.$pid.'&a='.$aid.'&f='.$folder.'&qp='.$page["name"].'&r='.$page["rev"].'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].'</a>
    			';
    		} else {
    			echo'
    			<a href="arcwk.php?p='.$pid.'&a='.$aid.'&f='.$folder.'&qp='.$page["name"].'&r='.$page["rev"].'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].'&nbsp;<span class="label" style="float:right;"><span class="glyphicons glyphicons-comments"></span> '.$aCount.'</span></a>
    			';
    		}
    		echo '
    		</h4>
		</div>
	</div>
			';
		}
		$i++;
	}
	echo '
</div>
	';		
?>	