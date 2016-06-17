<?php

// get the LAB colour values from an image

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$theImg = new PsychoMorph_Image($_POST['img']);

if ($image = $theImg->getImage()) {
    $return['width'] = $theImg->getWidth();
    $return['height'] = $theImg->getHeight();
    
    $return['newFileName'] = preg_replace('/(jpg|gif|png)$/', 'csv', $_POST['img']);
    
    $filename = IMAGEBASEDIR . $return['newFileName'];
    
    $filecontents = "x,y,L,a,b\n";
    
    if ($_POST['ignore_mask'] == 'true') {
        $mask_color = imagecolorat($image, 0, 0);
        $return['ignore_mask'] = $mask_color;
    } else {
        $mask_color = -1;
        $return['ignore_mask'] = $mask_color;
    }
    
    for ($x = 0; $x < $return['width']; $x++) {
        for ($y = 0; $y < $return['height']; $y++) {
            $color_index = imagecolorat($image, $x, $y);
            if ($color_index != $mask_color) {
                $r = ($color_index >> 16) & 0xFF;
                $g = ($color_index >> 8) & 0xFF;
                $b = $color_index & 0xFF;
                $lab = rgb2lab($r, $g, $b);
                $l = round($lab['L'], 4);
                $a = round($lab['a'], 4);
                $b = round($lab['b'], 4);
                
                $filecontents .= "{$x},{$y},{$l},{$a},{$b}\n";
            }
        }
    }
    
    if (file_exists($filename)) {
        unlink($filename);
    }
    
    if (!($file = fopen($filename, 'w'))) {
        $return['errorText'] =  "Could not open the file {$return['newFileName']}"; 
    } else if (!fwrite($file, $filecontents)) {
        $return['errorText'] =  "Could not write to the file {$return['newFileName']}"; 
    } else if (!fclose($file)) {
        $return['errorText'] =  "Could not close the file";
    } else {
        $return['error'] = false;
    }
} else {
    $return['errorText'] =  "Could not open the image {$_POST['img']}";
}

scriptReturn($return);

exit;

?>