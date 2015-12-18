<?php

// average selected images and tems and save the output

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => true,
	'errorText' => '',
	'newfilename' => ''
);

$url = 'http://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/avg?';

// set up data
$theData = $_POST['theData'];
$paramsJoined = array();
foreach($theData as $param => $value) {
	if (is_array($value)) {
		foreach($value as $subvalue) {
			$paramsJoined[] = "$param=$subvalue";
		}
	} else {
		$paramsJoined[] = "$param=$value";
	}
}
$query = implode('&', $paramsJoined);
$return['q'] = $query;

$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, $url . $query);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
$data = curl_exec($ch);
curl_close($ch);

$transdata = json_decode($data, true);
if (count($transdata) == 0) {
	$return['errorText'] .= 'The average was not created';
} else {
	// log image creation
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

	$imgtype = substr($transdata[0]['images'][0], -3);
	$avgtype = substr($transdata[0]['img'], -3);

	$query = sprintf("INSERT INTO avg_log (user_id, n, imgtype, avgtype, width, height, norm, texture, contours, memory, load_time, make_time, dt) VALUES (%d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, NOW())",
		$_SESSION['user_id'],
		count($transdata[0]['images']),
		intval($format[$imgtype]),
		intval($format[$avgtype]),
		intval($transdata[0]['width']),
		intval($transdata[0]['height']),
		intval($norm[$transdata[0]['norm']]),
		$transdata[0]['texture']=='true' ? 1 : 0,
		$transdata[0]['contours']=='true' ? 1 : 0,
		intval($transdata[0]['memory']),
		intval($transdata[0]['load-image-time']),
		intval($transdata[0]['average-time'])
	);
	$return['query'] = $query;
	$q = new myQuery($query);
	
	// save image and associated tem
	$return['data'] = $transdata[0];
	
	include_once DOC_ROOT . '/include/classes/psychomorph.class.php';
	$transimg = $theData['subfolder'] . '/.tmp/' . $transdata[0]['img'];
	$transtem = $theData['subfolder'] . '/.tmp/' . $transdata[0]['tem'];
	
	$img = new PsychoMorph_ImageTem($transimg, $transtem);
	
	$img->setDescription(array(
		'images' => $theData['images0'],
		'texture' => $theData['texture0'],
		'norm' => $theData['norm0'],
		'normpoints' => $theData['normPoint0_0'] . ',' . $theData['normPoint1_0'],
	));
	
	$newfilename = $theData['subfolder'] . $_POST['outname'];
	
	if ($img->save($newfilename)) {
		$return['error'] = false;
		$return['newfilename'] = $img->getImg()->getURL();
	} else {
		$return['errorText'] .= 'The image was not saved. ';
		$return['newfilename'] = $newfilename;
	}
}

scriptReturn($return);

exit;

?>