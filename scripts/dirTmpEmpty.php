<?php

// delete files in a user's .tmp folder

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

function delete_tmp($deletedir) {
    if (is_dir($deletedir)) {
        $handle = opendir($deletedir);
        $allowed_ext = array('jpg', 'png', 'gif', 'tem', 'zip', 'pca');
        
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $ext = pathinfo($entry, PATHINFO_EXTENSION);
                $path = $deletedir . '/' . $entry;
                if (!is_dir($path)) { // && in_array($ext, $allowed_ext)) {
                    unlink($path);
                }
            }
        }
        closedir($handle);
    }
}

delete_tmp(IMAGEBASEDIR . '/.tmp');

?>