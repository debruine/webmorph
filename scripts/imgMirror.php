<?php

// mirror selected images and tems

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$tem = IMAGEBASEDIR . preg_replace('@\.(jpg|png|gif)$@', '.tem', $_POST['img']);
if (file_exists($tem)) {
    $img = new PsychoMorph_ImageTem($_POST['img']);
} else {
    $img = new PsychoMorph_Image($_POST['img']);
}

$img->mirror();
$img->addHistory("mirror");
    
$newFileName = array(
    'subfolder' => $_POST['subfolder'],
    'prefix' => $_POST['prefix'],
    'suffix' => $_POST['suffix'],
    'ext' => $_POST['ext']
);

if ($img->save($newFileName)) {
    $return['error'] = false;
    $return['newFileName'] = $img->getURL();
} else {
    $return['errorText'] .= 'The image was not saved. ';
    $return['newFileName'] = '';
}

scriptReturn($return);

?>