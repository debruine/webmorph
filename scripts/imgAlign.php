<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();
checkAllocation();

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

if ($_POST['img']) {
    include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

    if (array_key_exists('tem', $_POST)) {
        $img = new PsychoMorph_ImageTem($_POST['img'], $_POST['tem']);
    } else {
        $img = new PsychoMorph_ImageTem($_POST['img']);
    }
    
    $img->alignEyes($_POST['width'], $_POST['height'],
                    array($_POST['x1'], $_POST['y1']),
                    array($_POST['x2'], $_POST['y2']),
                    $_POST['pt1'], $_POST['pt2'], $_POST['rgb']);
                    
    // add to image description
    $desc = "align: {$_POST['pt1']}, {$_POST['pt2']}, {$_POST['x1']}, {$_POST['y1']}, {$_POST['x2']}, {$_POST['y2']}, {$_POST['width']}, {$_POST['height']}";
    if ($rgb) { $desc .= ", rgb({$_POST['rgb'][0]}, {$_POST['rgb'][0]}, {$_POST['rgb'][0]})"; }
    $img->addHistory($desc);
    
    if (array_key_exists('newFileName', $_POST)) {
        $newFileName = safeFileName($_POST['newFileName']);
    } else {
        $newFileName = array(
            'subfolder' => $_POST['subfolder'],
            'prefix' => $_POST['prefix'],
            'suffix' => $_POST['suffix']
        );
    }
    
    //$img->setOverWrite(true);
    if ($img->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $img->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
    }
} else {
    $return['errorText'] .= 'The image to align was not found.';
}

scriptReturn($return);

exit;

?>