<?php

// convert image types

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
	'error' => false,
	'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$img = new PsychoMorph_ImageTem($_POST['img']);

$allowed_ext = array('jpg', 'png', 'gif');

if (in_array($_POST['to'], $allowed_ext)) {
	if ($img->convert($_POST['to'])) {
		$return['newfilename'] = $img->getURL();
	} else {
		$return['error'] = true;
		$return['errorText'] = 'The converted image did not save.';
	}
} else {
	$return['error'] = true;
	$return['errorText'] = $_POST['to'] . ' is not an allowed filetype.';
}

scriptReturn($return);

exit;

?>