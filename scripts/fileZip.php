<?php

// zip selected mages or directories for user download

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
    'zippath' => ''
);

$mytmpdir = IMAGEBASEDIR . '/.tmp';
if (!is_dir($mytmpdir) && !mkdir($mytmpdir, 0777)) {
    $return['error'] = true;
    $return['errorText'] .=  '<li>Your image directory temporary folder could not be created</li>';    
}
chmod($mytmpdir, 0777);        // keep this until all tmp dirs have been created and chmoded


function addFileToList($dir) {
    $list = array();
    
    if (is_dir($dir)) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file)) {
                $list = array_merge($list, addFileToList($file));
            } else {
                $list[] = $file;
            }
        }
    } else if (is_file($dir)) {
        $list[] = $dir;
    }
    
    return $list;
}

if (array_key_exists('directory', $_POST)) {
    $mydir = preg_replace('@/$@', '', IMAGEBASEDIR . $_POST['directory']);
    $files = addFileToList($mydir);
} else if (array_key_exists('files', $_POST)) {
    $files = array();
    $exploded = explode(',', $_POST['files']);
    foreach ($exploded as $f) {
        $files[] = IMAGEBASEDIR . $f;
    }
    $mydir = pathinfo($files[0], PATHINFO_DIRNAME);
}
$dirnames = explode(DS, $mydir);
$zn = $dirnames[count($dirnames)-1];

if (count($files) == 1) {
    // only a single file to download, so don't zip it
    $filename = pathinfo($files[0], PATHINFO_BASENAME);
    $mime = mime_content_type($files[0]);
    $filepath = $files[0];
    
    header('Content-Type: ' . $mime);
    header('Content-disposition: attachment; filename=' . $filename);
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else if (count($files) > 1) {
    // set up zip file in tmp directory
    $zipname = $zn . '.zip';
    $zippath = IMAGEBASEDIR . '/.tmp/' . $zipname;
    @unlink($zippath); // delete zip file if one already exists
    $zip = new ZipArchive;
    
    if (count($files) == 0) {
        $return['error'] = true;
        $return['errorText'] .= '<li>There were no files to zip.</li>';
    } else if (!$zip->open($zippath, ZipArchive::CREATE)) {
        $return['error'] = true;
        $return['errorText'] .= "<li><code>$zippath</code> was not created</li>";
    } else {
        $return['zippath'] = $zippath;
        foreach ($files as $file) {
            $name = str_replace($mydir . DS , '', $file);
            if (!file_exists($file)) {
                $return['error'] = true;
                $return['errorText'] .= "<li><code>$name</code> does not exist.</li>";
            } else if (!$zip->addFile($file, $name)) {
                $return['error'] = true;
                $return['errorText'] .= "<li><code>$name</code> could not be added to the zip file.</li>";
            } else {
                $return['added'][] =  "$name";
            }
        }
        $zip->close();
    }
    
    $filesize = filesize($zippath);
    if ($filesize > 0) {
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipname);
        header('Content-Length: ' . $filesize);
        readfile($zippath);
        exit;    
    }
}

scriptReturn($return);

exit;
    
?>