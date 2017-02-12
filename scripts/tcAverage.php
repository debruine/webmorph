<?php

// average selected images and tems and save the output

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

$url = 'http://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/avg?';

$theData = $_POST['theData'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, serializeForTomcat($theData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
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
    $q = new myQuery($query);
    
    // save image and associated tem
    include_once DOC_ROOT . '/include/classes/psychomorph.class.php';
    $transimg = $theData['subfolder'] . '/.tmp/' . $transdata[0]['img'];
    $transtem = $theData['subfolder'] . '/.tmp/' . $transdata[0]['tem'];
    
    $img = new PsychoMorph_ImageTem($transimg, $transtem);

    $desc = array(
        'label' => 'average',
        'images' => $theData['images0'],
        'texture' => $theData['texture0'],
        'norm' => $theData['norm0']
    );
    if ($theData['norm0'] != "none") {
        $desc['normpoints'] = $theData['normPoint0_0'] . ',' . $theData['normPoint1_0'];
    }
    $img->addHistory($desc);
    
    if (empty($_POST['outname'])) {
        $newFileName = null;
    } else {
        $newFileName = $theData['subfolder'] . $_POST['outname'];
    }
    
    if ($img->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $img->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
        $return['newFileName'] = $newFileName;
    }
}

scriptReturn($return);

exit;

?>