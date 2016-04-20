<?php

// webcam file save

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
    //'post' => $_POST
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$newFileName = safeFileName($_POST['basedir'] . '/' . $_POST['name']) . '.jpg';
$return['newFileName'] = $newFileName;

if (!array_key_exists('imgBase64', $_POST)) {
    $return['error'] = true;
    $return['errorText'] .= 'The webcam image did not transfer.';
} else if (file_exists(IMAGEBASEDIR . $newFileName)) {
    $return['error'] = true;
    $return['errorText'] .= preg_replace("/^(\d{1,11}\/)/", "/", $newFileName) . ' already exists. Delete, rename, or move it first.';
} else {
    $img = new PsychoMorph_Image();
    $b64 = str_replace('data:image/jpeg;base64,', '', $_POST['imgBase64']);
    $b64 = str_replace(' ', '+', $b64);
    $img->setImageBase64($b64);
    $img->setDescription('Webcam upload');
    $img->save($newFileName);
}

scriptReturn($return);

exit;

?>