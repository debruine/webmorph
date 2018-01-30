<?php

// selected files for user download

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$acceptable_files = array(
    "/include/examples/webmorph_template_batchAvg.txt",
    "/include/examples/webmorph_template_batchEdit.txt",
    "/include/examples/webmorph_template_batchTrans.txt"
);

if (in_array($_POST['file'], $acceptable_files)) {

    $file = $_SERVER['DOCUMENT_ROOT'] . $_POST['file'];
    
    $filename = pathinfo($file, PATHINFO_BASENAME);
    $mime = mime_content_type($file);
    $filepath = $file;
    
    header('Content-Type: ' . $mime);
    header('Content-disposition: attachment; filename=' . $filename);
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else {
    $return['error'] = true;
    $return['errorText'] = "You requested an unavailable file.";
}

scriptReturn($return);

exit;
    
?>