<?php
	include('../includes/inc.php');
	
	$pid=$_POST["pid"];
	if(isset($_POST["fid"])){
		$fid=$_POST["fid"];
		$folder=true;
	} else {
		$folder=false;
	}
	if(isset($_POST["zid"])){
		$zid=$_POST["zid"];
		$arch=true;
	} else {
		$arch=false;
	}
	$aid=$_POST["aid"];
	$pg=$_POST["pg"];
	$rev=$_POST["r"];
	$tz=$_POST["tz"];
	$uid=$_POST["uid"];
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
		$revMax=$page["rev"];
		if($page["name"]==$pg) {
			echo '
	<div class="panel panel-default">
		<div class="panel-active" data-toggle="tooltip" title="Latest version: '.timestamp($page["last_modify"],$tz).'">
			';
			$aQuery=mysqli_query($con,"SELECT text,hl,user,created FROM annotation WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$rev."' UNION ALL SELECT notes,hl,user,created FROM notes WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$rev."' ORDER BY created");
			$aCount=mysqli_num_rows($aQuery);
			if($page["status"]=='Approved') {
				echo '
				<h4 class="panel-title"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].'<span class="glyphicons glyphicons-ok-sign" style="float:right;" data-toggle="tooltip" title="Approved"></span></h4>
				';
			} else {
				echo '
				<h4 class="panel-title"><span class="badge" style="color:yellow;float:left;">'.$page["rev"].'</span>'.$page["name"].'</h4>
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
  			if($page["rev"]==$rev && $page["status"]!=='Approved' && $_POST["u"]!=="e26026b73cdc3b59012c318ba26b5518" && $arch!==true){
  				echo '				
  				<div class="row">
  					<textarea id="noteBox" placeholder="New page note" style="width:265px;" rows="5" oninput="unlockNoteButton()"></textarea>
  					<button id="updateNote" type="button" class="btn btn-primary btn-sm disabled" style="margin-bottom:15px;">Add Note</button>
  				</div>
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
		<div class="panel-heading" data-toggle="tooltip" title="Latest version: '.timestamp($page["last_modify"],$tz).'">
			<h4 class="panel-title">
			';
			$aQuery=mysqli_query($con,"SELECT text,hl,user,created FROM annotation WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$page["rev"]."' UNION ALL SELECT notes,hl,user,created FROM notes WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$page["name"]."' AND rev = '".$page["rev"]."' ORDER BY created");
			$aCount=mysqli_num_rows($aQuery);
			$pageURL='ad.php?p='.$pid;
			if($arch==true){
				$pageURL.='&z='.$zid;
			}
			if($folder==true){
				$pageURL.='&f='.$fid;
			}
			$pageURL.='&a='.$aid.'&qp='.$page["name"].'&r='.$page["rev"];
			
//			Process 'viewlog' field to find out if the page has been viewed by this user.
			$viewLog=explode(":",$page["viewlog"]);
			$star='<span class="glyphicons glyphicons-star" style="color:yellow;float:right;"></span>';
			foreach($viewLog as $view){
				if($view=='') { break; }
				$userLog=preg_match("/u".$uid."_/",$view);
				if($userLog==1 && substr(strrchr($view, "_"),1)==$revMax){
					$star='';
					break;
				}
			}
			if($page["status"]=='Approved') {
    			echo'
    			<a href="'.$pageURL.'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].$star.'<span class="glyphicons glyphicons-ok-sign" style="float:right;" data-toggle="tooltip" title="Approved"></span></a>
    			';
			} elseif($aCount == 0){
    			echo'
    			<a href="'.$pageURL.'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].$star.'</a>
    			';
    		} else {
    			echo'
    			<a href="'.$pageURL.'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$page["name"].$star.'&nbsp;<span class="label" style="float:right;"><span class="glyphicons glyphicons-comments"></span> '.$aCount.'</span></a>
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
</div>
	';		
?>	