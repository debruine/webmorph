<?php

// resize selected images and tems

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
);

$newWidth = $_POST['w'];
$newHeight = $_POST['h'];

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$tem = IMAGEBASEDIR . preg_replace('@\.(jpg|png|gif)$@', '.tem', $_POST['img']);

if (file_exists($tem)) {
    $img = new PsychoMorph_ImageTem($_POST['img']);
} else {
    $img = new PsychoMorph_Image($_POST['img']);
}

if ($newWidth > 0 && $newHeight > 0) {
    // calculate resize dimensions based on exact pixels for both dimensions
    $w = $img->getWidth();
    $h = $img->getHeight();
    $xResize = $newWidth/$w;
    $yResize = $newHeight/$h;
    $desc = "resize: {$newWidth}px, {$newHeight}px";
} else if ($newWidth > 0) {
    // calculate resize dimensions based on exact width and same % height
    $w = $img->getWidth();
    $xResize = $newWidth/$w;
    $yResize = $xResize;
    $desc = "resize: {$newWidth}px, null";
} else if ($newHeight > 0) {
    // calculate resize dimensions based on exact height and same % width
    $h = $img->getHeight();
    $yResize = $newHeight/$h;
    $xResize = $yResize;
    $desc = "resize: null, {$newHeight}px";
} else if ($_POST['x'] > 0) {
    $xResize = $_POST['x']/100;
    $yResize = $_POST['y'] ? $_POST['y']/100 : $xResize;
    $desc = "resize: {$_POST['x']}%";
    if ($_POST['y']) { $desc .= ", {$_POST['y']}%"; }
} else if ($_POST['y'] > 0) {
    $yResize = $_POST['y']/100;
    $xResize = $_POST['x'] ? $_POST['x']/100 : $yResize;
    $desc = "resize: {$_POST['x']}%";
} else {
    $return['error'] = true;
    $return['errorText'] = 'There was not enough information to resize the images.';
}

if (!$return['error']) {
    $img->resize($xResize, $yResize);
    $img->addHistory($desc);

    
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
        $return['error'] = true;
        $return['errorText'] .= 'The image was not saved. ';
        $return['newFileName'] = '';
    }
}

scriptReturn($return);

exit;

?>