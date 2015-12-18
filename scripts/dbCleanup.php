<?php

// clean up the database to make consistent with file structure

//scriptReturn(array('error' => 'false')); // fix this later for project structure

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);

$user = $_SESSION['user_id'];

function imageFiles($dir) {
	$result = array();
	$acceptable_file_ext = array('jpg', 'png', 'gif');

	$cdir = scandir($dir);
	
	foreach ($cdir as $key => $value) {
		if (substr($value,0, 1) != '.') {
			$filename = $dir . DIRECTORY_SEPARATOR . $value;
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			
			if (is_dir($filename)) {
				$subfiles = imageFiles($filename);
				$result = array_merge($result, $subfiles);
			} else if (in_array($ext , $acceptable_file_ext)) {
				$result[$filename] = $filename;
			}
		}
	}
	
	ksort($result);
	return $result;
}

if ($_GET['project'] > 0) {
	// get one authorised project
    $q = new myQuery("SELECT project_id FROM project_user WHERE project_id='{$_GET['project']}' AND user_id='{$user}'");
    $projects = $q->get_col("project_id");
} else {
	// get all authorised projects
    $q = new myQuery("SELECT project_id FROM project_user WHERE user_id='{$user}'");
    $projects = $q->get_col("project_id");
}

$dircontents = array();

foreach ($projects as $project_id) {
	$dir = IMAGEBASEDIR . $project_id;

	if (!is_dir($dir)) {
		$return['error'] = true;
		$return['errorText'] = 'Your user directory does not exist.';
	} else {
		$dircontents = array_merge($dircontents, imageFiles($dir));
	}
}

$proj_list = implode(",", $projects);
$q = new myQuery("SELECT id, CONCAT(project_id, name) as path FROM img WHERE project_id IN ({$proj_list})");
$imglist = $q->get_assoc(false, 'id', 'path');

// delete missing images from the DB
$deleted_from_db = array();
foreach ($imglist as $id => $name) {
	$path = IMAGEBASEDIR . $name;
	if (!is_file($path)) {
		$q = new myQuery("DELETE FROM img WHERE id='$id'");
		//$q = new myQuery("DELETE FROM tag WHERE id='$id'");
		$deleted_from_db[] = $name;
	} else {
		unset($dircontents[$path]);
	}
}

$return['deleted'] = $deleted_from_db;

// add new images to DB
$added_to_db = array();
foreach ($dircontents as $file) {
	$exp_path = explode("/", str_replace(IMAGEBASEDIR, '', $file));
	$project_id = $exp_path[0];
	unset($exp_path[0]);
	$name = "/" . implode("/", $exp_path);
	$q = new myQuery("INSERT INTO img (id, user_id, dt, name, project_id) VALUES (NULL, $user, NOW(), '$name', '$project_id')");
	$added_to_db[] = $project_id . $name;
}

$return['added'] = $added_to_db;

require_once $_SERVER['DOCUMENT_ROOT'] . '/scripts/dirTmpEmpty.php';

scriptReturn($return); 

?>
