<?php

// set up data to delineate an image

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();

ini_set('max_execution_time', 10); 

if (array_key_exists('img', $_POST)) {
    if (is_numeric($_POST['img'])) { // get saved image by id number
        $q = new myQuery('SELECT name FROM img WHERE id=' . intval($_POST['img']));
        $name = '/images' . $q->get_one();
        echo $name; exit;
    } else {
        $name = my_clean($_POST['img']);
    }

    $imgname = str_replace("//", "/", $name);
    if (substr($imgname, -4) == '.tem') {
        // find the actual image associated with this tem if only a tem was passed
        $imgname = preg_replace('@\.(jpg|png|gif|tem)$@', '.jpg', $imgname);
        if (!file_exists(IMAGEBASEDIR . $imgname)) {
            $imgname = preg_replace('@\.(jpg|png|gif|tem)$@', '.png', $imgname);
        } else if (!file_exists(IMAGEBASEDIR . $imgname)) {
            $imgname = preg_replace('@\.(jpg|png|gif|tem)$@', '.gif', $imgname);
        } else if (!file_exists(IMAGEBASEDIR . $imgname)) {
            $imgname = preg_replace('@\.(jpg|png|gif|tem)$@', '.tem', $imgname);
        }
    }
    list($width, $height) = getimagesize(IMAGEBASEDIR . $imgname);
    
    // read saved tem
    $temfile = IMAGEBASEDIR . preg_replace('@\.(jpg|png|gif)$@', '.tem', $imgname);
    if (file_exists($temfile)) {
        $rawtem = file($temfile);
        
        $mytem = array();
        // remove any comment lines
        foreach ($rawtem as $line) {
            if (preg_match('/^[0-9\.\s]+$/', $line)) {
                $mytem[] = $line;
            }
        }
        
        if (count($mytem) > 0) {
            $pointNumber = trim($mytem[0]);
            $temPoints = array_slice($mytem, 1, $pointNumber);
            $temPoints = array_map(function($n) { 
                $pts = preg_split('/\s+/', $n);
                return array($pts[0], $pts[1]);
            }, $temPoints);
            
            $lineVectors = array();
            for ($i = $pointNumber+4; $i < count($mytem); $i += 3) {
                $lineVectors[] = explode(" ", trim($mytem[$i]));
                
                if (trim($mytem[$i-2]) == 1) {
                    // if a closed loop, add the first point to the end again
                    $n = count($lineVectors) - 1;
                    $lineVectors[$n][] = $lineVectors[$n][0];
                }
            }
        }
    }

    $return = array(
        'originalWidth' => $width,
        'originalHeight' => $height,
        'imgname' => $imgname,
        'pointNumber' => $pointNumber,
        'temPoints' => $temPoints,
        'lineVectors' => $lineVectors
    );
    
    scriptReturn($return);
    //echo json_encode($return, JSON_NUMERIC_CHECK);
}

?>

