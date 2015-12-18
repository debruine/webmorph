<?php

// delete a default template from the database

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);

$user = $_SESSION['user_id'];

// check if user has permission to delete this template
$tem_id = intval($_POST['id']);
$q = new myQuery("SELECT COUNT(*) as c FROM tem WHERE user_id={$user} AND id={$tem_id} AND id>1");
if ($q->get_one() == 0) {
	$return['error'] = true;
	$return['errorText'] = 'You do not have permission to delete this template.';
	header('Content-Type: application/json');
	echo json_encode($return);
	exit;
}

$q = new myQuery("DELETE FROM tem WHERE id={$tem_id};");
$return['tem_deleted'] = $q->get_affected_rows();

if ($return['tem_deleted'] == 0) {
	$return['error'] = true;
	$return['errorText'] = "Template {$tem_id} was not deleted.";
} else {
	$q->set_query("DELETE FROM point WHERE tem_id={$tem_id};");
	$return['points_deleted'] = $q->get_affected_rows();
	$q->set_query("DELETE FROM line WHERE tem_id={$tem_id};");
	$return['lines_deleted'] = $q->get_affected_rows();
}

scriptReturn($return);
exit;

?>