<?php

// add an owner to a project

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);

$user = $_SESSION['user_id'];
$project = $_POST['project'];
$adduser = $_POST['owner'];

$q = new myQuery("SELECT 1 FROM project_user WHERE project_id='{$project}' AND user_id='{$user}'");
if ($q->get_affected_rows() > 0) {

	$q = new myQuery("REPLACE INTO project_user (project_id, user_id) VALUES ('{$project}', '{$adduser}')");
	
	if ($q->get_affected_rows() == 0) {
		$return['error'] = true;
		$return['errorText'] = "This user could not be added to this project";
	}
} else {
	$return['error'] = true;
	$return['errorText'] = "You do not have permission to add users to this project";
}

scriptReturn($return);

exit;

?>

