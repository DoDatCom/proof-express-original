<?php
/**
 * Proof Express Ad File
 * 
 * PHP version 7.1
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  ProofExpress
 * @package   PE
 * @author    David Wilkins <dwilkins@dodatcommunications.com>
 * @copyright 2016-2018 Dodat Communications
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PackageName
 */

session_start();

// Retrieve 'includes' info
require 'includes/inc.php';

// Initialize string filter and replacement variables
$dirIN=array(' ','#');
$dirOUT=array('_','');
$stringIN=array("\"","\n");
$stringOUT=array("\\\"","<br/>");
$stringTXT=array("\\\"","\\r");

if (isset($_GET["p"])) {
    // Set project folder variables
    $pid=$_GET["p"];
    $pname=projectName($pid);
    $updateURL='ajax/update.php?p='.$pid;
    $pageURL='ajax/addpage.php?p='.$pid;
    $reloadURL=WEB.'/ad.php?p='.$pid;
}
// Set archive variables (if necessary)
if (isset($_GET["z"])) {
    $zid=$_GET["z"];
    $zname=archiveName($zid);
    $updateURL.='&z='.$zid;
    $pageURL.='&z='.$zid;
    $reloadURL.='&z='.$zid;
    $arch=true;
} else {
    $arch=false;
}
// Set subfolder variables (if necessary)
if (isset($_GET["f"])) {
    $fid=$_GET["f"];
    $fname=folderName($fid);
    $updateURL.='&f='.$fid;
    $pageURL.='&f='.$fid;
    $reloadURL.='&f='.$fid;
    $folder=true;
} else {
    $folder=false;
}
// Set ad folder variables
$jsVar='';
$markVar='';
$blankText='';
if (isset($_GET["a"])) {
    $aid=$_GET["a"];
    $aname=adName($aid);
    $updateURL.='&a='.$aid;
    $pageURL.='&a='.$aid.'&u='.$_COOKIE["user"];
    $reloadURL.='&a='.$aid;

    // Retrieve revision number of current page
    if (isset($_GET["pg"])) {
        $pgid=$_GET["pg"];
        $rQuery=mysqli_query($con, "SELECT * FROM pages WHERE id = ".$pgid);
        if (mysqli_num_rows($rQuery)==0) {
            $rev=0;
            $pgName='-';
            $pgid=0;
            $rMax=0;
            $status='EMPTY';
            $hr="-";
            $hrType="-";
        } else {
            while ($r=mysqli_fetch_assoc($rQuery)) {
                $rev=$r["rev"];
                $pgName=$r["name"];
                $status=$r["status"];
                if (!empty($r["hrtype"])) {
                    $hr=strtoupper($r["hrtype"]);
                    $hrType=$r["hrtype"];
                }

                // Fallback in case IMG file doesn't appear in the IMG folder
                if (!is_file(ABSDIR.'proofs/IMG/'.$pgid.'.jpg')) {
                    $jsVar='proof.src = "images/logos/pdf_logo.jpg";';
                } else {
                    $jsVar='proof.src = "proofs/IMG/'.$pgid.'.jpg";';
                }

                // Get total number of revisions for this page
                $revQuery=mysqli_query($con, "SELECT MAX(rev) FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND name = '".$pgName."'");
                $revResult=mysqli_fetch_array($revQuery);
                $rMax=$revResult[0];

                $mQuery=mysqli_query($con, "SELECT * FROM annotation WHERE project = '".$pid."' AND wk = '".$aid."' AND page = '".$pgName."' AND rev = ".$rev);
                if (mysqli_num_rows($mQuery)>0) {
                    $i = 0;
                    while ($m = mysqli_fetch_assoc($mQuery)) {
                        if (!$m["hl"]) {
                            $hlColor="255,255,0";
                        } else {
                            $hlColor=$m["hl"];
                        }
                        $markVar .= 'hlx['.$i.'] = '.$m["hlx"].'; hly['.$i.'] = '.$m["hly"].'; hlw['.$i.'] = '.$m["hlw"].'; hlh['.$i.'] = '.$m["hlh"].'; hlText['.$i.'] = "'.str_replace($stringIN, $stringTXT, $m["text"]).'" ; hlColor['.$i.'] = \''.$hlColor.'\';';
                        $i++;
                    }
                    $markVar .= 'markID = '.$i;
                } else {
                    $markVar = 'hlColor[0] = "255,255,0";';
                }
            }
        }
    } else {
        $pgCheck=mysqli_query($con, "SELECT id FROM pages WHERE ad = ".$aid);
        if (mysqli_num_rows($pgCheck)>0) {
            $status='BLANK';
            $blankText='Select a page to view';
        } else {
            $status='EMPTY';
            $blankText='This ad proof is currently empty';
        }
        $rev=0;
        $pgName='-';
        $pgid=0;
        $rMax=0;
        $hr="-";
        $hrType="-";
    }
} else {
    header("Location: ".WEB);
}

$updateURL.='&n='.$pgName.'&pg='.$pgid.'&u='.$_COOKIE["user"];
$safeURL=$reloadURL;
$reloadURL.='&pg='.$pgid;

