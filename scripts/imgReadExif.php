<?php

// read the exif information in a jpeg

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
session_write_close();
auth();

$return = array(
    'error' => false
);

if ($_GET['img']) {
    include_once DOC_ROOT . '/include/classes/psychomorph.class.php';
    
    if ($img = new PsychoMorph_Image($_GET['img'])) {
        $desc = $img->getDescription('array');
        
        $info['Name'] = $img->getURL(false);
        //$info['Linked Tem'] = $desc['tem'];
        $info['Kind'] = $img->getFileType();
        $info['Size'] = $img->getFileSize();
        $info['Dimensions'] = $img->getWidth() . ' x ' . $img->getHeight();
        $info['Created'] = $img->getCreateDate();
        //$info['Last Saved'] = $desc['last_saved'];

        $info['Tem Embed'] = (empty($desc['tem'])) ? "FALSE" : "TRUE"; 
        
        $return['info'] = htmlArray($info);
        $return['history'] = preg_replace('@(<tr>)(<td>\d{4}-\d{1,2}-\d{1,2})( )(\d{2}:\d{2}:\d{2}</td>)@m', '<tr class="date">$2<br>$4', htmlArray($desc['history']));
    } else {
        $return['error'] = true;
    } 
}

scriptReturn($return);

exit;




/*
include_once DOC_ROOT . '/include/classes/png_reader.class.php';

$filename = IMAGEBASEDIR . safeFileName($_GET['img']);

$return['Name'] = pathinfo($filename, PATHINFO_BASENAME);





if (!file_exists($filename)) {
    $return['error'] = true;
    $return['errorText'] = "File does not exist";
} else {
    $size = getimagesize($filename);
    
    $return['Kind'] = $size['mime'];
    $return['Size'] = formatBytes(filesize($filename));
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
        $json = json_decode($origdesc, true);
        if (!is_null($json) && is_array($json)) {
            //$return['Description'] = $json;
        } else if (count($desc) > 1) {
            $json = array();
            foreach ($desc as $d) {
                $d = trim($d);
                $vars = explode(':', $d);
                if (count($vars) == 2) {
                    if (trim($vars[0]) == 'averaged') {
                        $json['type'] = 'average';
                        $json = array_merge($json, explode(' ', trim($vars[1])));
                    } else {
                        $json[trim($vars[0])] = trim($vars[1]);
                    }
                } else {
                    $json[] = $d;
                }
            }
        } else {
            $json = str_replace("\n", "<br>", trim($origdesc));
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
            //$return['Embedded Tem'] = "<pre>" . $tem . "</pre>";
            $return['Embedded Tem'] = "TRUE";
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

// break dates into two lines for better display
$html = preg_replace('@^(<dt>\d{4}-\d{1,2}-\d{1,2})( )(\d{2}:\d{2}:\d{2}</dt>)@m', '$1<br>$3', htmlArray($return));
$html .= preg_replace('@^(<dt>\d{4}-\d{1,2}-\d{1,2})( )(\d{2}:\d{2}:\d{2}</dt>)@m', '$1<br>$3', htmlArray($json));

scriptReturn(array('desc' => $html));

exit;
*/

?>