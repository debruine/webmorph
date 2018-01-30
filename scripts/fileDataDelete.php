<?php

// delete metadata from image or tem
// doesn't work yet!!

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();

$return = array(
    'error' => false,
    'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

if (preg_match('@\.tem$@', $_POST['img']) {
    $file = new PsychoMorph_Tem($_POST['img']);
} else if (preg_match('@\.(jpg|png)$@', $_POST['img']) {
    // only jpg and png have metadata
    $file = new PsychoMorph_Image($_POST['img']);
} else {
    $return['error'] = true;
    $return['errorText'] = 'The file could not be opened.';
}

if (!$return['error']) {
    if ($file->save()) {
        $return['error'] = false;
        $return['newFileName'] = $file->getURL();
    } else {
        $return['errorText'] .= 'The file was not saved. ';
        $return['newFileName'] = '';
    }
}

scriptReturn($return);

exit;

?>