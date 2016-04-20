<?php

// download data as a csv file

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

# filename for download 
$filename = ifEmpty($_POST['name'], 'csvfile') . ".csv"; 

# check that everything went OK
if (!empty($_POST['file'])) {

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/plain");  
    
    echo $_POST['file'];
}


exit;
    
?>