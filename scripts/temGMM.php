<?php

// concatenate tem files into a form for GMM

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$return = array(
	'error' => true,
	'errorText' => '',
);

$filename = $_POST['tem'];
$temfile = IMAGEBASEDIR . safeFileName($filename);
$tem = new PsychoMorph_Tem($temfile);

$points = $tem->getPoints();

$tps = 'LM=' . count($points) . "\n";
foreach ($points as $p) {
	$tps .=$p[0] . ' ' . $p[1] . "\n";
}
$tps .= 'IMAGE=' . $filename . "\n";
$tps .= "SCALE=1.0\n";


$newfilename = array(
	'subfolder' => $_POST['subfolder'],
	'prefix' => $_POST['prefix'],
	'suffix' => $_POST['suffix'],
	'ext' => $ext
);

if () {
	$return['error'] = false;
	$return['newfilename'] = $tem->getURL();
} else {
	$return['errorText'] .= 'The tem file was not saved. ';
	$return['newfilename'] = '';
}

scriptReturn($return);

exit;

?>