<?php

// removes a directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => true,
	'errorText' => '',
	'post' => $_POST
);

$dir = preg_replace('@/$@', '', safeFileName($_POST['dirname'])); // remove trailing slash
$basedir = explode('/', $dir);
$deletedir = array_pop($basedir);
$basedir = '/' . implode('/', $basedir);
$basedir = preg_replace('@/$@', '', str_replace('//', '/', $basedir));
$mydir = IMAGEBASEDIR;

if (!chdir(IMAGEBASEDIR . $basedir)) {
	$return['errorText'] .= "Could not change directory to <code>$basedir</code>.";
} else if (strpos(getcwd() . '/', $mydir) !== 0 ) {
	$return['errorText'] .= "The directory <code>$basedir</code> is not in your image directory.";
} else if (!is_dir($deletedir)) {
	$return['errorText'] .= "The directory <code>$basedir/$deletedir</code> could not be found.";
} else {
	// check if there are any files in this directory and delete any images or tems
	// for safety, only goes one level down and doesn't delete directories
	$allowed_ext = array('jpg', 'png', 'gif', 'tem', 'txt', 'pca', 'csv', 'fimg', 'pci');
	$handle = opendir($deletedir);
	$count = 0;
    while (false !== ($entry = readdir($handle))) {
	    if ($entry != "." && $entry != "..") {
        	$ext = pathinfo($entry, PATHINFO_EXTENSION);
        	$path = $deletedir . '/' . $entry;
        /*
	        if (!is_dir($path) && in_array($ext, $allowed_ext)) {
        		unlink($path);
        		$return['info'][$basedir . '/' . $path] = 'deleted';
        		if ($ext != 'tem') {
        			$db_name = str_replace(IMAGEBASEDIR, '', $basedir) . '/' . $path;
        		
					// delete database entry if it is an image
					//$q = new myQuery("SELECT id FROM img WHERE name='{$db_name}'");
					//$id = $q->get_one();
					
					$q = new myQuery("DELETE FROM img WHERE name='{$db_name}'");
					if ($q->get_affected_rows() != 1) {
						$return['info'][$basedir . '/' . $path] = 'deleted (not from db)';
					}
					
					//$q = new myQuery("DELETE FROM tag WHERE id='{$id}'");
					//$return['info'][] = $db_name . ' deleted from database';
				}
			*/

			if (substr($entry, 0, 1) == "." && @unlink($path)) {
        		$return['info'][$basedir . '/' . $path] = 'deleted';
			} else {
            	$count++;
            	$return['info'][$basedir . '/' . $deletedir . '/' . $path] = 'not deleted';
            }
        }
    }
    closedir($handle);
    
    if ($count) {
    	$return['errorText'] .= "The directory contains $count remaining files. Please delete these before you delete the folder.";
    } else if (!rmdir($deletedir)) {
    	$return['errorText'] .= "The directory <code>/$basedir/$deletedir</code> is empty, but could not be deleted.";
    } else {
    	$return['error'] = false;
    }
}

scriptReturn($return);

exit;

?>
