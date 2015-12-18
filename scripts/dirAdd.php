<?php

// creates a new directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => true,
	'errorText' => '',
	'user' => ''
);

$newdir = safeFileName($_POST['dirname']);
$basedir = $_POST['basedir'];

if ($newdir == '') {
	$return['errorText'] .= 'The new directory needs a name.';
} else if (!chdir(IMAGEBASEDIR . $basedir)) {
	$return['errorText'] .= "The base directory <code>$basedir</code> could not be accessed.";
} else if (is_file($newdir)) {
	$return['errorText'] .= "The directory <code>$basedir/$newdir</code> already exists.";
} else if (!mkdir($newdir, DIRPERMS)) {
	$return['errorText'] .= "The directory <code>$basedir/$newdir</code> could not be created.";
} else {
	$return['error'] = false;
}

scriptReturn($return); 

exit;

?>
