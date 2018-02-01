<?php

// crop selected images

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$img = new PsychoMorph_ImageTem($_POST['img']);

$t = $_POST['t'];
$r = $_POST['r'];
$b = $_POST['b'];
$l = $_POST['l'];

$x = $_POST['x'];
$y = $_POST['y'];
$w = $_POST['w'];
$h = $_POST['h'];

$rgb = $_POST['rgb'];
    
if ($t !== '' && $r !== '' && $b !== '' && $l !== '') {
    $orig_w = $img->getImg()->getWidth();
    $orig_h = $img->getImg()->getHeight();
    
    $w = $orig_w + $l + $r;
    $h = $orig_h + $t + $b;
    $x = -1*$l;
    $y = -1*$t;
} else if ($x !== '' && $y !== '' && $w !== '' && $h !== '') {    
    // do nothing
} else {
    $return['error'] = true;
    $return['errorText'] = 'There was not enough information to crop the images.';
}

if (!$return['error']) {
    $newFileName = array(
        'subfolder' => $_POST['subfolder'],
        'prefix' => $_POST['prefix'],
        'suffix' => $_POST['suffix'],
        'ext' => $_POST['ext']
    );
    
    if ($_POST['patch']) {
        $rgb = $img->getImg()->patch(
            $_POST['patch'][0], 
            $_POST['patch'][1], 
            $_POST['patch'][2], 
            $_POST['patch'][3]
        );
    }
    
    $img->crop($x, $y, $w, $h, $rgb);
    
    $desc = "crop: {$t}, {$r}, {$b}, {$l}";
    if ($rgb) { $desc .= ", rgb({$rgb[0]}, {$rgb[1]}, {$rgb[2]})"; }
    $img->addHistory($desc);
    
    if ($img->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $img->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
        $return['newFileName'] = '';
    }
}

scriptReturn($return);

exit;

?>