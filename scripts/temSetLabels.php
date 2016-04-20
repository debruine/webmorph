<?php

// add the point labels to the points table in the database

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$user = $_SESSION['user_id'];
$tem_id = intval($_POST['tem_id']);
$labels = $_POST['labels'];


$q = new myQuery("SELECT COUNT(*) AS c FROM point LEFT JOIN tem ON tem.id=tem_id WHERE tem_id={$tem_id} AND user_id={$user}");
$c = $q->get_one();

if ($c == 0) {
    $return['error'] = true;
    $return['errorText'] = "Either the template ({$tem_id}) does't exist or you do not own it.";
} else if ($c != count($labels)) {
    $return['error'] = true;
    $s = count($labels);
    $return['errorText'] = "This template ({$tem_id}) has {$c} points, but you provided {$s} labels.";
}

foreach ($labels as $n=>$l) {
    $q = new myQuery("UPDATE point SET name='{$l}' WHERE tem_id={$tem_id} AND n={$n}");
}

scriptReturn($return);
exit;

?>