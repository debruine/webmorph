<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

$img = safeFileName($_POST['img']);

if ($img) {
    ini_set('memory_limit','512M');
    
    // standard 1350 x 1800 FRL alignment
    $aligned_x1 = $_POST['x1'];
    $aligned_y1 = $_POST['y1'];
    $aligned_x2 = $_POST['x2'];
    $aligned_y2 = $_POST['y2'];
    $align_width = $_POST['width'];
    $align_height = $_POST['height'];
    $align_pt1 = $_POST['pt1'];
    $align_pt2 = $_POST['pt2'];
    $rgb = $_POST['rgb'];
    
    include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

    if (array_key_exists('tem', $_POST)) {
        $img = new PsychoMorph_ImageTem($img, $_POST['tem']);
    } else {
        $img = new PsychoMorph_ImageTem($img);
    }
    
    $img->alignEyes($align_width, $align_height,
                    array($aligned_x1, $aligned_y1),
                    array($aligned_x2, $aligned_y2),
                    $align_pt1, $align_pt2, $rgb);
    
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
        $return['newFileName'] = $img->getImg()->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
    }
} else {
    $return['errorText'] .= 'The image to align was not found.';
}

scriptReturn($return);

exit;

?>