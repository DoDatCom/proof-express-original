<?php
	include('../includes/inc.php');
	$p='Apples';//$_POST['p'];
	$w='wk06';//$_POST['w'];
	echo ABSDIR.'<br/><br/>';
	$zip = new ZipArchive;
	$res = $zip->open(ABSDIR.'projects/'.$p.'/'.$w.'/'.$p.'_'.$w.'.zip', ZipArchive::CREATE);
	if($res === TRUE) {
		echo '$res is TRUE';
	
		$projectArray = array_diff(scandir(ABSDIR.'/projects/'.$p.'/'.$w.'/pdf'), array(".", "..", ".DS_Store"));
		natcasesort($projectArray);
    // Add files to the zip file
    
    	foreach($projectArray as $proof){
    		echo $proof.'<br/>';
//    		$zip->addFile($proof);
			$zip->addFile(ABSDIR.'projects/'.$p.'/'.$w.'/pdf/'.$proof,$proof);
//			$zip->addFile(ABSDIR.'projects/'.$p.'/'.$w.'/pdf/FFS_06-04_3103_G1F.pdf','FFS_06-04_3103_G1F.pdf');
//			$zip->addFile(ABSDIR.'projects/'.$p.'/'.$w.'/pdf/FFS_06-04_3103_G2B.pdf','FFS_06-04_3103_G2B.pdf');
    	}
    
 
    // All files are added, so close the zip file.
    	$zip->close();
	}
?>