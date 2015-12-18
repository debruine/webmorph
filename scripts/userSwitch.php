<?php

// old function for switching users for Lisa

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);

if ($_SESSION['user_id'] == '1' || $_SESSION['user_id'] == '3') {
	$id = intval($_POST['id']);
	if ($id > 0) {
		// switch user
		$q = new myQuery("SELECT * FROM user WHERE id={$id}");
		$user = $q->get_one_array();
		
		$return['name'] = $user['firstname'] . ' ' . $user['lastname'];
		$return['email'] = $user['email'];
		$_SESSION['user_id'] = $id;
		$return['user_id'] = $id;
		
		setcookie('user_id', $id,      false, '/', $_SERVER['SERVER_NAME']);
		setcookie('id_hash', md5($id), false, '/', $_SERVER['SERVER_NAME']);
	} 
} else {
	$return['error'] = true;
	$return['errorText'] =  "You don't have permission to switch users. ({$_SESSION['user_id']})";
}

scriptReturn($return);

exit;

?>