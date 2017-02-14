<?php
    
// mask an image

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$img = new PsychoMorph_ImageTem('13/composites/f_easian.jpg');
$mask = array('left_eye','right_eye');
$blur = 0;
$rgba = array(255,255,0,1);
$custom = null;
$reverse = true;
    
$img->mask($mask, $rgba, $blur, $reverse, $custom);

header('Content-Type: image/jpeg');
imagepng($img->getImg()->getImage());

?>