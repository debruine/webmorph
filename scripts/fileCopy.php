<?php

// copy a file to another location

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

function recurse_copy($src,$dst) {    
    $success = true;
    
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                $success = $success && recurse_copy($src . '/' . $file,$dst . '/' . $file);
            } else {
                $success = $success && copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
    
    return $success;
} 

$toDir = IMAGEBASEDIR . $_POST['toDir'];
$func = ($_POST['action'] == 'move') ? 'rename' : 'copy';

if (!file_exists($toDir)) {
    $return['error'] = true;
    $return['errorText'] = "The directory <code>{$_POST['toDir']}</code> does not exist.";
} else if (count($_POST['files']) < 1) {
    $return['error'] = true;
    $return['errorText'] = 'There were no files to copy.';
} else {
    $return['errorText'] = '<ol>';
    foreach ($_POST['files'] as $file) {
        $source = IMAGEBASEDIR . $file;
        $is_dir = is_dir($source);
        $name = basename($file);
        $is_img = (substr($name, -4) == '.tem' || $is_dir) ? false : true;
        $destination = check_file_duplicate($toDir . $name);
        
        $return['source'] = $source;
        $return['destination'] = $destination;
        $return['is_dir'] = $is_dir ? 'yes' : 'no';
        
        if (!file_exists($source)) {
            $return['error'] = true;
            $return['errorText'] .= "<li>The file <code>$file</code> does not exist.</li>";
        } else if (file_exists($destination)) {
            $return['error'] = true;
            $return['errorText'] .= "<li>The file <code>{$_POST['toDir']}/{$name}</code> already exists, so <code>{$file}</code> was not copied.</li>";
        } else if (!($is_dir && $func == 'copy') && !$func($source, $destination)) {
            $return['error'] = true;
            $return['errorText'] .= "<li>The file <code>{$file}</code> could not be copied to <code>{$_POST['toDir']}/{$name}</code>.</li>";
        } else if ($is_dir && $func == 'copy' && !recurse_copy($source, $destination)) {
            $return['error'] = true;
            $return['errorText'] .= "<li>The folder <code>{$file}</code> could not be copied to <code>{$_POST['toDir']}/{$name}</code>.</li>";
        } else if ($is_img) {
            $oldname = str_replace(IMAGEBASEDIR, '', $source);
            $newname = str_replace(IMAGEBASEDIR, '', $destination);
            if ($func == 'copy') {
                $q = new myQuery("INSERT INTO img (user_id, dt, name) VALUES ('{$_SESSION['user_id']}', NOW(), '{$newname}')");
            } else if ($func == 'rename') {
                $q = new myQuery("UPDATE img SET name='{$newname}' WHERE name='{$oldname}' AND user_id='{$_SESSION['user_id']}'");
            }
        } else if ($is_dir) {
            $olddir = str_replace(IMAGEBASEDIR, '', $source);
            $newdir = str_replace(IMAGEBASEDIR, '', $destination);
            if ($func == 'copy') {
                $q = new myQuery("INSERT INTO img (user_id, dt, name) SELECT '{$_SESSION['user_id']}', NOW(), REPLACE(name, '{$olddir}', '{$newdir}/') FROM img WHERE user_id='{$_SESSION['user_id']}' AND name REGEXP '^{$olddir}[^/]+$'");
            } else if ($func == 'rename') {
                $q = new myQuery("UPDATE img SET name = REPLACE(name, '{$olddir}', '{$newdir}/') WHERE user_id='{$_SESSION['user_id']}' AND name REGEXP '^{$olddir}[^/]+$'");
            }
            $return['query'] = $q->get_query();
        }
        
    }
    $return['errorText'] .= '</ol>';
}

scriptReturn($return);

exit;

?>