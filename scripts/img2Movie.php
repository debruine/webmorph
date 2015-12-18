<?php

// create an animated gif from a series of images

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

require_once DOC_ROOT . '/include/classes/GIFEncoder.class.php';

$return = array(
	'error' => true,
	'errorText' => '<ul>',
	'post' => $_POST
);

ini_set('memory_limit','1024M');

// get images
$images = $_POST['images'];
$temp = "temp_" . md5(microtime()) . "_";
$new_height = ($_POST['new_height']>10) ? $_POST['new_height'] : 200;

foreach ($images as $n => $img) {
	if (strpos($img, "/tomcat/") !== false) {
		$filename = str_replace("/tomcat/", TOMCAT, $img);
	} else {
		$filename = IMAGEBASEDIR . $img;
	}
	
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	
	if (!file_exists($filename)) {
		$return['errorText'] .= "<li>$filename does not exist.</li>";
	} else if ($ext == 'png') {
		$image = imagecreatefrompng($filename);
	} else if ($ext == 'jpg') {
		$image = imagecreatefromjpeg($filename);
	} else if ($ext == 'gif') {
		$image = imagecreatefromgif($filename);
	} else {
		$return['errorText'] .= "<li>Could not open the image $filename</li>";
	}
	
	if ($image) {
		// shrink images to new_height
		list($width, $height) = getimagesize($filename);
		$percent = $new_height / $height;
		if ($percent == 1.0) {
			$newimage = $image;
		} else {
			$new_width = $width * $percent;
			$newimage = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		}
		
		ob_start();
		imagetruecolortopalette();
		imagegif($newimage);
		$frames[]=ob_get_contents();
		ob_end_clean(); 
	}
	
}

if (count($frames) == 0) {
	$return['errorText'] .= "<li>No movie frames could be created.</li></ul>";
	scriptReturn($return);
	exit;
}

// reverse frames
if ($_POST['rev'] == 'true') {
	$backframes = array_reverse($frames);
	$newframes = array_merge($frames, $backframes);
} else {
	$newframes = $frames;
}

$loops = ($_POST['loops'] > 0) ? $_POST['loops'] : 0;

// set framelength
$framelength = ($_POST['speed'] > 0) ? $_POST['speed'] : 5;
$delays = array_fill(0, count($newframes), $framelength);

if ($_POST['rev'] == 'true' && $_POST['pause'] > 0) {
	$delays[0] = $_POST['pause'];
	$delays[count($frames)] = $_POST['pause'];
}

$gif = new AnimatedGif($newframes,$delays,$loops);

$newfilename = str_replace(array('.jpg', '.gif'), '', safeFileName($_POST['newfilename'])) . '.gif';
	
if (!$newgif = fopen(IMAGEBASEDIR . $newfilename, "wb")) {
	$return['errorText'] .= "<li>Cannot open $newfilename.</li>";
} else if (!fwrite($newgif , $gif->GetAnimation())) {
	$return['errorText'] .= "<li>Cannot write to $newfilename.</li>";
} else {
	$return['error'] = false;
}
$return['gif'] = $newfilename;
$return['errorText'] .= "</ul>";

scriptReturn($return);

?>