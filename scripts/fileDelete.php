<?php

// delete a file in a user's directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

if (count($_POST['files']) < 1) {
    $return['error'] = true;
    $return['errorText'] .= 'There were no files to delete';
} else {    
    $allowed_ext = array('jpg', 'png', 'gif', 'tem', 'txt', 'pca', 'csv', 'fimg', 'pci');
    
    foreach ($_POST['files'] as $name) {
        
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        preg_match("/^\d{1,11}\//", $name, $project);
        $project = str_replace('/', '', $project[0]);
        if (!in_array($project, $_SESSION['projects'])) {
            $return['error'] = true;
            $return['errorText'][$name] = 'unauthorised to change project ' . $project;
        } else if (!in_array($ext, $allowed_ext)) {
            $return['error'] = true;
            $return['errorText'][$name] = 'exists, extension undeleteable';
        } else {
            $file = IMAGEBASEDIR . $name;
            $db_name = $name;
            $path = pathinfo($file);
            $destination = check_file_duplicate(IMAGEBASEDIR . $project . "/.trash/" . $path['filename'] . '.' . $path['extension']);    
            $return['dest'][] = $destination;    
            if (!file_exists($file)) {
                $return['error'] = true;
                $return['errorText'][$db_name] = 'does not exist';
            } else if (!underPath($file)) {
                $return['error'] = true;
                $return['errorText'][$db_name] = 'is not in your image path';
            } else if (!rename($file, $destination)) {
                $return['error'] = true;
                $return['errorText'][$db_name] = 'could not be deleted';
            } else if ($ext == 'jpg') {
                // delete database entry if it is an image
                //$q = new myQuery("SELECT id FROM img WHERE name='{$db_name}'");
                //$id = $q->get_one();
                
                $q = new myQuery("DELETE FROM img WHERE name='{$db_name}'");
                
                if ($q->get_affected_rows() != 1) {
                    //$return['error'] = true;
                    $return['errorText'][$db_name] = 'deleted (not from db)';
                } else {
                    $return['errorText'][$db_name] = 'deleted';
                }
                
                //$q = new myQuery("DELETE FROM tag WHERE id='{$id}'");
            }
        }
    }
}

scriptReturn($return);

exit;

?>