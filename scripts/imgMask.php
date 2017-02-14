<?php
    
// mask an image

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => '',
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$img = new PsychoMorph_ImageTem($_POST['img']);

$mask = explode(',', $_POST['mask']);
$blur = $_POST['blur'];
$reverse = ($_POST['reverse'] == 'true');

$custom = null;
if (!empty($_POST['custom'])) {
    $mask = array('custom');
    
    $custom = explode(':', $_POST['custom']);
    foreach ($custom as $j => $m) {
        $custom[$j] = explode(';', $m);
        foreach($custom[$j] as $i => $m2) {
            $custom[$j][$i] = explode(',', $m2);
        }
    }
    $desc = "mask: ({$_POST['custom']}), {$blur}";
} else {
    $desc = "mask: ({$_POST['mask']}), {$blur}";
}


$rgba = $_POST['rgb'];
$a = $_POST['transparent'] == 'true' ? 0 : 1;
array_push($rgba, $a);

if ($_POST['transparent'] == 'true') { 
        $desc .= ", transparent";
    } else {
        $desc .= ", rgb({$_POST['rgb'][0]}, {$_POST['rgb'][1]}, {$_POST['rgb'][2]})";
    }




if (!$mask || !$rgba) {
    $return['error'] = true;
    $return['errorText'] = 'There was not enough information to mask the images.';
}

if (!$return['error']) {
    ini_set('max_execution_time', 30*($blur+1));
    
    $img->mask($mask, $rgba, $blur, $reverse, $custom);
    
    if ($custom) {
        
    } else {
        
    }
    
    $img->addHistory($desc);
    $newFileName = array(
        'subfolder' => $_POST['subfolder'],
        'prefix' => $_POST['prefix'],
        'suffix' => $_POST['suffix'],
        'ext' => $_POST['transparent'] == 'true' ? 'png' : ''
    );
    
    if ($img->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $img->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
        $return['newFileName'] = '';
    }
}

scriptReturn($return);

?>