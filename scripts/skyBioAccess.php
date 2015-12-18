<?php

// access a file in a user's directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

$file = '/var/www/html/images/' . $_GET['file'];
$userCanDownloadThisFile = true; // apply your logic here

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
		echo file_get_contents($file);
	}
} else {
	echo $file;
}

?>