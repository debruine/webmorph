<?php

// read a text file

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
    'text' => ''
);

$filename = $_POST['url'];
$file = IMAGEBASEDIR . safeFileName($filename);

if (!file_exists($file)) {
    $return['errorText'] .= "The file <code>$filename</code> does not exist.";
} else if (!($mytext = file($file))) {
    $return['errorText'] .= "The file <code>$filename</code> could not be read.";
} else if (count($mytext) < 1) {
    $return['errorText'] .= "The file <code>$filename</code> had no data.";
} else {

    $return['error'] = false;
    $return['text'] =  implode("", $mytext);
    $return['created'] = date('Y-m-d H:i:s', filemtime($file));
}

scriptReturn($return);

exit;

?>