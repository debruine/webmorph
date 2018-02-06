<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

// register logout
$q = new myQuery("UPDATE login SET logouttime=NOW() WHERE user_id='{$_SESSION['user_id']}' 
                    AND logouttime IS NULL ORDER BY logintime DESC LIMIT 1");
                    
// remove any persistent cookies
setcookie('user_id', '', time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
setcookie('id_hash', '', time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
//setcookie('email',   '', time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);

// delete guest projects
$q = new myQuery("SELECT status, project.id 
                    FROM user 
               LEFT JOIN project ON user_id = user.id 
                   WHERE user.id = {$_SESSION['user_id']}");
$proj = $q->get_one_row();
if ($proj['status'] == 'guest') {
    $proj_id = $proj['id'];
    recursive_delete($proj_id);
    $q = new myQuery("DELETE FROM project WHERE id={$proj_id}");
    $q = new myQuery("DELETE FROM project_user WHERE project_id={$proj_id}");
    $q = new myQuery("DELETE FROM img WHERE project_id={$proj_id}");
}

session_destroy();

scriptReturn($return, true); // don't wait for tmp dir emptying

require_once $_SERVER['DOCUMENT_ROOT'] . '/scripts/dirTmpEmpty.php';

?>