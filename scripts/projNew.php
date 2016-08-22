<?php

// create a new project

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);


$user = $_SESSION['user_id'];
$name = my_clean($_POST['name']);
$notes = my_clean($_POST['notes']);

$q = new myQuery("INSERT INTO project (user_id, name, dt, notes) 
                  VALUES ('{$user}', '{$name}', NOW(), '{$notes}')");                         
$new_proj_id = $q->get_insert_id();
$mydir = IMAGEBASEDIR . $new_proj_id;

try {
    if (file_exists($mydir)) {
        throw new Exception("<li>{$new_proj_id} already exists</li>");
    } else if (!mkdir($mydir, DIRPERMS)) {
        throw new Exception("<li>{$new_proj_id} could not be created</li>");
    } else {
        mkdir($mydir . '/.tmp', DIRPERMS);
        mkdir($mydir . '/.trash', DIRPERMS);
        copy(DOC_ROOT . '/include/examples/_female_avg.jpg', $mydir . '/_female_avg.jpg');
        copy(DOC_ROOT . '/include/examples/_female_avg.tem', $mydir . '/_female_avg.tem');
        copy(DOC_ROOT . '/include/examples/_male_avg.jpg', $mydir . '/_male_avg.jpg');
        copy(DOC_ROOT . '/include/examples/_male_avg.tem', $mydir . '/_male_avg.tem');
        
        copy(DOC_ROOT . '/include/examples/webmorph_template_batchAvg.txt', $mydir . '/_batchAvg_template.txt');
        copy(DOC_ROOT . '/include/examples/webmorph_template_batchTrans.txt', $mydir . '/_batchTrans_template.txt');
        copy(DOC_ROOT . '/include/examples/webmorph_template_batchEdit.txt', $mydir . '/_batchEdit_template.txt');
        
        $return['project'] = $new_proj_id;
        
        $q = new myQuery("INSERT INTO project_user (project_id, user_id) VALUES ($new_proj_id, $user)");
        $q->set_query("INSERT INTO img (user_id, dt, project_id, name, width, height) "
                    . "VALUES ({$user}, NOW(), {$new_proj_id}, '/_female_avg.jpg', 1350, 1800), "
                    . "       ({$user}, NOW(), {$new_proj_id}, '/_male_avg.jpg', 1350, 1800)");
        
        $_SESSION['projects'][] = $new_proj_id;
    }
} catch (Exception $e) {
    $return['error'] = true;
    $return['errorText'] .=  $e->getMessage();
}

scriptReturn($return);

exit;

/*

DROP TABLE IF EXISTS project;    
CREATE TABLE project (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(8) UNSIGNED,
    name VARCHAR(32) NOT NULL,
    notes TEXT,
    dt DATETIME,
    filemtime INT(10),
    files INT(8),
    size BIGINT,
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS project_user;
CREATE TABLE project_user (
    project_id INT(11) UNSIGNED,
    user_id INT(8) UNSIGNED,
    UNIQUE INDEX (project_id, user_id)
);

// Start by adding personal projects for all existing users

DELETE FROM project;

INSERT INTO project 
SELECT id, id, 
    "My Project", 
    CONCAT(firstname, " ", lastname, " (", email, ") project"),
    regdate
FROM user;

DELETE FROM project_user;

INSERT INTO project_user SELECT id, id FROM user;

// update pref and img tables for projects

REPLACE INTO pref SELECT id, 'default_project', id FROM user;

ALTER TABLE img ADD COLUMN project_id INT(11) AFTER dt;
UPDATE img SET project_id = user_id;
DROP index name ON img;
CREATE UNIQUE INDEX project_name ON img(project_id, name);

// added project size variables 2016-09-22

ALTER TABLE project ADD filemtime INT(10);
ALTER TABLE project ADD files INT(8);
ALTER TABLE project ADD size BIGINT;

*/

?>

