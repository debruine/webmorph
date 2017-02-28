<?php

// new file save (needs a lot of work)

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => false,
    'errorText' => ''
);

if (count($_FILES) == 0) {
    $return['error'] = true;
    $return['errorText'] = "No files were uploaded";
    scriptReturn($return);
    exit;
}


include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

$mydir = '';
if (array_key_exists('basedir', $_POST)) { 
    $mydir = str_replace('..', '', $_POST['basedir']);
    $mydir = safeFileName($mydir);
}
$return['mydir'] = $mydir;

// reformat into sensible structure for iteration
foreach ($_FILES['upload']['tmp_name'] as $i => $tmp_name) {
    $name = safeFileName($_FILES['upload']['name'][$i]);
    $size = $_FILES['upload']['size'][$i];
    $error = $_FILES['upload']['error'][$i];
    
    if ($size > 0 && !$error) {
        $files[$name] = array(
            'origname' => $_FILES['upload']['name'][$i],
            'name' => pathinfo($name, PATHINFO_FILENAME),
            'ext' => pathinfo($name, PATHINFO_EXTENSION),
            'tmp_name' => $tmp_name,
            'type' => explode('/', $_FILES['upload']['type'][$i]),
            'size' => $size
        );
    }
}

$return['files'] = $files;

foreach ($files as $file) {
    $newFileName = "{$mydir}{$file['name']}.{$file['ext']}";
    
    if (!$file['name']) {
        $return['error'] = true;
        $return['errorText'] .= "There was an error with the file name: {$file['origname']}.";
        continue;
    } else if (file_exists(IMAGEBASEDIR . $newFileName)) {
        // file already exists
        $return['error'] = true;
        $return['errorText'] .= "{$file['name']}.{$file['ext']} already exists. Delete, rename, or move it first.";
        continue;
    } else if (in_array($file['ext'], array('txt', 'csv', 'pci', 'pca', 'obj', 'bmp'))) {
        /*if (
            ($file['ext'] == 'txt' && $file['type'][0] == '' && $file['type'][1] == '') ||
            ($file['ext'] == 'csv' && $file['type'][0] == '' && $file['type'][1] == '') ||
            ($file['ext'] == 'pci' && $file['type'][0] == '' && $file['type'][1] == '') ||
            ($file['ext'] == 'pca' && $file['type'][0] == '' && $file['type'][1] == '') ||
            ($file['ext'] == 'obj' && $file['type'][0] == '' && $file['type'][1] == '') ||
            ($file['ext'] == 'bmp' && $file['type'][0] == 'image' && $file['type'][1] == 'x-ms-bmp')
        ) {*/
            rename($file['tmp_name'], IMAGEBASEDIR . $newFileName);
            $return['error'] = $return['error'] || false;
            $return['newFileName'][] = $newFileName;
            continue;
        /*} else {
            $return['error'] = true;
            $return['errorText'] .= "{$file['name']}.{$file['ext']} 
                is actually a different file type ({$file['type'][0]}/{$file['type'][1]}).";
            continue;
        }*/
    }
    
    $img = null;

    if ($file['type'][0] == 'image') {
        if (!in_array($file['type'][1], array('jpg','jpeg','png','gif'))) {
            // unsupported image type
            $return['error'] = true;
            $return['errorText'] .= "{$file['name']}.{$file['ext']} is an unsupported file type.";
            continue;
        }
        
        
        $tem_name = $name . '.tem';
        $existingtemfile = "{$mydir}/{$name}.tem";
        if (file_exists(IMAGEBASEDIR . $existingtemfile)) {
            // a tem already exists for this image in the user directory
            $img = new PsychoMorph_ImageTem($file['tmp_name'], $existingtemfile);
            $img->addHistory('Uploaded image, pre-existing tem');
            $img->setOverwrite(true);
        } else if (!empty($files[$tem_name])) {
            // a tem was uploaded with this image
            $img = new PsychoMorph_ImageTem($file['tmp_name'], $files[$tem_name]);
            $img->addHistory('Uploaded image and tem');
        } else {
            // there is no tem for this image
            $img = new PsychoMorph_Image($file['tmp_name']);
            $img->addHistory('Uploaded image');
        }
    } else if ($file['ext'] == 'tem') {
        if (!empty($files["{$name}.jpg"]) || 
            !empty($files["{$name}.png"]) || 
            !empty($files["{$name}.gif"])) {
            break;
        } else {
            // a tem is being uploaded by itself
            $jpg = "{$mydir}/{$name}.jpg";
            $png = "{$mydir}/{$name}.png";
            $gif = "{$mydir}/{$name}.gif";

            if (file_exists(IMAGEBASEDIR . $jpg)) {
                // a jpg already exists for this tem in the user directory
                $img = new PsychoMorph_ImageTem($jpg, $file['tmp_name']);
                $img->setOverwrite(true);
            } else if (file_exists(IMAGEBASEDIR . $png)) {
                // a png already exists for this tem in the user directory
                $img = new PsychoMorph_ImageTem($png, $file['tmp_name']);
                $img->setOverwrite(true);
            } else if (file_exists(IMAGEBASEDIR . $gif)) {
                // a gif already exists for this tem in the user directory
                $img = new PsychoMorph_ImageTem($gif, $file['tmp_name']);
                $img->setOverwrite(true);
            } else {
                // there is no image for this tem
                $img = new PsychoMorph_Tem($file['tmp_name']);
                $img->addHistory('Uploaded tem');
            }
        }
    }
    
    // save if an image was made
    if ($img !== null && $img->save(IMAGEBASEDIR . $newFileName)) {
        $return['newFileName'][] = $img->getURL();
        $return['error'] = $return['error'] || false;
    } else {
        $return['error'] = true;
        $return['errorText'] .= "{$file['name']}.{$file['ext']} was not saved.";
    }
}

scriptReturn($return);

exit;

?>