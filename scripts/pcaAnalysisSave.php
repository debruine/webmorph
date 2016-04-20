<?php

// saves a pca analysis file

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => true,
    'errorText' => '',
);



if (array_key_exists('analysis', $_POST)) {
    $analysis = $_POST['analysis'];
    $filecontents = "Name,PC";
    $firstvalue = reset($analysis);
    $r = range(0, (substr_count($firstvalue, ",")-1));
    $filecontents .= implode(",PC", $r);
    $filecontents .= "\n";
    
    foreach ($analysis as $imgname => $weights) {
        $imgnamae = preg_replace('@\.tem$@', '', $imgname);
        $filecontents .= "{$imgname}," . trim($weights);
        $filecontents .= "\n";
    }
    
    //$return['output'] = $filecontents;
    
    $filename = IMAGEBASEDIR . $_POST['name'];
    
    if (!($file = fopen($filename, 'w'))) {
        $return['errorText'] =  "Could not open the file {$filename}"; 
    } else if (!fwrite($file, $filecontents)) {
        $return['errorText'] =  "Could not write to the file {$_POST['name']}"; 
    } else if (!fclose($file)) {
        $return['errorText'] =  "Could not close the file";
    } else {
        $return['error'] = false;
    }
} else {
    $return['errorText'] = 'The analysis was blank';
}

scriptReturn($return);

exit;

?>