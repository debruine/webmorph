<?php

// read the tem file

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
    'tem' => ''
);

$filename = $_GET['url'];
$temfile = IMAGEBASEDIR . safeFileName($filename);

if (!file_exists($temfile)) {
    $return['errorText'] .= "The file <code>$filename</code> does not exist.";
} else if (!($mytem = file($temfile))) {
    $return['errorText'] .= "The file <code>$filename</code> could not be read.";
} else if (count($mytem) < 1) {
    $return['errorText'] .= "The file <code>$filename</code> had no data.";
} else {
    $return['error'] = false;
    $return['tem'] =  implode("", $mytem);
        /*
        $pointNumber = $mytem[0];
        $temPoints = array_slice($mytem, 1, $pointNumber);
        $temPoints = array_map(function($n) { 
            global $display_height, $new_height;
            $pts = preg_split('/\s+/', $n);
            $pts[0] = $pts[0] * $display_height / $new_height;
            $pts[1] = $pts[1] * $display_height / $new_height;
            return $pts;
        }, $temPoints);
        $lineVectors = array();
        for ($i = $pointNumber+4; $i < count($mytem); $i += 3) {
            $lineVectors[] = explode(" ", trim($mytem[$i]));
        }
        */    
}

scriptReturn($return);

exit;

?>