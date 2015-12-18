<?php

// access a file in a user's directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$file = IMAGEBASEDIR . $_GET['file'];

preg_match("/^\d{1,11}\//", $_GET['file'], $project);
$project = str_replace('/', '', $project[0]);
$user = $_SESSION['user_id'];

// apply your logic here
$userCanDownloadThisFile = in_array($project, $_SESSION['projects']); 

if (file_exists($file) && $userCanDownloadThisFile && underPath($file)) {
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	if (array_key_exists('thumb', $_GET) && in_array($ext, array('jpg','gif','png')) && filesize($file) > 5000) {
		header('Content-Type: image/jpeg');
		if ($ext == 'jpg') {
			echo exif_thumbnail($file);
		} else if ($ext == 'png' || $ext == 'gif') {
			$img = ($ext == 'png') ? imagecreatefrompng($file) : imagecreatefromgif($file);
			$width = imagesx($img); 
			$height = imagesy($img);
			$new_height = 100;
			$new_width = $width * $new_height / $height;
			$thumb_img = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($thumb_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagedestroy($img);
			
			imagejpeg($thumb_img);
			imagedestroy($thumb_img);
		}
	} else {
		if ($ext == 'jpg') {
	    	header('Content-Type: image/jpeg');
		} else if ($ext == 'png') {	
	    	header('Content-Type: image/png');
		} else if ($ext == 'gif') {
	    	header('Content-Type: image/gif');
		} else if ($ext == 'tem') {
	    	header('Content-Type: text/plain');
		}
		
		readfile($file); //echo file_get_contents($file);
	}
} else {
	echo "You do not have permission to access this file";
}

?>