<?php

// read the exif information in a jpeg

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();

$return = array();

include_once DOC_ROOT . '/include/classes/png_reader.class.php';

function human_filesize($bytes) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    $hrsize = $bytes / pow(1024, $factor);
    $decimals = 1;
    return sprintf("%.{$decimals}f", $hrsize) . @$sz[$factor];
}

$filename = IMAGEBASEDIR . safeFileName($_GET['img']);

$return['Name'] = pathinfo($filename, PATHINFO_BASENAME);

if (file_exists($filename)) {
    $size = getimagesize($filename);
    
    $return['Kind'] = $size['mime'];
    $return['Size'] = human_filesize(filesize($filename));
    $return['Created'] = date('Y-m-d H:i:s', filemtime($filename));
    $return['Dimensions'] = $size[0] . ' x ' . $size[1];
    //$return['Owner'] = $_SESSION['email'];
    

    if (exif_imagetype($filename) == IMAGETYPE_JPEG) {
    
        $exif = exif_read_data($filename);

        preg_match('/IMG_ID: (?P<id>\d+)/', $exif['Copyright'], $img);
        $copyright = explode(":", $exif['Copyright']);
        $return['Owner'] = $copyright[1] . '&nbsp;';
        $return['Image ID'] = $copyright[3] . '&nbsp;';
        
        $origdesc = $exif['ImageDescription'];
        
        $desc = explode(';', trim($origdesc));
        if (is_array(json_decode($origdesc, true))) {
            // parse as JSON
            $json = json_decode($origdesc, true);
            $return['Description'] = $json;
        } else if (count($desc) > 1) {
            $return['Description'] = array();
            foreach ($desc as $d) {
                $d = trim($d);
                $vars = explode(':', $d);
                if (count($vars) == 2) {
                    if (trim($vars[0]) == 'averaged') {
                        $return['Description']['type'] = 'average';
                        $return['Description'] = array_merge($return['Description'], explode(' ', trim($vars[1])));
                    } else {
                        $return['Description'][trim($vars[0])] = trim($vars[1]);
                    }
                } else {
                    $return['Description'][] = $d;
                }
            }
        } else {
            $return['Description'] = str_replace("\n", "<br>", trim($origdesc));
        }
        
        if (is_numeric($img['id'])) {
            $q = new myQuery("SELECT GROUP_CONCAT(tag SEPARATOR '; ') as tags FROM tag WHERE id={$img['id']} GROUP BY id");
            if ($q->get_num_rows() > 0) {
                $return = array_merge(array('Tags'=> $q->get_one()), $return);
            }
        }
        
        // add embedded tem info
        $tem = str_replace('ASCII', '', $exif['UserComment']);
        if (!empty($tem)) {
            $return['Embedded Tem'] = "<pre>" . $tem . "</pre>";
        }
    } elseif (exif_imagetype($filename) == IMAGETYPE_PNG) {
        
        $png = new PNG_Reader($filename);

        $rawTextData = $png->get_chunks('tEXt');
        
        $metadata = array();
        
        foreach($rawTextData as $data) {
           $sections = explode("\0", $data);
        
           if ($sections > 1) {
               $key = array_shift($sections);
               $metadata[$key] = implode("\0", $sections);
           } else {
               $metadata[] = $data;
           }
        }
        $return['Description'] = "PNG XMP=" . count($metadata);
    }
    
}

echo htmlArray($return);
//echo json_encode($exif);

?>