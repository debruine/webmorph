<?php

// remove an owner from a project

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$user = $_SESSION['user_id'];
$project = $_POST['project'];
$deluser = $_POST['owner'];

$q = new myQuery("SELECT 1 FROM project_user WHERE project_id='{$project}' AND user_id='{$user}'");
if ($q->get_affected_rows() > 0) {
    
    $q = new myQuery("SELECT COUNT(*) as c FROM project_user WHERE project_id='{$project}' GROUP BY project_id");
    
    if ($q->get_one() < 2) {
        $return['error'] = true;
        $return['errorText'] = "This project only has one user. Please add more before deleting users to avoid abandoning projects.";
    } else {
        $q = new myQuery("DELETE FROM project_user WHERE project_id='{$project}' AND user_id='{$deluser}'");
        
        if ($q->get_affected_rows() == 0) {
            $return['error'] = true;
            $return['errorText'] = "This user could not be deleted from this project";
        }
    }
} else {
    $return['error'] = true;
    $return['errorText'] = "You do not have permission to delete users from this project";
}

scriptReturn($return);

exit;

?>

