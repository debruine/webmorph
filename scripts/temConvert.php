<?php

// modify the selected tem files by deleting points and chnaging lines
// need to add adding points

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$return = array(
	'error' => true,
	'errorText' => '',
);

$filename = $_POST['img'];
$temfile = IMAGEBASEDIR . safeFileName($filename);
//$tem = new PsychoMorph_Tem($temfile);

$img_jpg = preg_replace('@\.tem$@', '.jpg', $temfile);
$img_png = preg_replace('@\.tem$@', '.png', $temfile);
$img_gif = preg_replace('@\.tem$@', '.gif', $temfile);

if (file_exists($img_jpg)) {
	$tem = new PsychoMorph_ImageTem($img_jpg);
	$ext = 'jpg';
} else if (file_exists($img_png)) {
	$tem = new PsychoMorph_ImageTem($img_png);
	$ext = 'png';
} else if (file_exists($img_gif)) {
	$tem = new PsychoMorph_ImageTem($img_gif);
	$ext = 'gif';
} else {
	$tem = new PsychoMorph_Tem($temfile);
	$ext = 'tem';
}

// modify tem file
if ($ext == "tem") {
	$tem->temConvert($_POST['old_tem'], $_POST['new_tem']);
} else {
	$tem->getTem()->temConvert($_POST['old_tem'], $_POST['new_tem']);
}

$newfilename = array(
	'subfolder' => $_POST['subfolder'],
	'prefix' => $_POST['prefix'],
	'suffix' => $_POST['suffix'],
	'ext' => $ext
);

if ($tem->save($newfilename)) {
	$return['error'] = false;
	$return['newfilename'] = $tem->getURL();
} else {
	$return['errorText'] .= 'The tem file was not saved. ';
	$return['newfilename'] = '';
}

scriptReturn($return);

exit;

?>