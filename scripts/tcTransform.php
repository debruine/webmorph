<?php

// transform selected images and tems and save the output

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

$url = 'https://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/trans?';

$theData = $_POST['theData'];

$ch = curl_init();
if ($_SERVER['SERVER_NAME'] == 'webmorph.test') {
    // workaround for local server problem with self-signed certificates
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
}
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, serializeForTomcat($theData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 600);
if ($data = curl_exec($ch)) {
    $transdata = json_decode($data, true);
    if (count($transdata) == 0) {
        $return['errorText'] .= $data;
    } else {
        $return['data'] = $transdata[0]; 
    } 
} else {
    $return['errorText'] .= curl_error($ch);  
}
curl_close($ch);

if ($return['data']) {
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
    
    $desc = array(
        'label' => 'transform',
        'transimg' => $theData['transimage0'],
        'fromimg' => $theData['fromimage0'],
        'toimg' => $theData['toimage0'],
        'shape' => round($theData['shape0']*100,1) . '%',
        'color' => round($theData['color0']*100,1) . '%',
        'texture' => round($theData['texture0']*100,1) . '%',
        'warp' => $theData['warp0'],
        'contour' => $theData['sampleContours0'],
        'norm' => $theData['norm0'],
    );
    if ($theData['norm0'] != "none") {
        $desc['normpoints'] = $theData['normPoint0_0'] . ',' . $theData['normPoint1_0'];
    }
    $img->addHistory($desc);
    
    if (empty($_POST['outname'])) {
        $newFileName = null;
        $img->setOverWrite(true);
    } else {
        $newFileName = $theData['subfolder'] . $_POST['outname'];
    }
    
    if ($img->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $img->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
    }
}

scriptReturn($return);

exit;

?>