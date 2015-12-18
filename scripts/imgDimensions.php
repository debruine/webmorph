<?php

// get image dimensions

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array('error' => false);

if (is_array($_GET['img'])) { 
	foreach ($_GET['img'] as $i => $img) {
		$filename = IMAGEBASEDIR . $img;
	
		if (is_file($filename)) {
			list($return['w'][$i], $return['h'][$i]) = getimagesize($filename);
		} else {
			$return['error'] = true;
			$return['errorText'][$i]= 'The image <code>' . $img . '</code> does not exist.';
		}		
	}
} else {
	$filename = IMAGEBASEDIR . $_GET['img'];
	
	if (is_file($filename)) {
		list($return['w'], $return['h']) = getimagesize($filename);
	} else {
		$return['error'] = true;
		$return['errorText'] .= 'The image <code>' . $_GET['img'] . '</code> does not exist.';
	}
}

scriptReturn($return);

?>