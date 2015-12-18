<?php

// new file save (needs a lot of work)

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
	'error' => false,
	'errorText' => '',
	//'post' => $_POST
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

if (array_key_exists('imgBase64', $_POST)) {
	// create image from base64
	$newfilename = safeFileName($_POST['basedir'] . '/' . $_POST['name']) . '.jpg';
	
	$img = new PsychoMorph_Image($newfilename);
	$b64 = str_replace('data:image/jpeg;base64,', '', $_POST['imgBase64']);
	$b64 = str_replace(' ', '+', $b64);
	$img->setImageBase64($b64);
	$img->setDescription('Webcam upload');
} else if (strpos($_POST['img'], "/.tmp/") !== false) {
	$tempath = IMAGEBASEDIR . str_replace("/scripts/fileAccess?file=/", "", $_POST['tem']);
	$imgpath = IMAGEBASEDIR . str_replace("/scripts/fileAccess?file=/", "", $_POST['img']);
	$newfilename = safeFileName($_POST['name']);

	$img = new PsychoMorph_ImageTem($imgpath, $tempath);
	$img->setDescription(json_decode($_POST['desc']));
} else if (count($_FILES) > 0) {
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
			$newfilename = safeFileName("{$mydir}/{$name}.{$ext}");
		
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
				//$img->save($newfilename);
			} else if ($ext == 'tem') {
				if (!empty($files["{$name}.jpg"])) {
					// a tem file is uploaded with the image
					// already taken care of with the image
					$newfilename = safeFileName("{$mydir}/{$name}.jpg");
				} else if (!empty($files["{$name}.png"])) {
					$newfilename = safeFileName("{$mydir}/{$name}.png");
				} else if (!empty($files["{$name}.gif"])) {
					$newfilename = safeFileName("{$mydir}/{$name}.gif");
				} else {
					// a tem is being uploaded by itself
					$name = $_FILES['upload']['name'][$i];
					$existingimgfile = $newfilename;
		
					if (file_exists(IMAGEBASEDIR . $existingimgfile)) {
						// a tem already exists for this image in the user directory
						$img = new PsychoMorph_ImageTem($existingimgfile, $tmp_name);
					} else {
						// there is no image for this tem
						$img = new PsychoMorph_Tem($tmp_name);
					}
					//$img->save($newfilename);
				}
			}
		}
	}
}

$return['newfilename'] = $newfilename;

if (file_exists(IMAGEBASEDIR . $newfilename)) {
	$return['error'] = true;
	$return['errorText'] .= preg_replace("/^(\d{1,11}\/)/", "/", $newfilename) . ' already exists. Delete, rename, or move it first.';
} else if ($img !== null && $img->save($newfilename)) {
	$return['error'] = false;
} else if (in_array($ext, array('txt', 'csv', 'pci', 'pca'))) {
	rename($tmp_name, IMAGEBASEDIR . $newfilename);
	$return['error'] = false;
} else {
	$return['error'] = true;
	$return['errorText'] .= preg_replace("/^(\d{1,11}\/)/", "/", $newfilename) . ' was not saved.';
}

scriptReturn($return);

exit;

?>