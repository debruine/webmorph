<?php
    
// create a template visualisation

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$filename = pathinfo($_POST['img'], PATHINFO_FILENAME);
$filename = ifEmpty($filename, "template") . '.svg';

header('Content-Type: image/svg+xml');
header('Content-disposition: attachment; filename=' . $filename);

echo $_POST['svg'];

exit;
?>