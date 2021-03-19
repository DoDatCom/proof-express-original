<?php
/**
 * Proof Express
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
 * @copyright 2016-2019 Dodat Communications
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PackageName
 */

require $_SERVER["DOCUMENT_ROOT"].'/includes/inc.php';

$imgDir = ABSDIR.'proofs/IMG/';
$start_id = $_POST["id"];
if (isset($_POST['l'])) {
    $limit = $_POST['l'];
} else {
    $limit = 1000;
}
$error_msg = false;

$scanned_directory = array_diff(scandir($imgDir), array('.','..'));

natsort($scanned_directory);

$i = 0; $r = 0;
foreach ($scanned_directory as $key => $val) {
    $id = str_replace('.jpg', '', $val);
    if ($query = mysqli_query($con, "SELECT `id` FROM `pages` WHERE `id` = ".$id)) {
        if (mysqli_num_rows($query) == 0) {
            echo $id.'...';
            unlink($imgDir.$id.'.jpg');
            $r++;
            if ($r == $limit) {
                break;
            }
        }
    } 
    $final = $id;
}
echo '<hr/>'.$r.' files purged. Final ID is '.$final;

?>
