<?php

// performs matrix functions to fit a template to a 3-point delineation

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

//include_once "Math/Matrix.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/include/classes/Math/Matrix.php";

$temPoints = $_POST['temPoints'];
$eyeclicks = $_POST['eyeclicks'];


$original = array(
    array($temPoints[0]['x'], $temPoints[0]['y'], 1.0),
    array($temPoints[1]['x'], $temPoints[1]['y'], 1.0),
    array($temPoints[2]['x'], $temPoints[2]['y'], 1.0,),
);
$m = new Math_Matrix($original);

$xnew = array($eyeclicks[0]['x'], $eyeclicks[1]['x'], $eyeclicks[2]['x']);
$ynew = array($eyeclicks[0]['y'], $eyeclicks[1]['y'], $eyeclicks[2]['y']);

$xvector = new Math_Vector($xnew);
$yvector = new Math_Vector($ynew);

$m1 = $m->cloneMatrix();
$x = Math_Matrix::solve($m1, $xvector);
$a = round($x->get(0), 3);
$b = round($x->get(1), 3);
$c = round($x->get(2), 3);

$m2 = $m->cloneMatrix();
$y = Math_Matrix::solve($m2, $yvector);
$d = round($y->get(0), 3);
$e = round($y->get(1), 3);
$f = round($y->get(2), 3);

$variables = array(
    'a' => $a,
    'b' => $b,
    'c' => $c,
    'd' => $d,
    'e' => $e,
    'f' => $f,
    'fitPoints' => $_POST['fitPoints']
);

echo json_encode($variables);

exit;

?>