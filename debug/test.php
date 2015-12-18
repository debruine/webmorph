<?php
	
// Calculate the coordinate of the Bezier curve at $t = 0..1
function Bezier_eval($p1,$p2,$p3,$p4,$t) {
    // lines between successive pairs of points (degree 1)
    $q1  = array((1-$t) * $p1[0] + $t * $p2[0],(1-$t) * $p1[1] + $t * $p2[1]);
    $q2  = array((1-$t) * $p2[0] + $t * $p3[0],(1-$t) * $p2[1] + $t * $p3[1]);
    $q3  = array((1-$t) * $p3[0] + $t * $p4[0],(1-$t) * $p3[1] + $t * $p4[1]);
    // curves between successive pairs of lines. (degree 2)
    $r1  = array((1-$t) * $q1[0] + $t * $q2[0],(1-$t) * $q1[1] + $t * $q2[1]);
    $r2  = array((1-$t) * $q2[0] + $t * $q3[0],(1-$t) * $q2[1] + $t * $q3[1]);
    // final curve between the two 2-degree curves. (degree 3)
    return array((1-$t) * $r1[0] + $t * $r2[0],(1-$t) * $r1[1] + $t * $r2[1]);
}

// Calculate the squared distance between two points
function Point_distance2($p1,$p2) {
    $dx = $p2[0] - $p1[0];
    $dy = $p2[1] - $p1[1];
    return $dx * $dx + $dy * $dy;
}

// Convert the curve to a polyline
function Bezier_convert($p1,$p2,$p3,$p4,$tolerance) {
    $t1 = 0.0;
    $prev = $p1;
    $t2 = 0.1;
    $tol2 = $tolerance * $tolerance;
    $result []= $prev[0];
    $result []= $prev[1];
    while ($t1 < 1.0) {
        if ($t2 > 1.0) {
            $t2 = 1.0;
        }
        $next = Bezier_eval($p1,$p2,$p3,$p4,$t2);
        $dist = Point_distance2($prev,$next);
        while ($dist > $tol2) {
            // Halve the distance until small enough
            $t2 = $t1 + ($t2 - $t1) * 0.5;
            $next = Bezier_eval($p1,$p2,$p3,$p4,$t2);
            $dist = Point_distance2($prev,$next);
        }
        // the image*polygon functions expect a flattened array of coordiantes
        $result []= $next[0];
        $result []= $next[1];
        $t1 = $t2;
        $prev = $next;
        $t2 = $t1 + 0.1;
    }
    return $result;
}

function quadBezier($x1, $y1, $x2, $y2, $x3, $y3) {
	$quad_array = array();

    $b = $pre1 = $pre2 = $pre3 = 0;
    $d = sqrt(($x1 - $x2) * ($x1 - $x2) + ($y1 - $y2) * ($y1 - $y2)) +
        sqrt(($x2 - $x3) * ($x2 - $x3) + ($y2 - $y3) * ($y2 - $y3));
    $resolution = (1/$d) * 10;
    for ($a = 1; $a >0; $a-=$resolution) {
        $b=1-$a;
        $pre1=($a*$a);
        $pre2=2*$a*$b;
        $pre3=($b*$b);
        $x = $pre1*$x1 + $pre2*$x2  + $pre3*$x3;
        $y = $pre1*$y1 + $pre2*$y2 + $pre3*$y3;
        array_push($quad_array, $x, $y);
    }
    array_push($quad_array, $x3, $y3);
    return $quad_array;
}

// Draw a Bezier curve on an image
function Bezier_drawfilled($image,$p1,$p2,$p3,$p4,$color) {
    $polygon = Bezier_convert($p1,$p2,$p3,$p4,0.3);
    imagefilledpolygon($image,$polygon,count($polygon)/2,$color);
}


