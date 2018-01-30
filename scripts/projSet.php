<?php

// check project permissions and set project_id SESSION variable

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$proj_id = validID($_POST['project']) ? $_POST['project'] : 0;

$q = new myQuery("SELECT perm
                  FROM project_user
                  WHERE user_id='{$_SESSION['user_id']}'
                    AND project_id='{$proj_id}'");
$return['query'] = $q->get_query();
if ($q->get_num_rows() > 0) {
    $return['perm'] = $q->get_one();                   
    $_SESSION['project_id'] = $proj_id;
} else {
    $return['error'] = true;
    $return['errorText'] = 'You do not have permission to access this project.';
}

scriptReturn($return);

exit;

?>