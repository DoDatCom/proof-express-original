<?php
/**
 * EventSource is documented at
 * http://dev.w3.org/html5/eventsource/
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
 * @package   Dodat
 * @author    David Wilkins <dwilkins@dodatcommunications.com>
 * @copyright 2016-2018 Dodat Communications
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PackageName
 */

// Initialize includes
require '../includes/inc.php';

// Establish variables from POST data
$aid = $_POST["aid"];
$pg = $_POST["pg"];
$rev = $_POST["r"];
$pgName = $_POST["pgname"];
$tz = $_POST["tz"];
//$bnw = $_POST["bnw"];

$stringIN=array("\"","\n");
$stringOUT=array("\\\"","<br/>");
$stringTXT=array("\\\"","\\r");

$aQuery=mysqli_query($con, "SELECT notes,hl,user,created FROM notes WHERE wk = '".$aid."' AND page = '".$pgName."' AND rev = '".$rev."' ORDER BY created");

echo '<div class="panel-body">';

while ($aText = mysqli_fetch_assoc($aQuery)) {
    echo '<p style="background-color:rgba('.$aText["hl"].',0.5);text-align:left;padding:0px 5px;">'.str_replace($stringIN, $stringOUT, $aText["notes"]).'<br/><small><i>'.$aText["user"].' - '.timestamp($aText["created"], $tz).'</i></small></p>';
}
echo '<div class="row">
    <textarea id="noteBox" placeholder="New page note" style="width:265px;" rows="5" oninput="unlockNoteButton()"></textarea>
    <button id="updateNote" type="button" class="btn btn-primary btn-sm disabled" style="margin-bottom:15px;">Add Note</button>
</div></div>';

?>
