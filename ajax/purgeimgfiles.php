<?php
 /**
 * Proof Express File Maintenance AJAX
 *
 * @category  PHP
 * @package   PE
 * @author    David Wilkins <dwilkins@dodatcommunications.com>
 * @copyright 2016-2021 DoDat Communications
 * @license   https://www.gnu.org/licenses/gpl-3.0.txt The GNU General Public License (V3)
 * @link      None
 */

require $_SERVER["DOCUMENT_ROOT"].'/includes/inc.php';

if (isset($_POST['a'])) {
    if ($_POST['a'] == 'ad') {
        // Purge ad
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
        }
        
        $r = 0;
        $ad_query = mysqli_query($con, "SELECT * FROM `pages` WHERE `ad` = ".$id);
        while ($ad_result = mysqli_fetch_assoc($ad_query)) {
            $ad_purge = mysqli_query($con, "DELETE FROM `pages` WHERE `id` = ".$ad_result['id']);
            if (is_file(ABSDIR.'proofs/PDF/'.$ad_result['id'].'.pdf')) {
                unlink(ABSDIR.'proofs/PDF/'.$ad_result['id'].'.pdf');
            }
            if (is_file(ABSDIR.'proofs/IMG/'.$ad_result['id'].'.jpg')) {
                unlink(ABSDIR.'proofs/IMG/'.$ad_result['id'].'.jpg');
            }
            if (is_file(ABSDIR.'proofs/JPG/'.$ad_result['id'].'.jpg')) {
                unlink(ABSDIR.'proofs/JPG/'.$ad_result['id'].'.jpg');
            }
            $r++;
        }
        $ad_delete = mysqli_query($con, "DELETE FROM `ads` WHERE `id` = ".$id);
        echo $r.' pages purged. Ad #'.$id.' purged.';
    }

    if ($_POST['a'] == 'folder') {
        // Purge folder
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
        }
        
        $p = 0;
        $a = 0;
        $folder_query = mysqli_query($con, "SELECT * FROM `ads` WHERE `folder` = ".$id);
        while ($folder_result = mysqli_fetch_assoc($folder_query)) {
            $ad_query = mysqli_query($con, "SELECT * FROM `pages` WHERE `ad` = ".$folder_result['id']);
            while ($ad_result = mysqli_fetch_assoc($ad_query)) {
                $ad_purge = mysqli_query($con, "DELETE FROM `pages` WHERE `id` = ".$ad_result['id']);
                if (is_file(ABSDIR.'proofs/PDF/'.$ad_result['id'].'.pdf')) {
                    unlink(ABSDIR.'proofs/PDF/'.$ad_result['id'].'.pdf');
                }
                if (is_file(ABSDIR.'proofs/IMG/'.$ad_result['id'].'.jpg')) {
                    unlink(ABSDIR.'proofs/IMG/'.$ad_result['id'].'.jpg');
                }
                if (is_file(ABSDIR.'proofs/JPG/'.$ad_result['id'].'.jpg')) {
                    unlink(ABSDIR.'proofs/JPG/'.$ad_result['id'].'.jpg');
                }
                $p++;
            }
            $ad_delete = mysqli_query($con, "DELETE FROM `ads` WHERE `id` = ".$folder_result['id']);
            $a++;
        }
        $ad_delete = mysqli_query($con, "DELETE FROM `ads` WHERE `id` = ".$id);
        echo $p.' pages purged from '.$a.' ads. Folder #'.$id.' purged.';

    }

    if ($_POST['a'] == 'dir') {
        // Purge directory
        if (isset($_POST['id'])) {
            if ($_POST['id'] == 'img') {
                $folder = 'jpg';
            } else {
                $folder = $_POST['id'];
            }
        }

        $dir = ABSDIR.'proofs/'.strtoupper($folder).'/';

        $error_msg = false;

        $scanned_directory = array_diff(scandir($dir), array('.','..'));
        natsort($scanned_directory);

        $r = 0;

        foreach ($scanned_directory as $key => $val) {
            $id = str_replace('.'.$folder, '', $val);
            if ($query = mysqli_query($con, "SELECT `id` FROM `pages` WHERE `id` = ".$id)) {
                if (mysqli_num_rows($query) == 0) {
                    unlink($dir.$id.'.'.$folder);
                    $r++;
                }
            } 

        }
        
        echo '<hr/>'.$r.' files purged.';
    }
}

?>
