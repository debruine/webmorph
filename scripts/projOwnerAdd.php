<?php

// add an owner to a project

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
noguest();

$return = array(
    'error' => false,
    'errorText' => '',
);

$user = $_SESSION['user_id'];
$project = validID($_POST['project']) ? $_POST['project'] : 0;
$adduser = validID($_POST['owner']) ? $_POST['owner'] : 0;
$perm = in_array($_POST['perm'], array('all', 'read-only')) ? $_POST['perm'] : 'all';

$q = new myQuery("SELECT 1 
                  FROM project_user AS pu
                  LEFT JOIN project AS p ON p.id=project_id
                  WHERE project_id='{$project}' 
                    AND pu.user_id='{$user}' 
                    AND (perm = 'all' OR p.user_id='{$user}')");
if ($q->get_affected_rows() > 0) {

    $q = new myQuery("REPLACE INTO project_user (project_id, user_id, perm) VALUES ('{$project}', '{$adduser}', '{$perm}')");
    
    if ($q->get_affected_rows() == 0) {
        $return['error'] = true;
        $return['errorText'] = "This user could not be added to this project";
    }
} else {
    $return['error'] = true;
    $return['errorText'] = "You do not have permission to add users to this project";
}

scriptReturn($return);

exit;

?>

