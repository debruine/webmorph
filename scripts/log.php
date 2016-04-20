<?php

// log details of averages and transforms

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
);

$norm = array(
    "none" => 1,
    "2point" => 2,
    "rigid" => 3
);

$warp = array(
    'multiscale' => 1, 
    'linear' => 2, 
    'tps' => 3, 
    'multiscalerb' => 4
);
$format = array(
    "jpg" => 1,
    "gif" => 2,
    "png" => 3
);

if (array_key_exists('average-time', $_POST)) {
    $imgtype = substr($_POST['images'][0], -3);
    $avgtype = substr($_POST['img'], -3);

    $query = sprintf("INSERT INTO avg_log (user_id, n, imgtype, avgtype, width, height, norm, texture, contours, memory, load_time, make_time, dt) VALUES (%d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, NOW())",
        $_SESSION['user_id'],
        count($_POST['images']),
        intval($format[$imgtype]),
        intval($format[$avgtype]),
        intval($_POST['width']),
        intval($_POST['height']),
        intval($norm[$_POST['norm']]),
        $_POST['texture']=='true' ? 1 : 0,
        $_POST['contours']=='true' ? 1 : 0,
        intval($_POST['memory']),
        intval($_POST['load-image-time']),
        intval($_POST['average-time'])
    );
    $return['query'] = $query;
    $q = new myQuery($query);
    
} else if (array_key_exists('transform-time', $_POST)) {
    $imgtype = substr($_POST['transimg'], -3);
    $transtype = substr($_POST['img'], -3);
    
    $query = sprintf("INSERT INTO trans_log (user_id, imgtype, transtype, shape, color, texture, width, height, norm, warp, contours, memory, load_time, make_time, dt) VALUES (%d, %d, %d, %f, %f, %f, %d, %d, %d, %d, %d, %d, %d, %d, NOW())",
        $_SESSION['user_id'],
        intval($format[$imgtype]),
        intval($format[$transtype]),
        floatval(str_replace('%', '', $_POST['shape'])),
        floatval(str_replace('%', '', $_POST['color'])),
        floatval(str_replace('%', '', $_POST['texture'])),
        intval($_POST['width']),
        intval($_POST['height']),
        intval($norm[$_POST['norm']]),
        intval($warp[$_POST['warp']]),
        $_POST['contours']=='true' ? 1 : 0,
        intval($_POST['memory']),
        intval($_POST['load-image-time']),
        intval($_POST['transform-time'])
    );
    $return['query'] = $query;
    $q = new myQuery($query);
} else {
    $return['error'] = true;
    $return['errorText'] .= 'Only averages and transforms can be logged';
}

scriptReturn($return);
exit;


/*
DROP TABLE avg_log;
CREATE TABLE avg_log (
    user_id INT(8) UNSIGNED,
    n INT(4) UNSIGNED,
    imgtype ENUM('jpg','gif','png') DEFAULT 'jpg',
    avgtype ENUM('jpg','gif','png') DEFAULT 'jpg',
    width INT(5) UNSIGNED,
    height INT(5) UNSIGNED,
    norm ENUM('none','2point','rigid') DEFAULT 'none',
    texture BOOL,
    contours BOOL,
    memory INT(6) UNSIGNED,
    load_time INT(6) UNSIGNED,
    make_time INT(7) UNSIGNED,
    dt datetime
);
DROP TABLE trans_log;
CREATE TABLE trans_log (
    user_id INT(8) UNSIGNED,
    imgtype ENUM('jpg','gif','png') DEFAULT 'jpg',
    transtype ENUM('jpg','gif','png') DEFAULT 'jpg',
    shape INT(3),
    color INT(3),
    texture INT(3),
    width INT(5) UNSIGNED,
    height INT(5) UNSIGNED,
    norm ENUM('none','2point','rigid') DEFAULT 'none',
    warp ENUM('multiscale', 'linear', 'tps', 'multiscalerb') DEFAULT 'multiscale',
    contours BOOL,
    memory INT(6) UNSIGNED,
    load_time INT(6) UNSIGNED,
    make_time INT(7) UNSIGNED,
    dt datetime
);

*/

?>