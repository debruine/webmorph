<?php

// saves a tem file 
// needs to be replaced by the class

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
//include_once('PEL/src/PelJpeg.php');

$return = array(
	'error' => true,
	'errorText' => '',
);

/*
	function addExif($filename, $usercomment) {
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
		
		if (!$entry = $ifd0->getEntry($PelTag)) {
			$exif_ifd->addEntry(new PelEntryUserComment($usercomment));
		} else {
			$entry->setValue($usercomment);
		}
	
		$jpeg->saveFile($filename);
		
		return true;
	} catch (Exception $e) {
	    return false;
	}
}
*/

if (array_key_exists('tem', $_POST)) {
	$tem = $_POST['tem'];
	$temname = preg_replace('@\.(jpg|png|gif)$@', '.tem', $_POST['name']);
	$imgname = IMAGEBASEDIR . preg_replace('@\.tem$@', '.jpg', $temname);
	
	if (!($file = fopen(IMAGEBASEDIR . $temname, 'w'))) {
		$return['errorText'] =  "Could not open the file {$temname}"; 
	} else if (!fwrite($file, $tem)) {
		$return['errorText'] =  "Could not write to the file {$temname}"; 
	} else if (!fclose($file)) {
		$return['errorText'] =  "Could not close the file";
	//} else if (!addExif($imgname, $tem)) {
	//	$return['errorText'] =  "The tem could not be embedded into the image.";
	} else {
		$return['error'] = false;
	}
} else {
	$return['errorText'] = 'The tem points were not found.';
}

scriptReturn($return);

exit;

?>