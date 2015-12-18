<?php

// rotate selected images and tems

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
	'error' => false,
	'errorText' => '',
);

$degrees = intval($_POST['degrees']) % 360;
$rgb = $_POST['rgb'];

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$tem = IMAGEBASEDIR . preg_replace('@\.(jpg|png|gif)$@', '.tem', $_POST['img']);
if (file_exists($tem)) {
	$img = new PsychoMorph_ImageTem($_POST['img']);
} else {
	$img = new PsychoMorph_Image($_POST['img']);
}

if ($degrees == 0) {
	$return['error'] = true;
	$return['errorText'] = 'No need to rotate 0 degrees.';
}

if (!$return['error']) {
	$img->rotate($degrees, $rgb);
	
	$newfilename = array(
		'subfolder' => $_POST['subfolder'],
		'prefix' => $_POST['prefix'],
		'suffix' => $_POST['suffix'],
		'ext' => $_POST['ext']
	);
	
	if ($img->save($newfilename)) {
		$return['error'] = false;
		$return['newfilename'] = $img->getURL();
	} else {
		$return['error'] = true;
		$return['errorText'] .= 'The image was not saved. ';
		$return['newfilename'] = '';
	}
}

scriptReturn($return);

exit;

?>