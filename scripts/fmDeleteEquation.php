<?php

// delete a user's facialmetric equation from the database

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);


$user = $_SESSION['user_id'];
$name = my_clean($_POST['name']);

$q = new myQuery("DELETE FROM fm WHERE user_id='$user' AND name='$name'");

if ($q->get_affected_rows() !== 1) {
	$return['error'] = true;
	$return['errorText'] = $q->get_affected_rows() . ' equations were deleted';
}

scriptReturn($return);

exit;

?>

