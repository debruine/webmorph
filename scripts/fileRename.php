<?php

// rename a file in a user's directory

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => ''
);

$newname = safeFileName($_POST['newname']);
$oldurl = safeFileName($_POST['oldurl']);
$basedir = explode('/', $oldurl);
$oldname = array_pop($basedir);
$basedir = implode('/', $basedir);

if ($newname == $oldname) {
    $return['error'] = false;
    $return['newurl'] = $basedir . '/' . $newname;
    $return['newFileName'] = $basedir . '/' . $newname;
} else if ($newname == '') {
    $return['errorText'] .= 'You cannot change the name to &ldquo;' . $_POST['newname'] . '&rdquo;';
} else if (!chdir(IMAGEBASEDIR . $basedir)) {
    $return['errorText'] .= 'Could not change directory to /' . $basedir;
} else if (is_file($newname)) {
    $return['errorText'] .= '/' . $basedir . '/' . $newname . ' already exists';
} else if (!rename($oldname, $newname)) {
    $return['errorText'] .= "Could not rename $oldname to $newname";
} else {
    $return['error'] = false;
    
    if (!$_POST['nochangetem']) {
        // try to change tem if exists
        if (substr($oldname, -4) !== '.tem') {
            $oldtem = substr($oldname, 0, -4) . '.tem';
            $newtem = substr($newname, 0, -4) . '.tem';
            if (is_file($oldtem) && !is_file($newtem)) {
                rename($oldtem, $newtem);
            }
        }
    }
    
    $return['newurl'] = $basedir . '/' . $newname;
    $return['newFileName'] = $basedir . '/' . $newname;
    
    // change DB entry for jpegs
    if (in_array(substr($oldname, -4), array('.jpg', '.png', '.gif'))) {
        $old = $basedir . '/' . $oldname;
        $new = $basedir . '/' . $newname;
        
        $exp_path = explode("/", $new);
        $project_id = $exp_path[0];
        $old = substr($old, strlen($project_id));
        $new = substr($new, strlen($project_id));
        
        $q = new myQuery("UPDATE img SET name='{$new}' WHERE name='{$old}' AND project_id='{$project_id}'");
        $return['images_updated'] = $q->get_affected_rows();
    }        
}

scriptReturn($return);

exit;

?>
