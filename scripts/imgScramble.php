<?php

/****************************************************
*
* Scrambles images sensu Conway et al. (2008, JoV) 
* 
*
* TODO: create symmetric scrambles
*
****************************************************/

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$img_name = $_SERVER['DOCUMENT_ROOT'] . $_POST['img'];

if (substr($img_name, -4) == ".jpg") {
	$original_image = imagecreatefromjpeg($img_name);
} else if (substr($img_name, -4) == ".gif") {
	$original_image = imagecreatefromgif($img_name);
}

$gridsize = 25; // number of pixels between each grid
$x_offset = 0;  // x-coordiate at which to start the first vertical gridline
$y_offset = 0;  // y-coordiate at which to start the first horizontal gridline

if (is_numeric($_POST['grid']) && $_POST['grid']>1) $gridsize = $_POST['grid'];
if (is_numeric($_POST['x'])) $x_offset = $_POST['x'];
if (is_numeric($_POST['y'])) $y_offset = $_POST['y'];

$imgwidth = imagesx($original_image);
$imgheight = imagesy($original_image);

// copy over the whole original image
$new_image = imagecreatetruecolor($imgwidth, $imgheight);
imagecopy($new_image, $original_image, 0, 0, 0, 0, $imgwidth, $imgheight);

//set up grid structure
if ($_POST['chosen'] == 'all') {
	// scramble all 
	
	// get number of rows and columns
	$rows = floor(($imgwidth-$x_offset)/$gridsize);
	$cols = floor(($imgheight-$y_offset)/$gridsize);
	
	for ($r = 0; $r < $rows; $r++) {
		for ($c = 0; $c < $cols; $c++) {
			$grids[] = array($r, $c);
		}
	}
} else {
	// scramble only chosen squares

	$grids = array();
	
	$chosen = explode("-", $_POST['chosen']);
	
	foreach($chosen as $xy) {
		$v = explode(".", $xy);
		$grids[] = array($v[0], $v[1]);
	}
}

// randomise grids
if ($_POST['sym'] == 'true') {

	
} else {
	$random_grids = $grids;
	shuffle($random_grids);
}

foreach($random_grids as $k => $grid) {
	$dest_x = $grid[0] * $gridsize + $x_offset;
	$dest_y = $grid[1] * $gridsize + $y_offset;
	$source_x = $grids[$k][0] * $gridsize + $x_offset;
	$source_y = $grids[$k][1] * $gridsize + $y_offset;
	imagecopy($new_image, $original_image, $dest_x, $dest_y, $source_x, $source_y, $gridsize, $gridsize);
}

// superimpose grid on image
if ($_POST['lines'] != "off") {
	$linecolor = imagecolorallocate($new_image, 0, 0, 0);
	for ($x = $x_offset ; $x <= $imgwidth; $x += $gridsize) {
		imageline($new_image, $x, 0, $x, $imgheight, $linecolor);
	}

	for ($y = $y_offset; $y <= $imgheight; $y += $gridsize) {
		imageline($new_image, 0, $y, $imgwidth, $y, $linecolor);
	}
}

ob_start();
header('Content-type: image/gif');
imagegif($new_image);
$outputBuffer = ob_get_clean();

$base64 = base64_encode($outputBuffer);
echo '<img src="data:image/gif;base64,'.$base64.'" />';
imagedestroy($new_image);

?>