$stringIN=array("\"","\n");
$stringOUT=array("\\\"","<br/>");
$stringTXT=array("\\\"","\\r");

$hrDir='proofs/'.$hr.'/'.$pgid.'.'.$hrType;
$printMarkUp='pg='.$pgid;

$jsVar.="
    var projectID = '".$pid."';
    var revNum = '".$rev."';
    var adName = '".$aid."';
    var currentImg = '".$pgName."';
    var pn='".$pname."';
    var an = '".$aname."';";

?>

<!DOCTYPE html>
<html lang="en">
    <!-- HTML Head -->
<?php require 'includes/head.php'; ?>

<?php echo '<body style="margin-right:15px;margin-left:15px;" onload="loadPage('.$pid.','.$pgid.','.$rev.',\''.$_COOKIE["user"].'\',\''.$blankText.'\')" onresize="resizeWindow()">'; ?>

<?php 
$title="Ad Proof";
require 'includes/navbar.php';
?>

        <div style="height:120px;"></div><!-- this acts as a "spacer" between the nav bar and the page body. Without it, the body flows up and underneath the nav bar. -->

        <div class="row">
            <div id="stack" class="col-sm-3" style="overflow-y:auto;"></div>

            <div class="col-sm-6" id="mainWindow">
                <div class="row">
                    <div class="col-sm-7" style="text-align:left">
                        <ul class="breadcrumb" style="margin-bottom: 0px;">
                            <li><a href="index.php">Return to Project List</a></li>
<?php
echo '                        <li><a href="project.php?p='.$pid.'">'.$pname.'</a></li>';
if ($arch==true) {
    echo '                    <li><a href="archive.php?p='.$pid.'&z='.$zid.'">'.$zname.'</a></li>';
}
if ($folder==true && $arch==false) {
    echo '                    <li><a href="folder.php?p='.$pid.'&f='.$fid.'">'.$fname.'</a></li>';
}
if ($folder==true && $arch==true) {
    echo '                    <li><a href="folder.php?p='.$pid.'&z='.$zid.'&f='.$fid.'">'.$fname.'</a></li>';
}
?>
                            <li class="active"><?php echo $aname; ?></li>
                        </ul>
                    </div>
                    <div class="col-sm-5" style="text-align:right">
                        <div class="btn-group">
<?php
if ($arch==false) { 
    if ($_COOKIE["bnw"]!=="63bcabf86a9a991864777c631c5b7617" && $_COOKIE["bnw"]!=="3cd38ab30e1e7002d239dd1a75a6dfa8" && $_COOKIE["bnw"]!=="e26026b73cdc3b59012c318ba26b5518") {
        echo '                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addModal"><span class="glyphicons glyphicons-plus"></span> Add Page</button>';
    } else {
        echo '                <button type="button" class="btn btn-info disabled"><span class="glyphicons glyphicons-plus"></span> Add Page</button>';
    }
}

if ($pgid!=="0" && $status=="Active" && $_COOKIE["bnw"]!=="3cd38ab30e1e7002d239dd1a75a6dfa8" && $_COOKIE["bnw"]!=="e26026b73cdc3b59012c318ba26b5518" && $arch!==true && $rev==$rMax) {
    echo '                    <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Page Actions <span class="caret"></span></button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';
    if ($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
        echo '                        <li><a data-toggle="modal" data-target="#renameModal" onMouseOver="this.style.cursor=\'pointer\'">Rename Page</a></li>';
    }
    if ($_COOKIE["bnw"]!=="63bcabf86a9a991864777c631c5b7617" && $_COOKIE["bnw"]!=="3cd38ab30e1e7002d239dd1a75a6dfa8" && $_COOKIE["bnw"]!=="e26026b73cdc3b59012c318ba26b5518") {
        echo '                        <li><a data-toggle="modal" data-target="#updateModal" onMouseOver="this.style.cursor=\'pointer\'">Update Page</a></li>';
    }
    echo '                            <li><a onclick="approvePage('.$pgid.','.$aid.',\''.$_COOKIE["user"].'\',\''.$reloadURL.'\')" onMouseOver="this.style.cursor=\'pointer\'">Approve Page</a></li>';
    if ($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
        echo '                        <li class="divider"></li>
                                    <li><a onclick="deletePage('.$pgid.',\''.$safeURL.'\',\''.$_COOKIE["user"].'\')" onMouseOver="this.style.cursor=\'pointer\'">Delete Page</a></li>';
    }
    echo '
                                </ul>
                            </div>';
} elseif ($pgid!=="0" && $status=="Approved" && $rev==$rMax) {
    if ($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
        echo '                <button type="button" class="btn btn-info" onclick="unapprovePage('.$pgid.','.$aid.',\''.$_COOKIE["user"].'\',\''.$reloadURL.'\')">Unapprove Page</button>';
    } else {
        echo '                <button type="button" class="btn btn-info disabled">Unapprove Page</button>';
    }
} else {
    echo '                    <button type="button" class="btn btn-default disabled">Version '.$rev.'</button>';
}
?>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="cWrap" style="position:relative;margin:auto;width:100%;">
                        <canvas style="background:#ddd;"></canvas>
                        <!-- Markup text window (visible only while hovering over a markup ) -->
