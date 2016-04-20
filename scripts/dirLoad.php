<?php

// load all files in a user's directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
    'dir' => ''
);

function dir2Array($dir, $base = IMAGEBASEDIR) {
    global $dblist;

    $result = array();

    $cdir = scandir($dir);
    
    foreach ($cdir as $key => $value) {
        if ($value == ".trash" || substr($value,0, 1) != '.') {
            $filename = $dir . DIRECTORY_SEPARATOR . $value;
            if (is_dir($filename)) {
                $folder = str_replace($base, '', $filename);
                $result[$folder] = dir2Array($filename);
            } else {
                $result[str_replace($base, 'i', $filename)] = '';
            }
        }
    }
    
    ksort($result);
    return $result;
}

$thedir = realpath(IMAGEBASEDIR . $_POST['subdir']);

if (strpos($thedir, IMAGEBASEDIR) !== 0) {
    $return['error'] = true;
    $return['errorText'] = 'The directory is not valid.';
} else if (!is_dir($thedir)) {
    $return['error'] = true;
    $return['errorText'] = 'The directory does not exist.';
} else {
    $return['dir'] = array($_POST['subdir'] => dir2Array($thedir, IMAGEBASEDIR) );
}

scriptReturn($return);

exit;

?>