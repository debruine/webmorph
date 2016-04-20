<?php

// add a user's mask structure to the database

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);


$user = $_SESSION['user_id'];
$name = my_clean($_POST['name']);
$tem_id = intval( $_POST['tem_id'] );

// clean the mask

$mask = preg_replace('/\s+/', '', $_POST['mask']); // Remove whitespaces

$blank_mask = str_replace( 
    array(    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ',', ';', ':'    ), // only allowed characters
    '', 
    $mask
);

if (!empty($blank_mask)) { 
    $return['error'] = true;
    $return['errorText'] .= 'The equation was not valid. These characters need to be removed: ' . $blank_mask;
} else {
    $q = new myQuery("REPLACE INTO mask (user_id, name, tem_id, mask) 
                        VALUES ('$user', '$name', '$tem_id', '$mask')");
                        
    if ($q->get_affected_rows() == 1) {
        $return['name'] = $name;
        $return['mask'] = $mask;
        $return['tem_id'] = $tem_id;
    } else {
        $return['name'] = $q->get_affected_rows();
    }
}
scriptReturn($return);

exit;

/*
    
CREATE TABLE mask (
    user_id INT(8) UNSIGNED,
    name VARCHAR(32) NOT NULL,
    tem_id INT(11),
    mask TEXT,
    UNIQUE INDEX (user_id, name)
);

*/

?>