<?php
if ($_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518" || $arch==true) {
    echo '                <div class="panel panel-default panel-xparent" id="mWindow" style="position:absolute;right:0px;bottom:0px;width:220px;height:75px;z-index:20;visibility:hidden;box-shadow:none;">
                            <div class="panel-body">
                                <textarea id="mTextBox" style="width:190px;" onMouseOver="this.style.cursor=\'not-allowed\'" readonly></textarea>
                                <input type="hidden" id="btnRemove" value="no">
                                <input type="hidden" id="hlRed" value="no">
                                <input type="hidden" id="hlYellow" value="no">
                                <input type="hidden" id="hlGreen" value="no">
                                <input type="hidden" id="hlBlue" value="no">
                            </div>
                        </div>';
} else {
    echo '                <div class="panel panel-default panel-xparent" id="mWindow" style="position:absolute;right:0px;bottom:0px;width:220px;height:75px;z-index:20;visibility:hidden;box-shadow:none;">
                            <div class="panel-body">';
    if ($rev==$rMax && $status=="Active") {
        echo '                    <textarea id="mTextBox" style="width:190px;" placeholder="Type note here..."></textarea>
                                <div>
                                    <div style="float:left;text-align:left;width:60%;">
                                        <button type="button" class="btn btn-default btn-xs" data-toggle="tooltip" title="Red" id="hlRed"><font color="red"><span class="glyphicon glyphicon-tint"></span></font></button>
                                        <button type="button" class="btn btn-default btn-xs" data-toggle="tooltip" title="Yellow" id="hlYellow"><font color="yellow"><span class="glyphicon glyphicon-tint"></span></font></button>
                                        <button type="button" class="btn btn-default btn-xs" data-toggle="tooltip" title="Green" id="hlGreen"><font color="green"><span class="glyphicon glyphicon-tint"></span></font></button>
                                        <button type="button" class="btn btn-default btn-xs" data-toggle="tooltip" title="Blue" id="hlBlue"><font color="blue"><span class="glyphicon glyphicon-tint"></span></font></button>
                                    </div>
                                    <div style="float:right;">
                                        <button type="button" class="btn btn-success btn-xs" data-toggle="tooltip" title="Apply note" id="btnApply" onclick="savemarks('.$pid.','.$aid.',\''.$pname.'\',\''.$aname.'\','.$rev.',\''.$pgid.'\',\''.$_COOKIE["user"].'\')"><span class="glyphicon glyphicon-ok"></span></button>';
        if ($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92") {
            echo '                    <button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" title="Remove note" id="btnRemove"><span class="glyphicon glyphicon-remove"></span></button>';
        } else {
            echo '                    <button type="button" class="btn btn-default btn-xs disabled" id="btnRemove" value="no" onMouseOver="this.style.cursor=\'not-allowed\'"><span class="glyphicons glyphicons-remove"></span></button>';
        }
        echo '                    </div>
                                </div>';
    } else {
        echo '                <textarea id="mTextBox" style="width:190px;" onMouseOver="this.style.cursor=\'not-allowed\'" readonly></textarea>
                                <div>
                                    <div style="float:left;text-align:left;width:60%;">
                                        <button type="button" class="btn btn-default btn-xs disabled" id="hlRed" value="no"><font color="grey"><span class="glyphicon glyphicon-tint"></span></font></button>
                                        <button type="button" class="btn btn-default btn-xs disabled" id="hlYellow" value="no"><font color="grey"><span class="glyphicon glyphicon-tint"></span></font></button>
                                        <button type="button" class="btn btn-default btn-xs disabled" id="hlGreen" value="no"><font color="grey"><span class="glyphicon glyphicon-tint"></span></font></button>
                                        <button type="button" class="btn btn-default btn-xs disabled" id="hlBlue" value="no"><font color="grey"><span class="glyphicon glyphicon-tint"></span></font></button>
                                    </div>
                                    <div style="float:right;">
                                        <button type="button" class="btn btn-default btn-xs disabled" id="btnApply" value="no"><font color="grey"><span class="glyphicon glyphicon-ok"></span></font></button>
                                        <button type="button" class="btn btn-default btn-xs disabled" id="btnRemove" value="no"><font color="grey"><span class="glyphicons glyphicons-remove"></span></font></button>
                                    </div>
                                </div>';
    }
    echo '
                            </div>
                        </div>';
}
?>
                    </div> <!-- end canvas wrapper -->
                    <div class="row"> <!-- Controls row -->
                        <div class="col-sm-9" style="text-align:left"> <!-- Proofing button section -->
                            <button type="button" class="btn btn-info" data-toggle="tooltip" title="Zoom in" id="btnZoomIn"><span class="glyphicons glyphicons-zoom-in"></span></button>
                            <button type="button" class="btn btn-info" data-toggle="tooltip" title="Zoom out" id="btnZoomOut"><span class="glyphicons glyphicons-zoom-out"></span></button>
                            <button type="button" class="btn btn-info" data-toggle="tooltip" title="Reset zoom" id="zoomReset"><span class="glyphicons glyphicons-refresh"></span></button>
                            &nbsp;&nbsp;&nbsp;&nbsp;
