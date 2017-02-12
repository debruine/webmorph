<?php

// read embedded tem and save as tem file

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$imgname = IMAGEBASEDIR . $_POST['img'];

if (file_exists($imgname)) {
    $img = new PsychoMorph_Image($imgname);
    $embTem = $img->getEmbeddedTem();
    $return['embtem'] = $embTem;
    if ($embTem != '') {
        $tem = new PsychoMorph_Tem();
        $tem->loadRawTem($embTem);
        $newFileName = preg_replace('@\.(jpg|png|gif)$@', '.tem', $imgname);
        // add JSON history to tem
        $tem->addHistory('tem created from embed ' . $img->getURL());
        $tem->setDescription('image', $img->getURL());
    } else {
        $return['error'] = true;
        $return['errorText'] .= 'The image did not have an embedded tem. ';
    }
} else {
    $return['error'] = true;
    $return['errorText'] .= 'The image was not found. ';
}

if (!$return['error']) {
    if ($tem->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $tem->getURL();
    } else {
        $return['error'] = true;
        $return['errorText'] .= 'The tem was not saved. ';
        $return['newFileName'] = '';
    }
}

scriptReturn($return);

exit;

?>