<?php

// delete files in a user's .tmp folder

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return['error'] = false;

function empty_trash($deletedir) {
    if (is_dir($deletedir)) {
        $handle = opendir($deletedir);
        $allowed_ext = array('jpg', 'png', 'gif', 'tem', 'zip', 'pca');
        
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $ext = pathinfo($entry, PATHINFO_EXTENSION);
                $path = $deletedir . '/' . $entry;
                if (!is_dir($path)) { // && in_array($ext, $allowed_ext)) {
                    unlink($path);
                    $return['deleted'][] = $entry;
                } else {
                    $return['not deleted'][] = $entry;
                }
            }
        }
        closedir($handle);
    }
}

if (projectPerm($_POST['project'])) {
    empty_trash(IMAGEBASEDIR . $_POST['project'] . '/.trash');
    empty_trash(IMAGEBASEDIR . $_POST['project'] . '/.tmp');
    updateDirMod();
} else {
    $return['error'] = true;
    $return['errorText'] = "You do not have permission to delete files in this project.";
}

scriptReturn($return);

exit;

?>