<?php

// add the symmetry points to a tem

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$user = $_SESSION['user_id'];
$tem_id = intval($_POST['tem_id']);
$sym = $_POST['sym'];


$q = new myQuery("SELECT COUNT(*) AS c FROM point LEFT JOIN tem ON tem.id=tem_id WHERE tem_id={$tem_id} AND user_id={$user}");
$c = $q->get_one();

if ($c == 0) {
    $return['error'] = true;
    $return['errorText'] = "Either the template ({$tem_id}) does't exist or you do not own it.";
} else if ($c != count($sym)) {
    $return['error'] = true;
    $s = count($sym);
    $return['errorText'] = "This template ({$tem_id}) has {$c} points, but you provided {$s} symmetry points.";
}

foreach ($sym as $n=>$s) {
    $q = new myQuery("UPDATE point SET sym={$s} WHERE tem_id={$tem_id} AND n={$n}");
}


scriptReturn($return);
exit;

?>