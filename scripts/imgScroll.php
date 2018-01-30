<?php

// get next or prev image in a user's database
// need to add failsafes if the database is out of date (or r-write to look in directories rather than the db)

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
session_write_close();
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$id = $_SESSION['user_id'];


if (array_key_exists('next', $_POST)) {
    $thisImg = my_clean($_POST['next']);
    $order = 1;
} elseif (array_key_exists('prev', $_POST)) {
    $thisImg = my_clean($_POST['prev']);
    $order = -1;
}

preg_match("/^\d{1,11}\//", $thisImg, $project);
$project = str_replace('/', '', $project[0]);

$path = pathinfo(IMAGEBASEDIR . $thisImg);

$dir = $path['dirname'];
$filename = $path['basename'];


$filelist = glob($dir . '/*.{jpg,png,gif}', GLOB_BRACE);

$n = count($filelist);
if ($n) {
    sort($filelist);
    $return['filelist'] = $filelist;
    $key = array_search(IMAGEBASEDIR . $thisImg, $filelist);
    $newkey = ($n + ($key + $order)) % $n;
    $return['img'] = str_replace(IMAGEBASEDIR, '', $filelist[$newkey]);
} else {
    $return['error'] = true;    
}

/*


// get anchor image and order all DB images alphabetically forward (next) or backwards (prev)
if (array_key_exists('next', $_POST)) {
    $thisImg = my_clean($_POST['next']);
    preg_match("/^\d{1,11}\//", $thisImg, $project);
    $project = str_replace('/', '', $project[0]);
    $q = new myQuery("SELECT CONCAT(project_id, name) AS name FROM img WHERE project_id='$project' ORDER BY name");
} elseif (array_key_exists('prev', $_POST)) {
    $thisImg = my_clean($_POST['prev']);
    preg_match("/^\d{1,11}\//", $thisImg, $project);
    $project = str_replace('/', '', $project[0]);
    $q = new myQuery("SELECT CONCAT(project_id, name) AS name FROM img WHERE project_id='$project' ORDER BY name DESC");
} else {
    $return['error'] = true;
    $return['errorText'] = 'No next or prev image';
    $return['img'] = $thisImg;
}

// check for existence of next image
if (!$return['error']) {
    $allIDs = $q->get_col('name');
    $return['allids'] = $allIDs;
    $origID = array_search($thisImg, $allIDs);
    if ($origID === false) {
        // the anchor image is not in the database
        $return['error'] = true;
        $return['errorText'] = "The image {$thisImg} is not in the database";
        $return['img'] = $thisImg;    
    } else {
        $nextID = $origID + 1;
        
        $n_IDs = count($allIDs);
        if ($nextID >= $n_IDs) { $nextID = 0; } // restart if at the end
        $return['img'] = $allIDs[$nextID];
        
        while (!file_exists(IMAGEBASEDIR . $return['img'])) {
            $return['unfound'][] = $return['img'];
            $return['error'] = true;
            $nextID++;
            if ($nextID >= $n_IDs) { $nextID = 0; } // restart if at the end
            $return['img'] = $allIDs[$nextID];
            if ($origID == $nextID) { break; } // break if back to the original 
        }
    }
}

*/

if ($return['error']) {
    // some image files don't exist
    $return['errorText'] .= 'The database will be cleaned.';
    scriptReturn($return, true);
    
    // do the db cleanup in the background (don't make the user wait for it)
    include DOC_ROOT . '/scripts/dbCleanup.php?project=' . $project;
} else {
    scriptReturn($return);
}

exit;

?>