<?php
if ($rev==$rMax && $status=="Active" && $_COOKIE["bnw"]!=="e26026b73cdc3b59012c318ba26b5518" && $arch!==true) {
    echo '                    <button type="button" class="btn btn-primary" data-toggle="tooltip" title="Apply markup" id="markup" onclick="markProof()"><span class="glyphicons glyphicons-pencil"></span></button>';
} else {
    echo '                    <button type="button" class="btn btn-default" data-toggle="tooltip" title="Apply markup" id="markup" disabled><span class="glyphicons glyphicons-pencil"></span></button>';
}
?>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#help"><span class="glyphicons glyphicons-question-sign"></span></button>
                            &nbsp;&nbsp;&nbsp;&nbsp;
<?php
if ($status!=='EMPTY' && $status!=='BLANK') {
    echo '                    <a type="button" class="btn btn-default" data-toggle="tooltip" title="View '.$hr.'" href="dl.php?pg='.$pgid.'" target="_blank" rel="noopener noreferrer""><span class="filetypes filetypes-'.$hrType.'"></span></a>';
}
echo '                        <a type="button" class="btn btn-default" data-toggle="tooltip" title="Print Markup" onclick="procPrintModal(\''.$printMarkUp.'\')"><span class="glyphicons glyphicons-print"></span></a>
                <!--            <a type="button" class="btn btn-default" data-toggle="tooltip" title="Print Markup" id="print" href="print/PDF/print.php?'.$printMarkUp.'"><span class="glyphicons glyphicons-print"></span></a>-->';?>
                        </div> <!-- end Proofing button section -->
                        <div class="col-sm-3" style="text-align:right">
<?php
if ($pgid!==0) {
    echo '                    <div class="dropup">';
    $verURL='ad.php?p='.$pid;
    if ($arch==true) {
        $verURL.='&z='.$zid;
    }
    if ($folder==true) {
        $verURL.='&f='.$fid;
    }
    $verURL.='&a='.$aid;
    echo '                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Version '.$rev;
    if ($rMax>1) {
        echo '                        <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">';
        $rList=mysqli_query($con, "SELECT id,rev FROM pages WHERE project = ".$pid." AND ad = ".$aid." AND name = '".$pgName."' ORDER BY rev ASC");
        while ($list=mysqli_fetch_assoc($rList)) {
            echo '                    <li><a href="'.$verURL.'&pg='.$list["id"].'">Version '.$list["rev"].'</a></li>';
        }
    }
    echo '                        </ul>
                            </div>';
}
?>
                        </div>
                    </div>
                </div> <!-- end Controls row -->
            </div> <!-- End main window -->
            <div id="expandNotes" class="col-sm-3">
                <div class="row" style="padding-right:15px;">
                    <div id="btnNoteExpand" style="text-align:right"><button type="button" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="bottom" title="Expand notes" onclick="noteExpand()"><span class="halflings halflings-resize-horizontal"></span></button></div>
                </div>
                <div class="row">
                    <div id="noteStack" class="col-sm-12" style="overflow-y:auto;">
<?php
// Acquire list of all distinct page names associated with this ad.
$stackQuery=mysqli_query($con, "SELECT DISTINCT name FROM pages WHERE ad = ".$aid." ORDER BY name ASC");

// Begin stack rendering.
echo '<div class="panel-group" id="prooflist">';

$iLoop=1;
$pdfstr=array(".JPG",".jpg");
$pdfrep=array("","");

