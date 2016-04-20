<?php

// get or set a user's read messages

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
    'read_msg_ids' => array()
);

$user = $_SESSION['user_id'];

if (empty($user)) {
    $return['error'] = true;
    $return['errorText'] = "No user is logged in";
} else {
    if (array_key_exists('msg_id', $_POST)) {
        // mark a message as read
        $msg_id = my_clean($_POST['msg_id']);
        $q = new myQuery("INSERT INTO msg (id, user_id, dt) VALUES ('$msg_id', '$user', NOW())");
    }
    
    // get all read messages for this user
    $q = new myQuery("SELECT id FROM msg WHERE user_id='$user'");
    $return['read_msg_ids'] = $q->get_col('id');
}

scriptReturn($return);

exit;

/*

CREATE TABLE msg (
    id VARCHAR(32),
    user_id INT(8) UNSIGNED,
    dt DATETIME,
    INDEX (user_id)
);

*/

?>