function getControlPoints($x0,$y0,$x1,$y1,$x2,$y2,$t) {
    $d01=sqrt(pow($x1-$x0,2)+pow($y1-$y0,2));
    $d12=sqrt(pow($x2-$x1,2)+pow($y2-$y1,2));
    $fa=$t*$d01/($d01+$d12);   // scaling factor for triangle Ta
    $fb=$t*$d12/($d01+$d12);   // ditto for Tb, simplifies to fb=t-fa
    $p1x=$x1-$fa*($x2-$x0);    // x2-x0 is the width of triangle T
    $p1y=$y1-$fa*($y2-$y0);    // y2-y0 is the height of T
    $p2x=$x1+$fb*($x2-$x0);
    $p2y=$y1+$fb*($y2-$y0);  
    return array($p1x,$p1y,$p2x,$p2y);
}

	
function drawSpline($pts, $t = 0.3){
	// $t = 0.3 same as Bezier curve t on delineator
    $cp=array();   // array of control points, as x0,y0,x1,y1,...
    $n=count($pts);
    $polygon = array();

    if ($pts[0] == $pts[$n-2] && $pts[1] == $pts[$n-1]) { 
    	// same start and end point, so closed curve
    	unset($pts[$n-1]);
    	unset($pts[$n-2]);
    	$n=count($pts);
    
        //   Append and prepend knots and control points to close the curve
        array_push($pts, $pts[0],$pts[1],$pts[2],$pts[3]);
        array_unshift($pts, $pts[$n-1]);
        array_unshift($pts, $pts[$n-1]);
        
        for($i=0;$i<$n;$i+=2){
        	$newcp = getControlPoints($pts[$i],$pts[$i+1],$pts[$i+2],$pts[$i+3],$pts[$i+4],$pts[$i+5],$t);
            $cp = array_merge($cp, $newcp);
        }
        array_push($cp, $cp[0], $cp[1]); 
          
        for($i=2;$i<$n+2;$i+=2){      
            
            //ctxx.moveTo(pts[i],pts[i+1]);
            //ctxx.bezierCurveTo(cp[2*i-2],cp[2*i-1],cp[2*i],cp[2*i+1],pts[i+2],pts[i+3]);
            
            $p1 = array($pts[$i],    $pts[$i+1]);
            $p2 = array($cp[2*$i-2], $cp[2*$i-1]);
            $p3 = array($cp[2*$i],   $cp[2*$i+1]);
            $p4 = array($pts[$i+2],  $pts[$i+3]);
            
            $bez = Bezier_convert($p1,$p2,$p3,$p4,$t);
            $polygon = array_merge($polygon, $bez);
        }
    } else {  
        // Draw an open curve, not connected at the ends	        
        for($i=0;$i<$n-4;$i+=2){
        	$newcp = getControlPoints($pts[$i],$pts[$i+1],$pts[$i+2],$pts[$i+3],$pts[$i+4],$pts[$i+5],$t);
            $cp = array_merge($cp, $newcp);
        }
          
        //ctxx.moveTo(pts[0],pts[1]);
        //ctxx.quadraticCurveTo(cp[0],cp[1],pts[2],pts[3]);  // first arc
        $quad = quadBezier($pts[0], $pts[1], $cp[0], $cp[1], $pts[2], $pts[3]);
        $polygon = array_merge($polygon, $quad);

        for($i=2;$i<$n-5;$i+=2){ 
        	//ctxx.bezierCurveTo(cp[2*i-2],cp[2*i-1],cp[2*i],cp[2*i+1],pts[i+2],pts[i+3]);
        	
        	$p1 = array($pts[$i],    $pts[$i+1]);
            $p2 = array($cp[2*$i-2], $cp[2*$i-1]);
            $p3 = array($cp[2*$i],   $cp[2*$i+1]);
            $p4 = array($pts[$i+2],  $pts[$i+3]);
            
            $bez = Bezier_convert($p1,$p2,$p3,$p4,$t);
            $polygon = array_merge($polygon, $bez);
        }
        
        //ctxx.quadraticCurveTo(cp[2*n-10],cp[2*n-9],pts[n-2],pts[n-1]); // last arc
        $quad = quadBezier($polygon[count($polygon)-2], $polygon[count($polygon)-1], $cp[2*$n-10],$cp[2*$n-9],$pts[$n-2],$pts[$n-1]);
        $polygon = array_merge($polygon, $quad);
    }
    
    return $polygon;
}

$p = drawSpline(array(50,250,550,250,550,750));
//print_r($p); exit;

$img = imagecreatetruecolor(80,80);
$img2 = imagecreatetruecolor(800,800);
$bg = ImageColorAllocate($img2, 255, 255, 255);
$fg = ImageColorAllocate($img2, 0, 0, 0);
imagefill($img2, 0, 0, $bg);
imageantialias($img2, true);
ImageSetThickness($img2, 5);
ImagePolygon($img2,$p,count($p)/2,$fg);
imagearc($img2, 400, 400, 200, 200, 0, 350, $fg);
for ($i=0;$i<0;$i++) imagefilter($img2,IMG_FILTER_GAUSSIAN_BLUR);
ImagePng($img2);


?>