<?php
 /**
 * Proof Express File Maintenance
 *
 * @category  PHP/HTML
 * @package   PE
 * @author    David Wilkins <dwilkins@dodatcommunications.com>
 * @copyright 2016-2021 DoDat Communications
 * @license   https://www.gnu.org/licenses/gpl-3.0.txt The GNU General Public License (V3)
 * @link      None
 */

session_start();

require $_SERVER["DOCUMENT_ROOT"].'/includes/inc.php';

?>

<!DOCTYPE html>
<html lang="en">
<!-- HTML Head -->
<head>

<?php require ABSDIR.'includes/head.php'; ?>

</head>

<body>

<?php
if ($_COOKIE["bnw"] !== "2c1743a391305fbf367df8e4f069f9f9") { // Administrator
    ?>
    
    <h3>This page is to be used by a system administrator only. <a href="<?php echo WEB; ?>">Click here</a> to go back.</h3>

    <?php
} else {
    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <p>Please enter the ID number of the ad to remove:</p>
                <input id="ad-id" type="text" class="form-control" placeholder="Ad ID..." />
                <button type="button" onclick="purge('ad')">Purge Ad</button>
            </div>
            <div class="col-lg-4">
                <p>Please enter the ID number of the folder to remove:</p>
                <input id="folder-id" type="text" class="form-control" placeholder="Folder ID..." />
                <button type="button" onclick="purge('folder')">Purge Folder</button>
            </div>
            <div class="col-lg-4">
                <p>Please enter the file type to remove:</p>
                <input id="file-type" type="text" class="form-control" placeholder="e.g., 'jpg', 'pdf', 'img'..." />
                <button type="button" onclick="purge('dir')">Purge Directory</button>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div id="response"></div>
        </div>
    </div>

    <script>
        function purge(action){
            document.getElementById('response').innerHTML = 'Please wait...';
            var xmlhttp;
            if (action == 'ad') {
                var params = 'a=ad&id=' + document.getElementById('ad-id').value;
            }
            if (action == 'folder') {
                var params = 'a=folder&id=' + document.getElementById('folder-id').value;
            }
            if (action == 'dir') {
                var params = 'a=dir&id=' + document.getElementById('file-type').value;
            }
            xmlhttp=new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    document.getElementById('response').innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("POST", "/ajax/purgeimgfiles.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(params);
        }
    </script>

    <?php
}
?>

</body>
</html>
