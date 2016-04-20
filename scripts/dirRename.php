<?php

// rename a directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
    'post' => $_POST
);

$newdir = preg_replace('@/$@', '', safeFileName($_POST['newdir'])); // remove trailing slash
$olddir = preg_replace('@/$@', '', safeFileName($_POST['olddir'])); // remove trailing slash
$basedir = preg_replace('@[^?;:{}/]+$@', '', $olddir);    // remove last dir name
$olddir = preg_replace('@^' . $basedir . '@', '', $olddir);

if ($newdir == '') {
    $return['errorText'] .= 'You cannot change the directory name to &ldquo;' . $_POST['newdir'] . '&rdquo;';
} else if (!is_dir(IMAGEBASEDIR . $basedir) || !chdir(IMAGEBASEDIR . $basedir)) {
    $return['errorText'] .= 'Could not change directory to ' . $basedir;
} else if (is_dir($newdir)) {
    $return['errorText'] .= $basedir . $newdir . ' already exists';
} else if (!rename($olddir, $newdir)) {
    $return['errorText'] .= "Could not rename $olddir to $newdir";
}else {
    chmod($newdir, DIRPERMS);
    $return['error'] = false;
    $return['newdir'] = $basedir . $newdir;
    $old = '/' . $basedir . '/' . $olddir . '/';
    $new = '/' . $basedir . '/' . $newdir . '/';
    $old = str_replace('//', '/', $old);
    $new = str_replace('//', '/', $new);
    $q = new myQuery("UPDATE img SET name = REPLACE(name, '{$old}', '{$new}') WHERE LOCATE('{$old}', name) = 1");
    $return['images_updated'] = $q->get_affected_rows();        
}

scriptReturn($return);

exit;

?>
