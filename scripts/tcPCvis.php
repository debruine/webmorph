<?php

// transform selected images and tems and save the output
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

ini_set('max_execution_time', 6000);

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

if (!perm('pca')) {
    $return['errorText'] .= 'You do not have permission to create a PC visualisation';
} else {
    $url = 'https://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/pcvis?';
    
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
    $data = curl_exec($ch);
    curl_close($ch);
    
    $visdata = json_decode($data, true);
    if (!array_key_exists('img', $visdata)) {
        $return['errorText'] .= 'The transform was not created';
    } else {
        // save image and associated tem
        $return['data'] = $visdata;
        
        include_once DOC_ROOT . '/include/classes/psychomorph.class.php';
        $transimg = $visdata['savefolder'] . $visdata['img'];
        $transtem = $visdata['savefolder'] . $visdata['tem'];
        $img = new PsychoMorph_ImageTem($transimg, $transtem);
        
        // put the weights back into proportion notation
        foreach($visdata['weights'] as $w) {
            $weights[] = $w/100; 
        }
        
        $img->addHistory(array(
            'PCA model' => $visdata['pcafile'],
            'avg' => $visdata['avgfile'],
            'PC weights' => "[" . implode(",", $weights) . "]"
        ));
        
        $newFileName = $_POST['outname'];
        
        if ($img->save($newFileName)) {
            $return['error'] = false;
            $return['newFileName'] = $img->getURL();
        } else {
            $return['errorText'] .= 'The image was not saved. ';
        }
    }
}

scriptReturn($return);

exit;

?>