<?php
/**
 * Proof Express Note Stack AJAX
 * 
 * PHP version 7.1
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  AJAX
 * @package   PE
 * @author    David Wilkins <dwilkins@dodatcommunications.com>
 * @copyright 2016-2018 Dodat Communications
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PackageName
 */

// Load functions and variables from 'includes' file
require '../includes/inc.php';

// Variables used for filtering and replacing invalid characters in text strings
$stringIN=array("\"","\n");
$stringOUT=array("\\\"","<br/>");
$stringTXT=array("\\\"","\\r");

// Process POST parameters
$pid=$_POST["pid"];                    // Project ID
if (isset($_POST["zid"])) {
    $zid=$_POST["zid"];                // Archive ID (if applicable)
    $arch=true;
} else {
    $arch=false;
}
if (isset($_POST["fid"])) {            
    $fid=$_POST["fid"];                // Folder ID (if applicable)
    $folder=true;
} else {
    $folder=false;
}
$aid=$_POST["aid"];                    // Ad ID
$pgName=$_POST["pg"];                  // Name of the currently-viewed page in the ad
$rev=$_POST["rev"];                    // Revision number of the currently-viewed page in the ad
$pgid=$_POST["pgid"];                  // Page ID of the currently-viewed page in the ad
$bnw=$_POST["u"];                      // Credential info for the user
$tz=$_POST["tz"];                      // Timezone for the user
$uid=$_POST["uid"];                    // User ID

// Acquire list of all distinct page names associated with this ad.
$stackQuery=mysqli_query($con, "SELECT DISTINCT name FROM pages WHERE ad = ".$aid." ORDER BY name ASC");

// Begin stack rendering.
echo '<div class="panel-group" id="prooflist">';

$i=1;
$pdfstr=array(".JPG",".jpg");
$pdfrep=array("","");

while ($stack=mysqli_fetch_assoc($stackQuery)) {
    // Initialize variables for each page in stack
    $star='';
    $appcom='';
    $aCountIcon='';

    // Acquire current revision number, status, and latest modification time for this looped page.
    $pgQuery=mysqli_query($con, "SELECT MAX(rev) AS rev, status, MAX(last_modify) AS last_mod, viewlog FROM pages WHERE ad = '".$aid."' AND name = '".$stack["name"]."'");
    $page=mysqli_fetch_assoc($pgQuery);
    // Store page status info for later
    if ($page["status"]=="Approved") {
        $status='Approved: ';
        $badge='<h4 class="panel-title"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$stack["name"].'<span class="glyphicons glyphicons-ok-sign" style="float:right;" data-toggle="tooltip" title="Approved"></span></h4>';
    } else {
        $status='Latest version: ';
        $badge='<h4 class="panel-title"><span class="badge" style="color:yellow;float:left;">'.$page["rev"].'</span>'.$stack["name"].'</h4>';
    }
    // If the name of the looped page matches the name of the page requested...
    if ($stack["name"]==$pgName) { 
        echo '
	<div class="panel panel-default"><div class="panel-active" data-toggle="tooltip" title="'.$status.timestamp($page["last_mod"], $tz).'">';
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
                <p style="background-color:rgba('.$aText["hl"].',0.5);text-align:left;padding:0px 5px;">'.str_replace($stringIN, $stringOUT, $aText["text"]).'<br/><small><i>'.$aText["user"].' - '.timestamp($aText["created"], $tz).'</i></small></p>
            ';
        }
        if ($rev==$page["rev"] && $page["status"]!=='Approved' && $_POST["u"]!=="e26026b73cdc3b59012c318ba26b5518" && $arch!==true) {
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
	    <div class="panel-heading" data-toggle="tooltip" title="'.$status.timestamp($page["last_mod"], $tz).'">
            <h4 class="panel-title">
		';
        // Select most recent revision of this page for navigation
        $revQuery=mysqli_query($con, "SELECT id FROM pages WHERE ad = '".$aid."' AND name = '".$stack["name"]."' AND rev = ".$page["rev"]);
        while ($rMax=mysqli_fetch_assoc($revQuery)) {
            $pgMax=$rMax["id"];
        }
        $aQuery=mysqli_query($con, "SELECT id FROM annotation WHERE wk = '".$aid."' AND page = '".$stack["name"]."' AND rev = '".$page["rev"]."' UNION ALL SELECT id FROM notes WHERE wk = '".$aid."' AND page = '".$stack["name"]."' AND rev = '".$page["rev"]."'");
        $aCount=mysqli_num_rows($aQuery);
        if ($aCount>0) {
            $aCountIcon='<span class="glyphicons glyphicons-comments" style="float:right;color:#fff;" data-toggle="tooltip" title="Comments"></span>';
        } else {
            $aCountIcon='';
        }
        $pageURL='ad.php?p='.$pid;
        if ($arch==true) {
            $pageURL.='&z='.$zid;
        }
        if ($folder==true) {
            $pageURL.='&f='.$fid;
        }
        $pageURL.='&a='.$aid.'&pg='.$pgMax;

        // Process 'viewlog' field to find out if the page has been viewed by this user.
        if (preg_match("/u".$uid."_/i", $page["viewlog"])) {
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
				<a href="'.$pageURL.'"><span class="badge" style="float:left;">'.$page["rev"].'</span>'.$stack["name"].$star.$appcom.'</a>
			</h4>
        </div>
    </div>
            ';
    }
}
echo '
</div>
</div>
';
?>
