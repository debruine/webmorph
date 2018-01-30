<?php

// clean up the database to make consistent with file structure

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

if ($_SESSION['user_id'] !== '1') exit;

$return = array();

function recursive_copy($source, $dest){
    global $return;
    
    if(is_dir($source)) {
        $dir_handle=opendir($source);
        while($file=readdir($dir_handle)){
            if($file!="." && $file!=".."){
                if(is_dir($source."/".$file)){
                    if(!is_dir($dest."/".$file)){
                        mkdir($dest."/".$file);
                    }
                    recursive_copy($source."/".$file, $dest."/".$file);
                } else {
                    copy($source."/".$file, $dest."/".$file);
                    $return['copied'][] = $source."/".$file;
                }
            }
        }
        closedir($dir_handle);
    } else {
        copy($source, $dest);
        $return['copied'][] = $source;
    }
}

function recursive_delete($deletedir) {
    global $return;
    
    if (is_dir($deletedir)) {
        $handle = opendir($deletedir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $ext = pathinfo($entry, PATHINFO_EXTENSION);
                $path = $deletedir . '/' . $entry;
                if (!is_dir($path)) {
                    unlink($path);
                    $return['deleted files'][] = $path;
                } else {
                    recursive_delete($path);
                }
            }
        }
        closedir($handle);
        rmdir($deletedir);
        $return['deleted dirs'][] = $deletedir;
    } else {
        $return[$deletedir] = 'does not exist';
    }
}

// copy a dir
//$source = IMAGEBASEDIR . 'align';
//$dest = IMAGEBASEDIR . '171';
//recursive_copy($source, $dest);

// delete a specific directory
$deletedir = IMAGEBASEDIR . 'UVA';
//recursive_delete($deletedir);

// remake .tmp directories
/*
for ($i = 1; $i < 150; $i++) {
   $dir = '/var/www/html/images/' . $i; 
   if (is_dir($dir)) {
       mkdir($dir . '/.tmp', DIRPERMS);
       $return[] = $dir . '/.tmp';
   }
}
*/

header('Content-Type: text/html');
echo "<h1>Maintainance Script</h1>";
echo htmlArray($return);

exit;