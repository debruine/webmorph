<?php

/****************************************************
*
* Scrambles images sensu Conway et al. (2008, JoV) 
*
****************************************************/

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();
checkAllocation();

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$newFileName = array(
    'subfolder' => $_POST['subfolder'],
    'prefix' => $_POST['prefix'],
    'suffix' => $_POST['suffix'],
    'ext' => $_POST['ext']
);

if (!($img = new PsychoMorph_ImageTem($_POST['img']))) {
    $return['errorText'] = 'The image could not be opened.';
} else if (!$img->scramble($_POST)) {
    $return['errorText'] = 'The image could not be scrambled.';
} else if (!$img->save($newFileName)) {
    $return['errorText'] = 'The image was not saved.';
} else {
    $return['error'] = false;
    $return['newFileName'] = $img->getURL();
}

scriptReturn($return);

exit;

?>