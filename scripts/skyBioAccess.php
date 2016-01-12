<?php

// access a file in a user's directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

$file = IMAGEBASEDIR . $_GET['file'];
$userCanDownloadThisFile = true; // apply your logic here

if (file_exists($file) && $userCanDownloadThisFile && underPath($file)) {
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	if (in_array($ext, array('jpg','gif','png')) && filesize($file) > 5000) {
		if ($ext == 'jpg') {
	    	header('Content-Type: image/jpeg');
		} else if ($ext == 'png') {	
	    	header('Content-Type: image/png');
		} else if ($ext == 'gif') {
	    	header('Content-Type: image/gif');
		}
		echo file_get_contents($file);
	}
} else {
	echo $file;
}

?>