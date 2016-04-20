<?php

// get a list of users

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

if ($_SESSION['user_id'] == '1') {
    $q = new myQuery("SELECT id, CONCAT(lastname, ', ', firstname, ' (', email, ')') as name FROM user ORDER BY name");
    $return['users'] = $q->get_assoc();    
} else {
    $return['error'] = true;
    $return['errorText'] =  "You don't have permission to list users.";
}

scriptReturn($return);

exit;

?>