<?php

// edit the name or notes of a project

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);

$user = $_SESSION['user_id'];
$project = intval($_POST['project']);
$newtext = my_clean($_POST['newname']);
$category = $_POST['category'] == "name" ? "name" : "notes";

$q = new myQuery("SELECT 1 FROM project_user WHERE project_id='{$project}' AND user_id='{$user}'");
if ($q->get_affected_rows() > 0) {

	$q = new myQuery("UPDATE project SET {$category} = '{$newtext}' WHERE id={$project}");
	
	if ($q->get_affected_rows() == 0) {
		$return['error'] = true;
		$return['errorText'] = "The project could not be updated";
	}
} else {
	$return['error'] = true;
	$return['errorText'] = "You do not have permission to change this project";
}

scriptReturn($return);

exit;

?>

