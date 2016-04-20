<?php

// calculate facialmetrics from selected tems

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
    'error' => false,
    'errorText' => '',
    'eq' => array()
);

$tems = $_POST['tems'];

// clean the equation

// Remove whitespaces
$eq = preg_replace('/\s+/', '', $_POST['eq']);
$eq = str_replace(
    array('x[', 'y['),
    array('$x[', '$y['),
    $eq
);

$blank_eq = str_replace( 
    array(    'abs(', 'min(', 'max(', 'atan(', 'asin(', 'acos(', 'tan(', 'sin(', 'cos(', 'sqrt(', 'pow(', 'rad2deg(',
            '$x[', '$y[', ']', '(', ')', '.', ',',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 
            '+', '-', '*', '/'
    ), 
    '', 
    $eq
);

if ($eq == 'allXY') {
    foreach ($tems as $j => $temfile) {
        $filename = IMAGEBASEDIR . $temfile;
        if (!file_exists($filename)) {
            $return['errorText'] .= "The file <code>$temfile</code> does not exist.";
        } else {
            $mytem = file($filename);
            $tempoints = trim($mytem[0]);
            for ($i = 0; $i < $tempoints; $i++) {
                list($x, $y) = preg_split('/\s+/', trim($mytem[$i+1]));
                $return['xy'][$i][$j]['x'] = $x;
                $return['xy'][$i][$j]['y'] = $y;
            }
        }
    }
} else if (!empty($blank_eq)) { 
    $return['error'] = true;
    $return['errorText'] .= 'The equation was not valid. These characters need to be removed: ' . $blank_eq;
} else {
    foreach ($tems as $j => $temfile) {
        $filename = IMAGEBASEDIR . $temfile;
        if (!file_exists($filename)) {
            $return['eq'][$j] = null;
            $return['errorText'] .= "The file <code>$temfile</code> does not exist.";
        } else {
            $mytem = file($filename);
            $tempoints = trim($mytem[0]);
            $x = array();
            $y = array();
            for ($i = 0; $i < $tempoints; $i++) {
                list($x[$i], $y[$i]) = preg_split('/\s+/', trim($mytem[$i+1]));
            }
            
            eval('$result = '.$eq.';');
            if (abs($result) >= 100) {
                $rounded = round($result, 1);
            } else {
                $rounded = round($result, ceil(0 - log10($result)) + 2);
            }
            $return['eq'][$j] = $rounded;
        }
    }
}

scriptReturn($return);

exit;

?>

