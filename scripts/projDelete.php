<?php

// removes a project

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => ''
);

function pwCheck($user_pw, $stored_pw) {
    $salt = substr($stored_pw, 0, 28) . '$';
    $hash_check = crypt($user_pw, $salt);
    
    return ($stored_pw == $hash_check);
}

$proj_id = intval($_POST['proj_id']);
$user = $_SESSION['user_id'];

$q = new myQuery("SELECT project.user_id as owner, email, perm, password 
                  FROM project 
                  LEFT JOIN project_user ON project_id=project.id 
                  LEFT JOIN user ON (user.id=project.user_id)
                  WHERE project.id={$proj_id}
                    AND project_user.user_id={$user}");
                    

                    
if ($q->get_num_rows() == 0) {
    $return['errorText'] = "You are not a member of this project.";
} else if ($q->get_one(0,'owner') !== $user) {
    $return['errorText'] = "You do not own this project. Please ask the owner (" . $q->get_one(0,'email') . ") to delete it.";
} else if ($q->get_one(0,'perm') == 'read-only') {
    $return['errorText'] = "You have read-only permission on this project. Please change this before deleting the project.";
} else if (!pwCheck($_POST['password'], $q->get_one(0,'password'))) {
    $return['errorText'] = "You did not provide the correct password for your account.";
} else if (!chdir(IMAGEBASEDIR)) {
    $return['errorText'] .= "Could not change directory.";
} else if (!is_dir($proj_id)) {
    $return['errorText'] .= "The directory <code>$proj_id</code> could not be found.";
} else if (!rename($proj_id, 'del_' . $proj_id)) { 
    $return['errorText'] .= "The directory <code>$proj_id</code> could not be deleted.";
} else {
    // clean up database
    $q = new myQuery("DELETE FROM project WHERE id={$proj_id}");
    $q = new myQuery("DELETE FROM project_user WHERE project_id={$proj_id}");
    $q = new myQuery("DELETE FROM img WHERE project_id={$proj_id}");
}

if ($return['errorText'] == '') { $return['error'] = false; }

scriptReturn($return);

exit;

?>
