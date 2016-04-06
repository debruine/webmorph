<?php

// get a user's authorised projects

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

function countFilesProc($dir) {
	$ret = array();
    $odir = opendir($dir);
    while(($currentFile = readdir($odir)) !== false) {
        if ( $currentFile == '.' or $currentFile == '..' ) {
            continue;
        } else if (is_dir($dir . '/' . $currentFile)) {
	        $ret2 = countFilesProc($dir . '/' . $currentFile);
	        $ret['files'] += $ret2['files'];
	        $ret['size'] += $ret2['size'];
	    } else {
        	$ret['files']++;
        	$ret['size'] += filesize($dir . '/' . $currentFile);
        }
    }
    closedir($odir);
	return $ret;
}

$user = $_SESSION['user_id'];
$proj_id = $_POST['proj_id'];

$q = new myQuery("SELECT project.user_id as owner 
                  FROM project 
                  LEFT JOIN project_user ON (project.id=project_id)
                  WHERE project_user.user_id='$user' AND project_id='$proj_id'");

if ($q->get_num_rows() > 0) {
	$return['filemtime'] = filemtime(IMAGEBASEDIR . $proj_id);
	if ($return['filemtime'] != $_POST['filemtime']) {
	    $res = countFilesProc(IMAGEBASEDIR . $proj_id);
	    if ($res) {
		    $return['mysize'] = ($q->get_one() == $user) ? $res['size'] : 0;
			$return['files'] = $res['files'] | 0;
			$return['trash'] = $res['trash'] | 0;
			$return['tmp'] = $res['tmp'] | 0;
			$return['size'] = formatBytes($res['size']);
		}
	}
}

scriptReturn($return);

exit;

?>