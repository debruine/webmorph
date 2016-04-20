<?php

// transform selected images and tems and save the output

/***********************************************************
Requires POST data:
    pca:            Boolean    do any shape PCA functions
    usepca:         Boolean    load PCA model from pcafile
    pcafile:         String    name of PCA model to load or save
    analysepca:     Boolean    analyse shape of images
    sanalysisfile:     String    name of shape analysis file
    usepci:         Boolean load PCI model from pcifile
    pci:            Boolean    do any color PCI functions
    pcifile:         String    name of PCI model to load or save
    analysepci:     Boolean    analyse colour of images
    canalysisfile:     String    name of colour analysis file
    images:         String[] list of image paths
    texture:         Boolean    use texture to create average
    mask:            String    name of mask to use for PCI
***********************************************************/

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

ini_set('max_execution_time', 6000);

$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

if (!perm('pca')) {
    $return['errorText'] .= 'You do not have permission to create a PCA';
} else {
    $url = 'http://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/pca?';
    
    // set up data
    $theData = $_POST['theData'];
    $theData['subfolder'] = intval($_SESSION['user_id']);
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
    
    $pcadata = json_decode($data, true);
    if (!is_array($pcadata) || !array_key_exists('error', $pcadata) || $pcadata['error'] == 'true') {
        $return['errorText'] = $data;
    } else {
        $return['data'] = $pcadata;
        
        if (!empty($pcadata['img'])) {
            // save image and associated tem
            include_once DOC_ROOT . '/include/classes/psychomorph.class.php';
            $avgimg = "/.tmp/" . $pcadata['img'];
            $avgtem = "/.tmp/" . $pcadata['tem'];
            
            $img = new PsychoMorph_ImageTem($avgimg, $avgtem);
            
            $img->setDescription(array(
                'Desc' => 'Average image for PCA model ' . $theData['pcafile'] . '.pca',
                'images' => $theData['images'],
                'texture' => $theData['texture'],
                'norm' => 'rigid'
            ));
            
            $newFileName = ifEmpty($theData['pcafile'], $theData['pcifile']);
            
            if ($img !== null && $img->save($newFileName)) {
                $return['error'] = false;
                $return['newFileName'] = $img->getImg()->getURL();
            } else {
                $return['errorText'] .= 'The image was not saved. ';
                $return['newFileName'] = $newFileName;
            }
        }
    }

}

scriptReturn($return);

exit;

?>