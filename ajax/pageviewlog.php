<?php
/**
 * Proof Express Page View AJAX Script
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

require '../includes/inc.php';

$now=date("U", strtotime("now"));

$uid=userID($_POST["u"]);
$pg=$_POST["pg"];
$rev=$_POST["rev"];

$vQuery=mysqli_query($con, "SELECT * FROM pages WHERE id = ".$pg);
while ($log=mysqli_fetch_assoc($vQuery)) {
    if (preg_match("/u".$uid."_/i", $log["viewlog"])) {
        return;
    } else {
        $newLog=mysqli_query($con, "UPDATE pages SET viewlog = CONCAT(viewlog,'u".$uid."_".$now.":') WHERE id = ".$pg) or die(mysqli_errno($newLog));
    }
}
?>
