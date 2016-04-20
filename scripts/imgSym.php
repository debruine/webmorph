<?php

// symmetrise selected images and tems

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

$return = array(
    'error' => true,
    'errorText' => '',
    'post' => $_POST
);

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

preg_match("/^\d{1,11}\//", $_POST['img'], $project);
$project_id = str_replace('/', '', $project[0]);

$img = new PsychoMorph_ImageTem($_POST['img']);
$tem_id = intval($_POST['tem_id']) > 1 ? intval($_POST['tem_id']) : 1;
$img->mirror($tem_id);
$tmpfilename = IMAGEBASEDIR . $project_id . '/.tmp/mirror_' . time() . '.' . $_POST['ext'];

if ($img->save($tmpfilename)) {
    $mirrored_image = str_replace(IMAGEBASEDIR . $project_id, '', $tmpfilename);
    $return['mirror'] = $mirrored_image;
} else {
    $return['errorText'] .= 'The mirrored image was not saved. ';
}

/*
$sympoints = empty($_POST['sym']) ? "1 0 10 17 16 15 14 13 12 11 2 9 8 7 6 5 4 3 27 26 25 24 23 22 21 20 19 18 33 32 31 30 29 28 43 42 41 40 39 38 37 36 35 34 49 48 47 46 45 44 56 57 58 59 60 55 50 51 52 53 54 66 67 68 69 70 61 62 63 64 65 82 81 80 79 78 77 76 75 74 73 72 71 86 85 84 83 93 92 91 90 89 88 87 98 97 96 95 94 103 102 101 100 99 108 107 106 105 104 112 113 114 109 110 111 120 121 122 123 124 115 116 117 118 119 133 132 131 130 129 128 127 126 125 144 143 142 141 140 139 138 137 136 135 134 157 156 155 154 153 152 151 150 149 148 147 146 145 161 162 163 158 159 160 167 168 169 164 165 166 172 173 170 171 174 175 178 177 176 182 181 180 179 184 183 187 188 185 186" : $_POST['sym'];
$symfile = preg_split('/\s+/', trim($sympoints));
*/

$url = 'http://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/trans?';
// get this user's default prefs from database
$q = new myQuery("SELECT pref, prefval FROM pref WHERE user_id='{$_SESSION['user_id']}'");
$myprefs = $q->get_assoc(false, 'pref', 'prefval');
$shape = ($_POST['shape'] == 'false') ? 0 : 0.5;
$colortex = ($_POST['color'] == 'false') ? 0 : 0.5;
$ext = in_array($_POST['ext'], array('jpg', 'png', 'gif')) ? $_POST['ext'] : $myprefs['default_imageformat'];

$theData = array(
    'subfolder' => $project_id,
    'savefolder' =>  '/.tmp/',
    'count' => 1,
    'shape0' => $shape,
    'color0' => $colortex,
    'texture0' => $colortex,
    'sampleContours0' => $myprefs['sample_contours'],
    'transimage0' => preg_replace("/^(\d{1,11}\/)/", "/", $_POST['img']),
    'fromimage0' => preg_replace("/^(\d{1,11}\/)/", "/", $_POST['img']),
    'toimage0' => preg_replace("/^(\d{1,11}\/)/", "/", $mirrored_image),
    'norm0' => 'none', // $myprefs['normalisation'], // other norms can produce tilted images
    'warp0' => $myprefs['warp'],
    'normPoint0_0' => $myprefs['align_pt1'], // doesn't matter
    'normPoint1_0' => $myprefs['align_pt2'], // doesn't matter
    'format' => $ext,
);

$paramsJoined = array();
foreach($theData as $param => $value) {
   $paramsJoined[] = "$param=$value";
}
$query = implode('&', $paramsJoined);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
$data = curl_exec($ch);
curl_close($ch);

$transdata = json_decode($data, true);

// save image and associated tem

$symtype = ($shape == 0) ? 
    (($colortex == 0) ? 'Un-' : 'Color-only ') : 
    (($colortex == 0) ? 'Shape-only ' : 'Shape & color ');

include_once DOC_ROOT . '/include/classes/psychomorph.class.php';
$symimg = $project_id . '/.tmp/' . $transdata[0]['img'];
$symtem = $project_id . '/.tmp/' . $transdata[0]['tem'];

$return['transdata'] = $transdata[0];

$img = new PsychoMorph_ImageTem($symimg, $symtem);
$img->setDescription($symtype . 'symmetrised version of ' . $_POST['img']);

$newFileName = array(
    'subfolder' => $_POST['subfolder'],
    'prefix' => $_POST['prefix'],
    'suffix' => $_POST['suffix'],
    'name' => pathinfo($_POST['img'], PATHINFO_FILENAME),
    'ext' => $_POST['ext']
);

if ($img->save($newFileName)) {
    $return['error'] = false;
    $return['newFileName'] = $img->getImg()->getURL();
} else {
    $return['errorText'] .= 'The image was not saved. ';
    $return['newFileName'] = '';
}

// delete the tmp files
unlink($tmpfilename);
unlink(preg_replace('@\.(jpg|png|gif)$@', '.tem', $tmpfilename));

scriptReturn($return);

exit;

?>