while ($stack=mysqli_fetch_assoc($stackQuery)) {
    // Initialize variables for each page in stack
    $star='';
    $appcom='';
    $aCountIcon='';

    // Acquire current revision number, status, and latest modification time for this looped page.
    $pgQuery=mysqli_query($con, "SELECT MAX(rev) AS rev, status, MAX(last_modify) AS last_mod FROM pages WHERE ad = '".$aid."' AND name = '".$stack["name"]."'");
    $page=mysqli_fetch_assoc($pgQuery);
    // Store page status info for later
    if ($page["status"]=="Approved") {
        $lastAction='Approved: ';
        $badge='<h4 class="panel-title"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$stack["name"].'<span class="glyphicons glyphicons-ok-sign" style="float:right;" data-toggle="tooltip" title="Approved"></span></h4>';
    } else {
        $lastAction='Latest version: ';
        $badge='<h4 class="panel-title"><span class="badge" style="color:yellow;float:left;">'.$page["rev"].'</span>'.$stack["name"].'</h4>';
    }
    // If the name of the looped page matches the name of the page requested...
    if ($stack["name"]==$pgName) { 
        echo '
	<div class="panel panel-default"><div class="panel-active" data-toggle="tooltip" title="'.$lastAction.timestamp($page["last_mod"], $_COOKIE["tz"]).'">';
        $aQuery=mysqli_query($con, "SELECT text,hl,user,created FROM annotation WHERE wk = '".$aid."' AND page = '".$stack["name"]."' AND rev = '".$rev."' UNION ALL SELECT notes,hl,user,created FROM notes WHERE wk = '".$aid."' AND page = '".$stack["name"]."' AND rev = '".$rev."' ORDER BY created");
        $aCount=mysqli_num_rows($aQuery);
        if ($aCount>0) {
            $aCountIcon='<span class="glyphicons glyphicons-comments" style="float:right;color:#fff;" data-toggle="tooltip" title="Comments"></span>';
        } else {
            $aCountIcon='';
        }
        echo $badge.'
        </div>
        <div id="'.$stack["name"].'" class="panel-collapse collapse in" aria-expanded="true">
			<div class="panel-body">
		';
        while ($aText = mysqli_fetch_assoc($aQuery)) {
            echo '
                <p style="background-color:rgba('.$aText["hl"].',0.5);text-align:left;padding:0px 5px;">'.str_replace($stringIN, $stringOUT, $aText["text"]).'<br/><small><i>'.$aText["user"].' - '.timestamp($aText["created"], $_COOKIE["tz"]).'</i></small></p>
            ';
        }
        if ($rev==$page["rev"] && $page["status"]!=='Approved' && $_COOKIE["bnw"]!=="e26026b73cdc3b59012c318ba26b5518" && $arch!==true) {
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
        // If the name of the looped page DOES NOT match the name of the page requested...
        echo '
	<div class="panel panel-default">
	    <div class="panel-heading" data-toggle="tooltip" title="'.$lastAction.timestamp($page["last_mod"], $_COOKIE["tz"]).'">
            <h4 class="panel-title">
		';
        // Select most recent revision of this page for navigation
        $revStackQuery=mysqli_query($con, "SELECT id, viewlog FROM pages WHERE ad = '".$aid."' AND name = '".$stack["name"]."' AND rev = ".$page["rev"]);
        while ($rStackMax=mysqli_fetch_assoc($revStackQuery)) {
            $pgMax=$rStackMax["id"];
            $vLog=$rStackMax["viewlog"];
        }
        $aQuery=mysqli_query($con, "SELECT id FROM annotation WHERE wk = '".$aid."' AND page = '".$stack["name"]."' AND rev = '".$page["rev"]."' UNION ALL SELECT id FROM notes WHERE wk = '".$aid."' AND page = '".$stack["name"]."' AND rev = '".$page["rev"]."'");
        $aCount=mysqli_num_rows($aQuery);
        if ($aCount>0) {
            $aCountIcon='<span class="glyphicons glyphicons-comments" style="float:right;color:#fff;" data-toggle="tooltip" title="Comments"></span>';
        } else {
            $aCountIcon='';
        }
        $pgURL='ad.php?p='.$pid;
        if ($arch==true) {
            $pgURL.='&z='.$zid;
        }
        if ($folder==true) {
            $pgURL.='&f='.$fid;
        }
        $pgURL.='&a='.$aid.'&pg='.$pgMax;

        // Process 'viewlog' field to find out if the page has been viewed by this user.
        if (preg_match("/u".userID($_COOKIE["user"])."_/i", $vLog)) {
            $star='';
        } else {
            $star='<span class="glyphicons glyphicons-star" style="color:yellow;float:right;"></span>';
        }
        if ($page["status"]=='Approved') {
            $appcom='<span class="glyphicons glyphicons-ok-sign" style="float:right;" data-toggle="tooltip" title="Approved"></span>';
        } elseif ($aCount>0) {
            $appcom='&nbsp;<span class="label" style="float:right;"><span class="glyphicons glyphicons-comments"></span> '.$aCount.'</span>';
        }
        echo'
				<a href="'.$pgURL.'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$stack["name"].$star.$appcom.'</a>
			</h4>
        </div>
    </div>
            ';
    }
}

?>


                    </div>
                </div>
            </div>
        </div>
        <!-- Help modal dialog -->
        <div class="modal fade modal-primary" id="help" role="dialog">
            <div style="height:80px;"></div>
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#007054;">
                        <button type="button" class="close" style="color:white;" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" style="color:white;">Proof Express Controls</h4>
                    </div>
                    <div class="modal-body" style="text-align:left;">
                        <b>Zoom in:</b>
                        <ul>
                            <li>Click directly on the proof image, or</li>
                            <li>Scroll up with mouse scroll wheel (if available)</li>
                        </ul>
                        <b>Zoom out:</b>
                        <ul>
                            <li>Hold down Shift key while clicking directly on the proof image, or</li>
                            <li>Scroll up with mouse scroll wheel (if available)</li>
                        </ul>
                        <b>Show entire proof:</b>
                        <ul>
                            <li>Click "Reset zoom" button <button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-refresh"></span></button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Help modal dialog -->
        <div class="modal fade modal-primary" id="renameModal" role="dialog">
            <div style="height:120px;"></div>
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Rename Page</h4>
                    </div>
                    <div class="modal-body">
                        <b>Enter a new name for this page:</b>
<?php echo '            <input id="rename" type="text" value="'.$pgName.'">
                        <button type="button" class="btn btn-info" data-dismiss="modal" onclick="renamePage('.$pgid.')">Save</button>'; ?>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade modal-primary" id="updateModal" role="dialog">
            <div style="height:120px;"></div>
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Update Page</h4>
                    </div>
                    <div class="modal-body">
                        <div id="updatePageUploader">Upload</div>
                        <div id="updatePageStatus"></div>
                    </div>
                    <div id="updatePageClose" class="modal-footer"></div>
                </div>
            </div>
        </div>
        <div class="modal fade modal-primary" id="addModal" role="dialog">
            <div style="height:120px;"></div>
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Page</h4>
                    </div>
                    <div class="modal-body">
                        <div id="addPageUploader">Upload</div>
                        <div id="addPageStatus"></div>
                    </div>
                    <div id="addModalClose" class="modal-footer"></div>
                </div>
            </div>
        </div>
        <div class="modal fade modal-primary" id="procPrint" role="dialog">
            <div style="height:120px;"></div>
            <div class="modal-dialog modal-sm">
              <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body" style="text-align:center;">
                        <img src="images/sn_wait_100.gif" />
                        <p>Processing markup print...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- JavaScript initial constants -->
        <script type="application/javascript">
            var canvas = document.getElementsByTagName('canvas')[0];
            var zoomReset = document.getElementById('zoomReset');
            canvas.width = (window.innerWidth / 2) - 8; canvas.height = window.innerHeight - 268;
            var proof = new Image;
            var refresh=false;
            var firstView = true,doMarkup = false,markID = 0,mWinOpen = [];
            var hlx = [],hly = [],hlw = [],hlh = [],mCurrent = null,hlText = [],hlColor = [];
            document.getElementById("mWindow").style.visibility = "hidden";
            document.getElementById("cWrap").style.height = canvas.height - 15;
            document.getElementById("cWrap").style.width = canvas.width - 15;
            document.getElementById("noteStack").style.height = (window.innerHeight - 175) + 'px';
            function pointIn(){
                document.body.style.cursor = 'pointer';
            }
            function pointOut(){
                document.body.style.cursor = 'auto';
            }
            function jNoteApply(proof){
                var xmlhttp;
                var URIText = encodeURIComponent(document.getElementById("jNoteText").value);
                var encText = URIText.replace(/'/g, "%5C%27");
                var params = 'act=jobnote&proof=' + proof + '&notes=' + encText;
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        window.alert(xmlhttp.responseText);
                    }
                };
                xmlhttp.open("POST", "ajax.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
            function pNoteApply(proof,version,page){
                var xmlhttp;
                var URIText = encodeURIComponent(document.getElementById("pNoteText").value);
                var encText = URIText.replace(/'/g, "%5C%27");
                var params = 'act=pagenote&proof=' + proof + '&v=' + version + '&page=' + page + '&notes=' + encText;
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        window.alert(xmlhttp.responseText);
                    }
                };
                xmlhttp.open("POST", "ajax.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
            function approvePage(pg,a,u,url){
                var xmlhttp;
                var params = 'pg=' + pg + '&a=' + a + '&u=' + u;
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                    // window.alert(xmlhttp.responseText);
                        window.location.href = url;
                    }
                };
                xmlhttp.open("POST", "ajax/approvepage.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
            function unapprovePage(pg,a,u,url){
                if(confirm("You are about to remove approval for this page. Are you sure?") == true){
                    var xmlhttp;
                    var params = 'pg=' + pg + '&u=' + u + '&url=' + url;
                    xmlhttp=new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState == 4) {
                            window.location.href = url;
                        }
                    };
                    xmlhttp.open("POST", "ajax/unapprovepage.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send(params);
                }
            }
            function loadPage(p,pg,rev,user,b){
                stackProjects(p);
                if(pg==0){
                    blankProof(b);
                } else {
                    loadProof();
                    viewLog(pg,rev,user);
                }
            }
            function addNote(pid,aid,pname,aname,rev,pg,user) {
                var xmlhttp;
                var note = encodeURIComponent(document.getElementById("noteBox").value);
                var params = 'pid=' + pid + '&aid=' + aid + '&pname=' + pname + '&aname=' + aname + '&r=' + rev + '&pg=' + pg + '&n=' + note + '&u=' + user;
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        if(xmlhttp.responseText!=="OK"){
                            window.alert(xmlhttp.responseText);
                        } else {
                            noteRefresh(aid,rev,pg);
                        }
                    }
                };
                xmlhttp.open("POST", "ajax/addnote.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }

            function renamePage(pg) {
                var xmlhttp;
                var name = document.getElementById("rename").value;
                var params = 'pg=' + pg + '&n=' + name;
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        if(xmlhttp.responseText=="EXISTS"){
                            window.alert("You have entered a file name that is already in use by this ad.");
                        } else {
                            location.reload();
                        }
                    }
                };
                xmlhttp.open("POST", "ajax/renamepage.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
            function deletePage(pg,url,u) {
                if(confirm("You are about to PERMANENTLY REMOVE this page from Proof Express. Are you sure?") == true){
                    var xmlhttp;
                    var params = 'pg=' + pg + '&url=' + url + '&u=' + u;
                    xmlhttp=new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState == 4) {
                            window.location.href = xmlhttp.responseText;
                        }
                    };
                    xmlhttp.open("POST", "ajax/deletepage.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send(params);
                }
            }
            function viewLog(pg,rev,user) {
                var xmlhttp;
                var params = 'pg=' + pg + '&rev=' + rev + '&u=' + user;
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        return;
                    }
                };
                xmlhttp.open("POST", "ajax/pageviewlog.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
            function procPrintModal(url){
                $("#procPrint").modal();
                window.location.href = "/print/PDF/print.php?" + url;
            }
            function resizeWindow(){
                var canvas = document.getElementsByTagName('canvas')[0];
                var cText = canvas.getContext("2d");
                canvas.width = (window.innerWidth / 2) - 8; canvas.height = window.innerHeight - 268;
                document.getElementById("cWrap").style.height = canvas.height - 15;
                document.getElementById("cWrap").style.width = canvas.width - 15;
                document.getElementById("noteStack").style.height = (window.innerHeight - 175) + 'px';
                var tx=(canvas.clientWidth / 2) - 179;
                var ty=canvas.clientHeight / 2;
                cText.fillStyle="#007054";
                cText.font='italic bold 24px sans-serif';
                cText.textBaseline = 'bottom';
                refresh=true;
                cText.fillText('Click this window to reset view',tx,ty);
            }
            function blankProof(b){
                var canvas = document.getElementsByTagName('canvas')[0];
                var cText = canvas.getContext("2d");
                canvas.width = (window.innerWidth / 2) - 8; canvas.height = window.innerHeight - 268;
                document.getElementById("cWrap").style.height = canvas.height - 15;
                document.getElementById("cWrap").style.width = canvas.width - 15;
                document.getElementById("noteStack").style.height = (window.innerHeight - 175) + 'px';
                var tx=(canvas.clientWidth / 2) - 123;
                var ty=canvas.clientHeight / 2;
                cText.fillStyle="#007054";
                cText.font='italic bold 24px sans-serif';
                cText.textBaseline = 'bottom';
                refresh=true;
                cText.fillText(b,tx,ty);
            }
            function noteExpand(){
                document.getElementById("mainWindow").className = 'col-sm-3';
                document.getElementById("mainWindow").style.visibility = 'hidden';
                var h = document.getElementById("cWrap").style.height;
                document.getElementById("cWrap").style.height = '100px';
                document.getElementById("expandNotes").className = 'col-sm-6';
                document.getElementById('btnNoteExpand').innerHTML = '<button type="button" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="bottom" title="Collapse notes" onclick="noteCollapse(\'' + h + '\')"><span class="halflings halflings-resize-horizontal"></span></button>';
            }
            function noteCollapse(h){
                document.getElementById("mainWindow").className = 'col-sm-6';
                document.getElementById("mainWindow").style.visibility = 'visible';
                document.getElementById("cWrap").style.height = h;
                document.getElementById("expandNotes").className = 'col-sm-3';
                document.getElementById('btnNoteExpand').innerHTML = '<button type="button" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="bottom" title="Expand notes" onclick="noteExpand()"><span class="halflings halflings-resize-horizontal"></span></button>';
            }
        </script>
<?php
echo '
        <script>
            function showLog(p,w,z) {
                var xmlhttp;
                var activity = document.getElementById("logType").value;
                if(activity=="Page Views"){
                    var params = "pg='.$pgid.'&rev='.$rev.'&user='.$_COOKIE["user"].'&tz=" + z;
                    xmlhttp=new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState == 4) {
                            document.getElementById("userLog").innerHTML = xmlhttp.responseText;
                        }
                    };
                    xmlhttp.open("POST", "ajax/pageviews.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send(params);
                } else {
                    var params = "p=" + p + "&w=" + w + "&a=" + activity + "&tz=" + z;
                    xmlhttp=new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState == 4) {
                            document.getElementById("userLog").innerHTML = xmlhttp.responseText;
                        }
                    };
                    xmlhttp.open("POST", "ajax/projectlog.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send(params);
                }
            }
        </script>
