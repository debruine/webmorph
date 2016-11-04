<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

$return = array(
    'error' => false,
    'errorText' => '',
    'user' => ''
);

$email = my_clean($_POST['email']);
$password = my_clean($_POST['password']);

if (empty($email)) {
    $return['error'] = true;
    $return['errorText'] .= '<li>' .$email . ' is an invalid email address</li>';
} else if (empty($password)) {
    $return['error'] = true;
    $return['errorText'] .=  '<li>Your password is not valid</li>';
} else {
    $q = new myQuery("SELECT id, firstname, lastname, email, password, status FROM user WHERE LCASE(email)=LCASE('$email')");
    
    if ($q->get_num_rows() == 1) {
        $res = $q->get_one_array();
        $id = intval($res['id']);
        $hash = $res['password'];
        $salt = substr($hash, 0, 28) . '$';
        $hash_check = crypt($password, $salt);
        
        
        if ($res['status'] == 'requested') {
            $q = new myQuery("SELECT COUNT(*) as c, COUNT(IF(id <= $id,1,NULL)) as me FROM user WHERE status='requested'");
            $wait_list = $q->get_one_row();
            
            $return['error'] = true;
            $return['errorText'] .=  "<li>Your account has not been authorized yet. 
                                        Because WebMorph is in alpha testing, we are limiting the number of users. 
                                        You are number {$wait_list['me']} of {$wait_list['c']} on the wait list.</li>";
        } else if ($res['status'] == 'disabled') {
            $return['error'] = true;
            $return['errorText'] .=  "<li>Your account has been disabled.</li>";
        } else if ($hash == $hash_check) {
            $return['user'] = $id;
            
            $q = new myQuery("INSERT INTO login (user_id, logintime) VALUES ($id, NOW())");
            
            // set session variables
            $_SESSION['user_id'] = $id;
            if ($id == 1) { $_SESSION['superuser'] = true; }
            
            // check if they have any project folders
            $q = new myQuery("SELECT project_id FROM project_user WHERE user_id={$id}");
            
            if ($q->get_num_rows() == 0) {
                $notes = my_clean("{$res['firstname']} {$res['lastname']} ({$res['email']}) first project");
                $projname = my_clean("{$res['firstname']} {$res['lastname']} Project");
                
                $q = new myQuery("INSERT INTO project (user_id, name, dt, notes) VALUES ({$id}, '{$projname}', NOW(), '{$notes}')");          
                $new_proj_id = $q->get_insert_id();
                $mydir = IMAGEBASEDIR . $new_proj_id;
                
                if ($new_proj_id > 1 && !mkdir($mydir, DIRPERMS)) {
                    $return['error'] = true;
                    $return['errorText'] .=  '<li>Your default image directory could not be created</li>';
                    $q->set_query("DELETE FROM project WHERE id={$new_proj_id}");
                } else {
                    mkdir($mydir . '/.tmp', DIRPERMS);
                    mkdir($mydir . '/.trash', DIRPERMS);
                    
                    $example_images = array(
                        "f_african", "f_easian", "f_multi", "f_wasian", "f_white", 
                        "m_african", "m_easian", "m_multi", "m_wasian", "m_white.jpg"
                    );
                    
                    foreach ($example_images as $img) {
                        copy(DOC_ROOT . "/include/examples/{$img}.jpg", "{$mydir}/{$img}.jpg");
                        copy(DOC_ROOT . "/include/examples/{$img}.tem", "{$mydir}/{$img}.tem");
                    }
                        
                    copy(DOC_ROOT . '/include/examples/_female_avg.jpg', $mydir . '/3dsk_female_avg.jpg');
                    copy(DOC_ROOT . '/include/examples/_female_avg.tem', $mydir . '/3dsk_female_avg.tem');
                    copy(DOC_ROOT . '/include/examples/_male_avg.jpg', $mydir . '/3dsk_male_avg.jpg');
                    copy(DOC_ROOT . '/include/examples/_male_avg.tem', $mydir . '/3dsk_male_avg.tem');

                    $q = new myQuery("INSERT INTO project_user (project_id, user_id) VALUES ($new_proj_id, $id)");
                    $q = new myQuery("INSERT INTO (user_id, pref, prefval) VALUES ($id, 'default_project', $new_proj_id)");
                }
            }

            // set remember cookie 
            if ($_POST['login_keep']) {
                // set cookie to expire in a year
                setcookie('user_id', $id,      time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
                setcookie('id_hash', md5($id), time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
                setcookie('email',   $email,   time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
            } else {
                setcookie('user_id', $id,      false, '/', $_SERVER['SERVER_NAME']);
                setcookie('id_hash', md5($id), false, '/', $_SERVER['SERVER_NAME']);
                setcookie('email',   $email,   false, '/', $_SERVER['SERVER_NAME']);
            }
        } else {
            $return['error'] = true;
            $return['errorText'] .=  "<li>The password is incorrect.</li>";
        }
    } else {
        $return['error'] = true;
        $return['errorText'] .=  '<li>The username is incorrect.</li>';
    }
}

$db->close();

scriptReturn($return, true);
    
// do the db cleanup in the background (don't make the user wait for it)
include DOC_ROOT . '/scripts/dbCleanup.php';

/*

CREATE TABLE login (
    user_id INT(8) UNSIGNED,
    logintime DATETIME,
    logouttime DATETIME,
    INDEX (user_id)
);

*/

?>