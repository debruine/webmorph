<?php

// transform selected images and tems and save the output

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

$url = 'http://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/trans?';

// set up data
$theData = $_POST['theData'];
$paramsJoined = array();
foreach($theData as $param => $value) {
   $paramsJoined[] = "$param=$value";
}
$query = implode('&', $paramsJoined);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 600);
$data = curl_exec($ch);
curl_close($ch);

$transdata = json_decode($data, true);
if (count($transdata) == 0) {
    $return['errorText'] .= 'The transform was not created';
} else {
    // log image transform
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
    $imgtype = substr($transdata[0]['transimg'], -3);
    $transtype = substr($transdata[0]['img'], -3);
    
    $query = sprintf("INSERT INTO trans_log (user_id, imgtype, transtype, shape, color, texture, width, height, norm, warp, contours, memory, load_time, make_time, dt) VALUES (%d, %d, %d, %f, %f, %f, %d, %d, %d, %d, %d, %d, %d, %d, NOW())",
        $_SESSION['user_id'],
        intval($format[$imgtype]),
        intval($format[$transtype]),
        floatval(str_replace('%', '', $transdata[0]['shape'])),
        floatval(str_replace('%', '', $transdata[0]['color'])),
        floatval(str_replace('%', '', $transdata[0]['texture'])),
        intval($transdata[0]['width']),
        intval($transdata[0]['height']),
        intval($norm[$transdata[0]['norm']]),
        intval($warp[$transdata[0]['warp']]),
        $transdata[0]['contours']=='true' ? 1 : 0,
        intval($transdata[0]['memory']),
        intval($transdata[0]['load-image-time']),
        intval($transdata[0]['transform-time'])
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
        'transimg' => $theData['transimage0'],
        'fromimg' => $theData['fromimage0'],
        'toimg' => $theData['toimage0'],
        'shape' => round($theData['shape0']*100,1) . '%',
        'color' => round($theData['color0']*100,1) . '%',
        'texture' => round($theData['texture0']*100,1) . '%',
        'norm' => $theData['norm0'],
        'normpoints' => $theData['normPoint0_0'] . ',' . $theData['normPoint1_0'],
        'warp' => $theData['warp0'],
        'contour' => $theData['sampleContours0'],
    ));
    
    $newFileName = $theData['subfolder'] . $_POST['outname'];
    
    if ($img->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $img->getImg()->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
    }
}

scriptReturn($return);

exit;

?>