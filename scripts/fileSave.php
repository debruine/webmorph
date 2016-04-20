<?php

// old file Save 

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
include_once('PEL/src/PelJpeg.php');

$mydir = IMAGEBASEDIR;

function makeThumb($original_image) {
    // Get new dimensions
    $width = imagesx($original_image);
    $height = imagesy($original_image);
    
    $new_height = 100;
    $new_width = $width * $new_height / $height;

    // Resample
    $thumbnail_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($thumbnail_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    return $thumbnail_image;
}

function nextImg($name) {
    // get next imgname from uploads table
    $img_query = new myQuery(sprintf("INSERT INTO img (user_id, dt, name) 
        VALUES ('%d', NOW(), '%s')
        ON DUPLICATE KEY UPDATE
        user_id='%d', dt=NOW()",
        $_SESSION['user_id'],
        str_replace(array('///', '//'), '/', $name),
        $_SESSION['user_id']
    ));
    $img_id = $img_query->get_insert_id();
    
    return $img_id;
}

function addExif($filename, $exifdata) {
    try {        
        $jpeg = new PelJpeg($filename);
        
        if (!$exif = $jpeg->getExif()) { 
            // Create and add empty Exif data to the image (this throws away any old Exif data in the image).
            $exif = new PelExif();
            $jpeg->setExif($exif);
        }
        
        if (!$tiff = $exif->getTiff()) {
            // Create and add TIFF data to the Exif data (Exif data is actually stored in a TIFF format).
            $tiff = new PelTiff();
            $exif->setTiff($tiff);
        }
        
        if (!$ifd0 = $tiff->getIfd()) {    
            // Create first Image File Directory and associate it with the TIFF data.
            $ifd0 = new PelIfd(PelIfd::IFD0);
            $tiff->setIfd($ifd0);    
        }
        
        if (!$exif_ifd = $ifd0->getSubIfd(PelIfd::EXIF)) {    
            // Create first Image File Directory and associate it with the TIFF data.
            $exif_ifd = new PelIfd(PelIfd::EXIF);
            $ifd0->addSubIfd($exif_ifd);    
        }
        
        if (!$ifd1 = $ifd0->getNextIfd()) {
            // thumbnail does not exist
            $ifd1 = new PelIfd(1);
            $ifd0->setNextIfd($ifd1);
            $original = ImageCreateFromString($jpeg->getBytes()); # create image resource of original
            $thumb = makeThumb($original);
            
            // start writing output to buffer
            ob_start();       
            // outputs thumb resource contents to buffer
            ImageJpeg($thumb);   
            // create PelDataWindow from buffer thumb contents (and end output to buffer)
            $window = new PelDataWindow(ob_get_clean()); 
            
            if ($window) {   
                $ifd1->setThumbnail($window); # set window data as thumbnail in ifd1
            }
            
            imagedestroy($original);
            imagedestroy($thumb);
        }
        
        foreach ($exifdata as $PelTag => $val) {
            if ($PelTag == PelTag::USER_COMMENT) {
                if (!$entry = $exif_ifd->getEntry($PelTag)) {
                    $exif_ifd->addEntry(new PelEntryUserComment($val));
                } else {
                    $entry->setValue($val);
                }
            } else {
                if (!$entry = $ifd0->getEntry($PelTag)) {
                    $ifd0->addEntry(new PelEntryAscii($PelTag, $val));
                } else {
                    $entry->setValue($val);
                }
            }
        }
    
        $jpeg->saveFile($filename);
        
        return true;
    } catch (Exception $e) {
        // Handle exception
        echo $e;
    }
}

if (array_key_exists('imgBase64', $_POST)) {
    // create image from base64
    $img = $_POST['imgBase64'];
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    
    $imgname = safeFileName('/' . $_POST['basedir'] . '/' . $_POST['name']);
    
    // make sure any directories are created
    $imgname_array = explode('/', $imgname);
    $imgbasedir = array_filter( $imgname_array, 'strlen' ); // get rid of empty items
    $imgfilename = array_pop($imgbasedir); 
    $checkbasedir = $mydir . '/' . implode('/', $imgbasedir);
    if (!file_exists($checkbasedir)) {
        mkdir($checkbasedir, DIRPERMS, true);
    }
    
    // save image
    $filename = IMAGEBASEDIR . $imgname . '.jpg';
    $success = file_put_contents($filename, $data);
    
    // get next imgname from uploads table
    $imgid = nextImg('/' . $_SESSION['user_id'] . $imgname);
    
    // add EXIF data
    $exifdata = array(
        PelTag::IMAGE_DESCRIPTION => 'Image uploaded from webcam',
        PelTag::COPYRIGHT => "webmorph.org: " . $_SESSION['email'] . ': IMG_ID: ' . $imgid,
        PelTag::USER_COMMENT => ''
    );
    addExif($filename, $exifdata);
    
    $msg = "Image saved;" . str_replace(IMAGEBASEDIR, '', $filename);
} else if (strpos($_POST['img'], "/tomcat/") !== false) {
    // get next imgname from uploads table
    $imgname = safeFileName('/' . $_POST['name']);

    // make sure any directories are created
    $imgname_array = explode('/', $imgname);
    $imgbasedir = array_filter( $imgname_array, 'strlen' ); // get rid of empty items
    $imgfilename = array_pop($imgbasedir); 
    $checkbasedir = $mydir . '/' . implode('/', $imgbasedir);
    if (!file_exists($checkbasedir)) {
        mkdir($checkbasedir, DIRPERMS, true);
    }
    
    // move tem
    $tem = str_replace(array("http://", "test.psychomorph", "webmorph.org"), "", $_POST['tem']);
    $source = str_replace("/tomcat/", TOMCAT, $tem);
    $destination = "{$mydir}$imgname.tem";
    copy($source, $destination);
    $temcontents = implode("", file($destination));

    // move image
    $img = str_replace(array("http://", "test.psychomorph", "webmorph.org"), "", $_POST['img']);
    $source = str_replace("/tomcat/", TOMCAT, $img);
    $destination = "{$mydir}{$imgname}.jpg";
    copy($source, $destination);
    $filename = $destination;
    
    list($w, $h) = getimagesize($filename);
    $imgid = nextImg('/' . $_SESSION['user_id'] . $imgname);
    
    // add EXIF data
    $exifdata = array(
        PelTag::IMAGE_DESCRIPTION => $_POST['desc'],
        PelTag::COPYRIGHT => "webmorph.org: " . $_SESSION['email'] . ': IMG_ID: ' . $imgid,
        PelTag::USER_COMMENT => $temcontents
    );
    addExif($filename, $exifdata);
    
    //imagedestroy($newimage);
    
    $msg = "Image saved;" . str_replace(IMAGEBASEDIR, '', $filename);
} else if (count($_FILES) > 0) {
    foreach ($_FILES['upload']['tmp_name'] as $i => $tmp_name) {
        $files[$_FILES['upload']['name'][$i]] = $tmp_name;
    }
    
    if (array_key_exists('basedir', $_POST)) { 
        $mydir = IMAGEBASEDIR . str_replace('..', '', $_POST['basedir']);
    }
    
    $imgsaved = array();
    
    for ($i = 0 ; $i < count($_FILES['upload']['type']); $i++) {
        $type = explode('/', $_FILES['upload']['type'][$i]);
        if ($_FILES['upload']['error'][$i] == 0 && $_FILES['upload']['size'][$i] > 0) {
            if ($type[0] == 'image') {
                $tmp_name = $_FILES['upload']['tmp_name'][$i];
                
                // get next imgname from uploads table
                $tags = explode(';', $_POST['tags']);
                $orig_name = explode('.', $_FILES['upload']['name'][$i]);
                $imgname = $orig_name[0];
                
                $filename = safeFileName("{$mydir}/{$imgname}.jpg");
                
                
                if ($type[1] == 'jpeg') {
                    copy($tmp_name, $filename);
                } else if ($type[1] == 'png') {
                    $newimage = imagecreatefrompng($tmp_name);
                    imagejpeg($newimage, $filename);
                    imagedestroy($newimage);
                } else if ($type[1] == 'gif') {
                    $newimage = imagecreatefromgif($tmp_name);
                    imagejpeg($newimage, $filename);
                    imagedestroy($newimage);
                }
                
                list($w, $h) = getimagesize($filename);
                $imgid = nextImg(str_replace(IMAGEBASEDIR, '', $mydir) . '/' . $imgname);
                
                // save tem file if there is one
                $tem_name = preg_replace('@\.(jpg|png|gif|JPG)$@', '.tem', $_FILES['upload']['name'][$i]);
                $temfilename = preg_replace('@\.(jpg|png|gif|JPG)$@', '.tem', $filename);
                if (!empty($files[$tem_name])) {
                    move_uploaded_file($files[$tem_name], $temfilename);
                    $temcontents = implode("", file($temfilename));
                } else if (file_exists($temfilename)) {
                    // get existing tem if it exists
                    $temcontents = implode("", file($temfilename));
                } else {
                    $temcontents = '';
                }
    
                // add EXIF data
                $exifdata = array(
                    PelTag::IMAGE_DESCRIPTION => $_POST['desc'],
                    PelTag::COPYRIGHT => "webmorph.org: " . $_SESSION['email'] . ': IMG_ID: ' . $imgid,
                    PelTag::USER_COMMENT => $temcontents
                );
                addExif($filename, $exifdata);
            } else if (substr($_FILES['upload']['name'][$i], -4) == '.tem' ||
                       substr($_FILES['upload']['name'][$i], -4) == '.txt') {
                $tmp_name = $_FILES['upload']['tmp_name'][$i];
                $name = $_FILES['upload']['name'][$i];
                $filename = safeFileName("{$mydir}/{$name}");
                move_uploaded_file($tmp_name, $filename);
            }
        }
    }
} else {
    echo "There was an error saving your file";
}

?>