<?php

// get the median rgb colour values from a patch of an image

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();

$return = array(
    'error' => true,
    'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

if ($theImg = new PsychoMorph_Image($_POST['img']);

if ($image = $theImg->getImage()) {
    $return['medians'] = $image->patch($_POST['startx'], $_POST['endx'], $_POST['starty'], $_POST['endy']);
} else {
    $return['errorText'] =  "Could not open the image {$_POST['img']}";
}

scriptReturn($return);

exit;

?>