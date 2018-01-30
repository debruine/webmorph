<?php
    
// visualise a template in SVG or PNG

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
    'newFileName' => ''
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

if (array_key_exists('img', $_POST)) {
    $img = new PsychoMorph_Tem($_POST['img']);
}

$svg = $img->tem2SVG($_POST);

$newFileName = array(
    'subfolder' => $_POST['subfolder'],
    'prefix' => $_POST['prefix'],
    'suffix' => $_POST['suffix'],
    'ext' => $_POST['ext']);

$newpath = $img->newPathFromArray($newFileName);

if (!$newpath) {
    $return['errorText'] .= 'The new file name could not be created. ';
} else if (file_exists($newpath)) {
    $return['errorText'] .= 'The file name already exists. ';
} else if ($_POST['ext'] == 'png') {
    $svg = "<?xml version='1.0' encoding='UTF-8'  standalone='no'?>" . PHP_EOL . 
          // "<!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'>" . PHP_EOL .
           $svg;
    $im = new Imagick();
    $im->setBackgroundColor(new ImagickPixel('rgba(0,0,0,0)'));
    $im->readImageBlob($svg);
    $im->setImageFormat("png32");
    if ($im->writeimage($newpath)) {
        $return['error'] = false;
        $return['newFileName'] = str_replace(IMAGEBASEDIR, '', $newpath);
    }    
} else if (file_put_contents($newpath, $svg)) {   
    $return['error'] = false;
    $return['newFileName'] = str_replace(IMAGEBASEDIR, '', $newpath);
} else {
    $return['errorText'] .= 'The image was not saved. ';
}

scriptReturn($return);

?>