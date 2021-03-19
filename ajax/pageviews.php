<?php
/**
 * Proof Express AJAX - Page Views
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

require '../includes/inc.php';
$pg=$_POST["pg"];
$rev=$_POST["rev"];
$tz=$_POST["tz"];

$viewLog=array();
$view=array();

// Retrieve log field from page id.
$vQuery=mysqli_query($con, "SELECT * FROM pages WHERE id = ".$pg);
while ($v=mysqli_fetch_assoc($vQuery)) {
    $log=$v["viewlog"];
}
echo '
<table style="width:100%;">
	<thead>
		<tr>
			<th style="text-align:left;">User</th>
			<th style="text-align:right;">Timestamp</th>
		</tr>
	</thead>
	<tbody>
';

// Parse log field.
$viewLog=explode(":", $log);
foreach ($viewLog as $row) {
    if ($row!=='') {
        $view=explode("_", $row);
        echo '
		<tr>
        	<td style="text-align:left;"><small>'.userName(str_replace("u", "", $view[0])).'</small></td>
        	<td style="text-align:right;"><small>'.timestamp($view[1], $tz).'</small></td>
		</tr>
		';
    }
}
echo '
	</tbody>
</table>
';
?>