';
if ($_COOKIE["bnw"]=="2c1743a391305fbf367df8e4f069f9f9" || $_COOKIE["bnw"]=="987bcab01b929eb2c07877b224215c92" || $_COOKIE["bnw"]=="05b048d7242cb7b8b57cfa3b1d65ecea") {
    echo '
        <script>
            function stackProjects(){
                var xmlhttp;
                var params = "p='.$pid.'&w='.$aid.'&tz='.$_COOKIE["tz"].'&bnw='.$_COOKIE["bnw"].'";
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        document.getElementById("stack").innerHTML = xmlhttp.responseText;
                    }
                };
                xmlhttp.open("POST", "ajax/stack_adm_projects.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
        </script>
    ';
} elseif ($_COOKIE["bnw"]=="63bcabf86a9a991864777c631c5b7617" || $_COOKIE["bnw"]=="3cd38ab30e1e7002d239dd1a75a6dfa8" || $_COOKIE["bnw"]=="e26026b73cdc3b59012c318ba26b5518") {
    echo '
        <script>
            function stackProjects(){
                var xmlhttp;
                var params = "p='.$pid.'&tz='.$_COOKIE["tz"].'&bnw='.$_COOKIE["bnw"].'";
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        document.getElementById("stack").innerHTML = xmlhttp.responseText;
                    }
                };
                xmlhttp.open("POST", "ajax/stack_gen_projects.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
        </script>
    ';
}
echo '
        <script>'.$jsVar.'</script>
        <script>'.$markVar.'</script>
        <script>
            function unlockNoteButton() {
                document.getElementById("updateNote").className = "btn btn-primary btn-sm";
                document.getElementById("updateNote").setAttribute("onclick", "addNote(\''.$pid.'\',\''.$aid.'\',\''.$pname.'\',\''.$aname.'\',\''.$rev.'\',\''.$pgName.'\',\''.$_COOKIE["user"].'\')");
            }

            function noteStack(){
    ';
if ($status!=='EMPTY') {
    $stackParams='pid='.$pid;
    if ($arch==true) {
        $stackParams.='&zid='.$zid;
    }
    if ($folder==true) {
        $stackParams.='&fid='.$fid;
    }
    $stackParams.='&aid='.$aid.'&pg='.$pgName.'&rev='.$rev.'&rmax='.$rMax.'&pgid='.$pgid.'&u='.$_COOKIE["bnw"].'&uid='.userID($_COOKIE["user"]).'&tz='.$_COOKIE["tz"];
    echo'
                var xmlhttp;
                var params = "'.$stackParams.'";
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        document.getElementById("noteStack").innerHTML = xmlhttp.responseText;
                    }
                };
                xmlhttp.open("POST", "ajax/stack_adm_notes.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
        ';
} else {
    echo '
                document.getElementById("noteStack").innerHTML = \'<div class="panel-group"><div class="panel-heading"><h4 class="panel-title">Proof is empty.</h4></div></div>\';
        ';
}
echo '    }';
echo '
            $(document).ready(function() {
                var updatePageSet = {
                    url: \''.$updateURL.'\',
                    method: "POST",
                    allowedTypes:"jpg,png,gif,doc,pdf,zip",
                    fileName: "myfile",
                    multiple: true,
                    onSuccess:function(files,data,xhr) {
                        if(data=="ERROR"){
                            $("#updatePageStatus").html("<font color=\'green\'>Update successful</font>");
                            $("#updatePageClose").html("<button type=\'button\' class=\'btn btn-success\' onclick=\'location.replace(\"'.$safeURL.'\")\'>Ok</button>");
                        } else if(data=="APPROVED"){
                            $("#updatePageStatus").html("<font color=\'red\'>Approved pages cannot be updated</font>");
                            $("#updatePageClose").html("<button type=\'button\' class=\'btn btn-success\' onclick=\'location.replace(\"'.$safeURL.'\")\'>Ok</button>");
                        } else {
                            $("#updatePageStatus").html("<font color=\'green\'>Update successful</font>");
                            $("#updatePageClose").html("<button type=\'button\' class=\'btn btn-success\' onclick=\'location.replace(" + data + ")\'>Ok</button>");
                        }
                    },
                    onError: function(files,status,errMsg) {        
                        $("#updatePageStatus").html("<font color=\'red\'>Update failed</font>");
                    }
                }
                $("#updatePageUploader").uploadFile(updatePageSet);
                var addPageSet = {
                    url: \''.$pageURL.'\',
                    method: "POST",
                    allowedTypes:"jpg,png,gif,doc,pdf,zip",
                    fileName: "myfile",
                    multiple: true,
                    onSuccess:function(files,data,xhr) {
                        if(data=="EXISTS"){
                            $("#addPageStatus").html("<font color=\'red\'>The page you are trying to add already exists.<br/>Use &quot;Update Page&quot; in the Page Actions dropdown to update this existing page.</font>");
                            $("#addModalClose").html("<button type=\'button\' class=\'btn btn-success\' data-dismiss=\'modal\'>Ok</button>");
                        } else {
                            $("#addPageStatus").html("<font color=\'green\'>Update successful</font>");
                            $("#addModalClose").html("<button type=\'button\' class =\'btn btn-success\' onclick=\'location.replace(" + data + ")\'>Ok</button>");
                        }
                    },
                    onError: function(files,status,errMsg) {        
                        $("#addPageStatus").html("<font color=\'red\'>Update failed</font>");
                    }
                }
                $("#addPageUploader").uploadFile(addPageSet);
            });

            function noteRefresh(aid,rev,pg) {
                var xmlhttp;
                var params = "aid=" + aid + "&r=" + rev + "&pg=" + pg + "&pgname='.$pgName.'&tz='.$_COOKIE["tz"].'&bnw='.$_COOKIE["bnw"].'";
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        document.getElementById("'.$pgName.'").innerHTML = xmlhttp.responseText;
                    }
                };
                xmlhttp.open("POST", "ajax/noterefresh.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            }
        </script>';
?>
</body>
</html>
