<?php

// get a user's preferences and personal data

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
	'prefs' => array(
		'mask_color' => 'rgb(255,255,255)',
		'cross_color' => 'rgb(0,255,0)',
		'selcross_color' => 'rgb(255,0,0)',
		'line_color' => 'rgb(0,0,255)',
		'default_line_width' => 1,
		'texture' => 'true',
		'sample_contours' => 'true',
		'show_thumbs' => 'false',
		'align_pt1' => 0,
		'align_pt2' => 1,
		'align_x1' => 496.980,
		'align_y1' => 825.688,
		'align_x2' => 853.020,
		'align_y2' => 825.688,
		'align_w' => 1350,
		'align_h' => 1800,
		'default_tem' => 1,
		'normalisation' => 'none',
		'warp' => 'multiscale',
		'default_imageformat' => 'jpg',
		'batch_names' => 'folder',
		'default_project' => NULL,
		'theme' => 0
	),
	'default_templates' => array('id' => 1, 'name' => 'FRL-189'),
	'fm' => array()
);

$user = $_SESSION['user_id'];
$return['user'] = $user;

if (empty($user)) {
	$return['error'] = true;
} else {

	$q = new myQuery("SELECT pref, prefval FROM pref WHERE user_id='$user'");
	$myprefs = $q->get_assoc(false, 'pref', 'prefval');
	
	foreach ($myprefs as $pref => $val) {
		$return['prefs'][$pref] = $val;
	}
	
	$q = new myQuery("SELECT * FROM user WHERE id='$user'");
	$userinfo = $q->get_one_array();
	unset($userinfo['password']);
	$return['prefs']= array_merge($return['prefs'], $userinfo);
	
	$q = new myQuery("SELECT id, tem.name, notes, 
						COUNT(DISTINCT l.n) as `lines`, 
						COUNT(DISTINCT p.n) AS points
						FROM tem
						LEFT JOIN point AS p ON (tem.id=p.tem_id)
						LEFT JOIN line AS l ON (tem.id=l.tem_id)
						WHERE user_id='$user' OR public=TRUE
						GROUP BY tem.id");
	$return['default_templates'] = $q->get_assoc();
	
	$_SESSION['theme'] = $return['prefs']['theme'];
	
	$q = new myQuery("SELECT name, description, equation FROM fm WHERE user_id='$user'");
	$return['fm'] = $q->get_assoc();
}

scriptReturn($return);

exit;

?>