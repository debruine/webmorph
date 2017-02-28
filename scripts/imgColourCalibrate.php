<?php

// align selected images using data from the tems

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
);

ini_set('memory_limit','512M');

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$img = new PsychoMorph_Image($_POST['img']);

// x, y coordiantes and L*(D65), a*(D65) and b*(D65) values for facelab chart #1
$facelab_chart1 = array(
    'Dark skin' =>         array(3685,645,38.87,12.15,12.76),
    'Light skin' =>     array(3685,855,64.13,13.22,17.01),
    'Blue sky' =>         array(3685,1065,51.1,-4.14,-20.63),
    'Foliage' =>         array(3685,1285,42.61,-10.89,21.48),
    'Blue flower' =>     array(3685,1490,56.67,6.91,-23.17),
    'Bluish green' =>     array(3685,1700,71.25,-30.38,2.88),
    'Orange' =>         array(3475,645,60.66,33.49,53.32),
    'Purplish blue' =>     array(3475,855,43.75,8.59,-41.51),
    'Moderate red' =>     array(3475,1065,50.92,42.18,13.62),
    'Purple' =>         array(3475,1285,31.86,19.1,-21.07),
    'Yellow green' =>     array(3475,1490,71.45,-20.45,57.14),
    'Orange Yellow' =>     array(3475,1700,69.97,18.5,64.65),
    'Blue' =>             array(3265,645,33.79,11.38,-43.55),
    'Green' =>             array(3265,855,56.06,-35.2,32.94),
    'Red' =>             array(3265,1065,42.04,43.83,25.47),
    'Yellow' =>         array(3265,1285,79.78,4.06,78.58),
    'Magenta' =>         array(3265,1490,52.05,43.77,-16.57),
    'Cyan' =>             array(3265,1700,53.65,-27.77,-22.5),
    'White' =>             array(3055,645,95.13,-0.81,2),
    'Neutral 8' =>         array(3055,855,81.05,-0.75,0.09),
    'Neutral 6.5' =>     array(3055,1065,66.92,-0.83,-0.36),
    'Neutral 5' =>         array(3055,1285,51.33,-0.05,-0.15),
    'Neutral 3.5' =>     array(3055,1490,36.27,-0.52,-1.01),
    'Black' =>             array(3055,1700,21.99,-0.09,-0.94),
);

$img->colourCalibrate($facelab_chart1);

$newFileName = array(
    'subfolder' => $_POST['subfolder'],
    'prefix' => $_POST['prefix'],
    'suffix' => $_POST['suffix'],
    'ext' => 'jpg'
);

if ($img->save($newFileName)) {
    $return['error'] = false;
    $return['newFileName'] = $img->getURL();
} else {
    $return['errorText'] .= 'The image was not saved.';
    $return['newFileName'] = '';
}

scriptReturn($return);

exit;

?>