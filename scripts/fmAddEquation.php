<?php

// add a user's facialmetric equation to the database

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

// clean the equation

// Remove whitespaces
$eq = preg_replace('/\s+/', '', $_POST['eq']);

$blank_eq = str_replace( 
    array(    'abs(', 'min(', 'max(', 'atan(', 'asin(', 'acos(', 'tan(', 'sin(', 'cos(', 'sqrt(', 'pow(', 'rad2deg(',
            'x[', 'y[', ']', '(', ')', '.', ',',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 
            '+', '-', '*', '/'
    ), 
    '', 
    $eq
);

if (!empty($blank_eq)) { 
    $return['error'] = true;
    $return['errorText'] .= 'The equation was not valid. These characters need to be removed: ' . $blank_eq;
} else {
    $user = $_SESSION['user_id'];
    $name = my_clean($_POST['name']);
    $desc = my_clean($_POST['desc']);
    
    $q = new myQuery("REPLACE INTO fm (user_id, name, description, equation) 
                        VALUES ('$user', '$name', '$desc', '{$_POST['eq']}')");
                        
    if ($q->get_affected_rows() == 1) {
        $return['name'] = $name;
        $return['desc'] = $desc;
        $return['eq'] = $_POST['eq'];
    } else {
        $return['name'] = $q->get_affected_rows();
    }
}

scriptReturn($return);

exit;

?>

