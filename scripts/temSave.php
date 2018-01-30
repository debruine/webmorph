<?php

// saves a tem file 
// needs to be replaced by the class

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
);

if (array_key_exists('tem', $_POST)) {
    $tem = $_POST['tem'];
    $temname = preg_replace('@\.(jpg|png|gif)$@', '.tem', $_POST['name']);
    $imgname = IMAGEBASEDIR . preg_replace('@\.tem$@', '.jpg', $temname);
    
    if (!($file = fopen(IMAGEBASEDIR . $temname, 'w'))) {
        $return['errorText'] =  "Could not open the file {$temname}"; 
    } else if (!fwrite($file, $tem)) {
        $return['errorText'] =  "Could not write to the file {$temname}"; 
    } else if (!fclose($file)) {
        $return['errorText'] =  "Could not close the file";
    } else {
        $return['error'] = false;
    }
} else {
    $return['errorText'] = 'The tem points were not found.';
}

scriptReturn($return);

exit;

?>