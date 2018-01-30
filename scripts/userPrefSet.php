<?php

// set a user's preferences and personal data

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '<ol>',
);

$user = $_SESSION['user_id'];

$colors = array("mask_color", "cross_color", "selcross_color", "line_color");

foreach($colors as $c) {
    $rgb = $_POST[$c];
    if (count($rgb) == 3) {
        $_POST[$c] = 'rgb(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ')';
    } else if (count($rgb) == 4) {
        $_POST[$c] = 'rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',' . round($rgb[3]/255, 2) . ')';
    }
}

$rgba = '/^(rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)|rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0|1|)(\.\d+)?\s*\))$/';
$bool = '/^(true|false)$/';
$dbl = '/^\d{1,5}(\.\d{1,5})?$/';
$int5 = '/^\d{1,5}$/';


$validPrefvals = array(
    'mask_color' => $rgba,
    'cross_color' => $rgba,
    'selcross_color' => $rgba,
    'line_color' => $rgba,
    'defaultLineWidth' => '/^\d{1,2}$/',
    'texture' => $bool,
    'sample_contours' => $bool,
    'show_thumbs' => $bool,
    'align_pt1' => $int5,
    'align_pt2' => $int5,
    'align_x1' => $dbl,
    'align_y1' => $dbl,
    'align_x2' => $dbl,
    'align_y2' => $dbl,
    'align_w' => $int5,
    'align_h' => $int5,
    'defaultTem' => '/^\d{1,11}$/',
    'normalisation' => '/^(none|twopoint|threepoint|rigid)$/',
    'warp' => '/^(multiscale|linear|tps|multiscalerb)$/',
    'default_imageformat' => '/^(jpg|png|gif)$/',
    'batch_names' => '/^(prefix|suffix|folder)$/',
    'default_project' => '/^\d{1,11}$/',
    'theme' => '/^-?\d{1,3}$/'
);

$userInfo = array(
    'email' => '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/',
    'password' => '/^.{6,}$/',
    'firstname' => '/^.*$/',
    'lastname' => '/^.*$/',
    'organisation' => '/^.*$/',
    'sex' => '/^(female|male|other)$/',
    'research' => '/^(0|1)$/',
    'school' => '/^(0|1)$/',
    'business' => '/^(0|1)$/',
    'art' => '/^(0|1)$/',
    'personal' => '/^(0|1)$/',
);
    
$validPrefs = array_merge(array_keys($validPrefvals), array_keys($userInfo));

foreach ($_POST as $pref => $prefval) {
    $pref = trim($pref);
    $prefval = trim($prefval);
    
    if ($pref == 'password' && empty($prefval)) {
        // do nothing
    } else if ($pref == 'password' && preg_match($userInfo[$pref], $prefval)) {
        $salt = '$2y$10$' . substr(md5(microtime()), 0, 21) . '$';
        $hash = crypt($prefval, $salt);
        $q = new myQuery("UPDATE user SET password='$hash' WHERE id='$user'");
    } else if (!in_array($pref, $validPrefs)) {
        $return['error'] = true;
        $return['errorText'] .= "<li><code>$pref</code> is not a valid preference type.</li>";
    } else if (array_key_exists($pref, $validPrefvals) && preg_match($validPrefvals[$pref], $prefval)) {
        $q = new myQuery("REPLACE INTO pref (user_id, pref, prefval) VALUES ('$user', '$pref', '$prefval')");
    } else if (array_key_exists($pref, $userInfo) && preg_match($userInfo[$pref], $prefval)) {
        $q = new myQuery("UPDATE user SET $pref='$prefval' WHERE id='$user'");
    } else {
        $return['error'] = true;
        $return['errorText'] .=  "<li><code>$prefval</code> is not a valid value for the preference <code>$pref</code>.</li>";
    }
}
$return['errorText'] .= "</ol>";

scriptReturn($return);

exit;

/*

CREATE TABLE pref (
    user_id INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    pref VARCHAR(255) NOT NULL,
    prefval VARCHAR(255) NOT NULL,
    UNIQUE INDEX (user_id, pref)
);

REPLACE INTO pref VALUES 
    (1, 'mask_colour', 'rgb(127,0,0)'), 
    (1, 'texture', 'false'), 
    (1, 'sample_contours', 'true'),
    (1, 'show_tems', 'true')
;

*/

?>