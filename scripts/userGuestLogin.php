<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

$return = array(
    'error' => false,
    'errorText' => '',
    'user' => ''
);

$q = new myQuery("INSERT INTO user (firstname, status, allocation, regdate) VALUES ('guest', 'guest', 128, NOW())");

$userid = $q->get_insert_id();

if ($userid > 0) {
    $return['user'] = $userid;
    $q = new myQuery("INSERT INTO login (user_id, logintime) VALUES ($userid, NOW())");
    
    setcookie('user_id', $userid,      false, '/', $_SERVER['SERVER_NAME']);
    setcookie('id_hash', md5($userid), false, '/', $_SERVER['SERVER_NAME']);
    setcookie('email',   '',           false, '/', $_SERVER['SERVER_NAME']);
    setcookie('hash',    '',           false, '/', $_SERVER['SERVER_NAME']);
    
    // set session variables
    $_SESSION['user_id'] = $userid;
    $_SESSION['guest'] = true;
    
    // set up project
    $q = new myQuery("INSERT INTO project (user_id, name, dt, notes) VALUES 
                        ({$userid}, 'Guest Project', NOW(), 'This project will be deleted when you log out.')");          
    $new_proj_id = $q->get_insert_id();
    $mydir = IMAGEBASEDIR . $new_proj_id;
    
    if ($new_proj_id > 0 && !mkdir($mydir, DIRPERMS)) {
        $return['error'] = true;
        $return['errorText'] .=  '<li>Your default image directory could not be created</li>';
        $q->set_query("DELETE FROM project WHERE id={$new_proj_id}");
    } else {
        $q = new myQuery("INSERT INTO project_user (project_id, user_id) VALUES ($new_proj_id, $userid)");
        $q = new myQuery("INSERT INTO (user_id, pref, prefval) VALUES ($userid, 'default_project', $new_proj_id)");
        addProjImages($new_proj_id, $userid);
    }
} else {
    $return['error'] = true;
    $return['errorText'] .=  '<li>A guest account could not be created.</li>';
}

scriptReturn($return, true);

?>