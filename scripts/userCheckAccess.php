<?php

// check if a user has access to something

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$user = $_SESSION['user_id'];
$id = intval($_POST['id']);
$table = $_POST['table'];

if (in_array($table, array('tem','fm','img'))) {
    $q = new myQuery("SELECT COUNT(*) AS c FROM $table WHERE id={$id} AND user_id={$user}");
    $c = $q->get_one();
    
    if ($c == 0) {
        $return['error'] = true;
        $return['errorText'] = "Either the item does't exist or you do not own it.";
    }
} else {
    $return['error'] = true;
    $return['errorText'] = "You cannot check for access in the $table table.";
}

scriptReturn($return);
exit;

?>