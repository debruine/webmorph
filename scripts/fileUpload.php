<?php

// new file save (needs a lot of work)

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => ''
);

if (count($_FILES) > 0) {
    include_once DOC_ROOT . '/include/classes/psychomorph.class.php';
    
    foreach ($_FILES['upload']['tmp_name'] as $i => $tmp_name) {
        $files[$_FILES['upload']['name'][$i]] = $tmp_name;
    }
    
    $mydir = '';
    if (array_key_exists('basedir', $_POST)) { 
        $mydir = str_replace('..', '', $_POST['basedir']);
    }
    
    $imgsaved = array();
    
    for ($i = 0 ; $i < count($_FILES['upload']['type']); $i++) {
        $type = explode('/', $_FILES['upload']['type'][$i]);
        
        
        if ($_FILES['upload']['error'][$i] == 0 && $_FILES['upload']['size'][$i] > 0) {
            // extract file name data
            $ext = pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION);
            $name = pathinfo($_FILES['upload']['name'][$i], PATHINFO_FILENAME);
            $tmp_name = $_FILES['upload']['tmp_name'][$i];
            $return['file'][$tmp_name] = file_exists($tmp_name);
            $newFileName = safeFileName("{$mydir}/{$name}.{$ext}");
        
            if ($type[0] == 'image') {
                $tem_name = $name . '.tem';
                $existingtemfile = safeFileName("{$mydir}/{$name}.tem");
                if (!empty($files[$tem_name])) {
                    // a tem was uploaded with this image
                    $img = new PsychoMorph_ImageTem($tmp_name, $files[$tem_name]);
                } else if (file_exists(IMAGEBASEDIR . $existingtemfile)) {
                    // a tem already exists for this image in the user directory
                    $img = new PsychoMorph_ImageTem($tmp_name, $existingtemfile);
                } else {
                    // there is no tem for this image
                    $img = new PsychoMorph_Image($tmp_name);
                }
                $img->setDescription('Uploaded file');
                //$img->save($newFileName);
            } else if ($ext == 'tem') {
                if (!empty($files["{$name}.jpg"])) {
                    // a tem file is uploaded with the image
                    // already taken care of with the image
                    $newFileName = safeFileName("{$mydir}/{$name}.jpg");
                } else if (!empty($files["{$name}.png"])) {
                    $newFileName = safeFileName("{$mydir}/{$name}.png");
                } else if (!empty($files["{$name}.gif"])) {
                    $newFileName = safeFileName("{$mydir}/{$name}.gif");
                } else {
                    // a tem is being uploaded by itself
                    $name = $_FILES['upload']['name'][$i];
                    $existingimgfile = $newFileName;
        
                    if (file_exists(IMAGEBASEDIR . $existingimgfile)) {
                        // a tem already exists for this image in the user directory
                        $img = new PsychoMorph_ImageTem($existingimgfile, $tmp_name);
                    } else {
                        // there is no image for this tem
                        $img = new PsychoMorph_Tem($tmp_name);
                    }
                    //$img->save($newFileName);
                }
            }
        }
    }
}

$return['newFileName'] = $newFileName;

if (file_exists(IMAGEBASEDIR . $newFileName)) {
    $return['error'] = true;
    $return['errorText'] .= preg_replace("/^(\d{1,11}\/)/", "/", $newFileName) . ' already exists. Delete, rename, or move it first.';
} else if ($img !== null && $img->save($newFileName)) {
    $return['newFileName'] = $img->getImg()->getURL();
    $return['error'] = false;
} else if (in_array($ext, array('txt', 'csv', 'pci', 'pca'))) {
    rename($tmp_name, IMAGEBASEDIR . $newFileName);
    $return['error'] = false;
} else {
    $return['error'] = true;
    $return['errorText'] .= preg_replace("/^(\d{1,11}\/)/", "/", $newFileName) . ' was not saved.';
}

scriptReturn($return);

exit;

?>