<?php

/**************************************************************************
 * PsychoMorph Classes
 *
 * PHP version 5
 *
 * @author     Lisa DeBruine <debruine@gmail.com>
 * @copyright  2013 Face Research Lab
 *************************************************************************/

require_once 'psychomorph.file.class.php';

/**************************************************************************
 * Template functions
 *************************************************************************/ 
 
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


function getControlPoints($x0,$y0,$x1,$y1,$x2,$y2,$t = 0.3) {
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
    
        // Append and prepend knots and control points to close the curve
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


function svgMoveTo($x, $y) {
    $x = round($x, 2);
    $y = round($y, 2);
    return "M {$x} {$y}";
}

function svgLineTo($x, $y) {
    $x = round($x, 2);
    $y = round($y, 2);
    return "             L {$x} {$y}";
}

function svgQuadraticCurveTo($x1, $y1, $x, $y) {
    $x1 = round($x1, 2);
    $y1 = round($y1, 2);
    $x = round($x, 2);
    $y = round($y, 2);
    return "             Q {$x1} {$y1}, {$x} {$y}";
}

function svgBezierCurveTo($x1, $y1, $x2, $y2, $x, $y) {
    $x1 = round($x1, 2);
    $y1 = round($y1, 2);
    $x2 = round($x2, 2);
    $y2 = round($y2, 2);
    $x = round($x, 2);
    $y = round($y, 2);
    return "             C {$x1} {$y1}, {$x2} {$y2}, {$x} {$y}";
}

function svgBezier($v) {
    $cp = array(); // control points
    $path = array();
    $vlen = count($v);
    
    if ($vlen == 2) {
        // connect with straight line
        $path[] = svgMoveTo($v[0][0], $v[0][1]);
        $path[] = svgLineTo($v[1][0], $v[1][1]);
        return $path;
    }

    
    // connect with bezier curve
    $pts = array();
    foreach ($v as $pt) {
        array_push($pts, $pt[0]);
        array_push($pts, $pt[1]);
    }

    $n = count($pts);
    if ($pts[0] == $pts[$n - 2] && $pts[1] == $pts[$n - 1]) {
        // Draw a closed curve, connected at the ends
        // remove duplicate points and adjust n
        $n = $n - 2;
        array_pop($pts);
        array_pop($pts);
        
        // Append and prepend knots and control points to close the curve
        array_push($pts, $pts[0]);
        array_push($pts, $pts[1]);
        array_push($pts, $pts[2]);
        array_push($pts, $pts[3]);
        
        array_unshift($pts, $pts[$n - 1]);
        array_unshift($pts, $pts[$n - 1]);
        for ($j = 0; $j < $n; $j += 2) {
            $cp = array_merge($cp, getControlPoints($pts[$j], $pts[$j + 1], $pts[$j + 2], $pts[$j + 3], $pts[$j + 4], $pts[$j + 5]));
        }
        array_push($cp, $cp[0]);
        array_push($cp, $cp[1]);
        $path[] = svgMoveTo($pts[2], $pts[3]);
        for ($j = 2; $j < $n + 2; $j += 2) {
            $path[] = svgBezierCurveTo($cp[2 * $j - 2], $cp[2 * $j - 1], $cp[2 * $j], $cp[2 * $j + 1], $pts[$j + 2], $pts[$j + 3]);
        }
    } else {
        // Draw an open curve, not connected at the ends
        for ($j = 0; $j < $n - 4; $j += 2) {
            $cp = array_merge($cp, getControlPoints($pts[$j], $pts[$j + 1], $pts[$j + 2], $pts[$j + 3], $pts[$j + 4], $pts[$j + 5]));
        }
        $path[] = svgMoveTo($pts[0], $pts[1]);
        $path[] = svgQuadraticCurveTo($cp[0], $cp[1], $pts[2], $pts[3]); // first arc
        for ($j = 2; $j < $n - 5; $j += 2) {
            $path[] = svgBezierCurveTo($cp[2 * $j - 2], $cp[2 * $j - 1], $cp[2 * $j], $cp[2 * $j + 1], $pts[$j + 2], $pts[$j + 3]);
        }
        $path[] = svgQuadraticCurveTo($cp[2 * $n - 10], $cp[2 * $n - 9], $pts[$n - 2], $pts[$n - 1]); // last arc
    }

    return $path;
}

/**************************************************************************
* PsychoMorph_Tem
*************************************************************************/

class PsychoMorph_Tem extends PsychoMorph_File {
    private $_points = array();
    private $_lines = array();

    public function _loadFile() {
        $this->_points = array();
        $this->_lines = array();
        $this->_description = array();
    
        // read the tem file
        $path = $this->getPath();
        if (file_exists($path) && $rawtem = file($path)) { 
            $this->loadRawTem($rawtem);
        }

        return $this;
    }
    
    public function loadRawTem($rawtem) {
        $tem = array();
        if (!is_array($rawtem)) {
            $rawtem = explode("\n", $rawtem);
        }
        
        // remove any comment lines
        $comment = '';
        foreach ($rawtem as $l) {
            $line = trim($l);
            if (preg_match('/^\s*$/', $line)) {
                // empty, do nothing
            } else if (preg_match('/^[\-0-9\.\s]+$/', $line)) {
                $tem[] = $line;
            } else  {
                $comment .= $line;
            }
        }
        
        // check for json content in comments
        $json = json_decode($comment, true);
        if (is_null($json) && $comment !== '') {
            // not json format, so add to extras
            $this->_description['extras'][] = $comment;
        } else if (is_array($json)) {
            // add json as array to comments
            $this->_description = array_merge($this->_description, $json);
        }
        
        $pointNumber = trim($tem[0]);
        
        $this->_points = array_slice($tem, 1, $pointNumber);
        $this->_points = array_map(function($n) { 
            $pts = preg_split('@\s+@', $n);
            return array($pts[0], $pts[1]);
        }, $this->_points);
        
        for ($i = $pointNumber+4; $i < count($tem); $i += 3) {
            $this->_lines[] = preg_split('@\s+@', trim($tem[$i]));
        }
        
        return $this;
    }
    
    public function setPoints($i, $v) { $this->_points[$i] = $v; }
    public function getPoints($i = null) { 
        if (is_null($i)) {
            return $this->_points; 
        } else if ($i < 0 || $i >= count($this->_points)) {
            return false;
        } else {
            return $this->_points[$i];
        }
    }

    public function setLines($newLines) {
        // changes lines, can handle:
        //   blank entry (deletes all lines)
        //   all text: space-delimited points with return-delimited lines
        //   1D array: array of space-delimited points
        //   2D array: array of points in arrays
    
        $this->_lines = array();
        
        if ($newLines == '') {
            return true;
        } else if (!is_array($newLines)) {
            // split at line breaks
            $newLines = preg_split('@\R@', trim($newLines));
        }
            
        foreach($newLines as $line) {
            if (is_array($line)) {
                $this->_lines[] = $line;
            } else {
                $this->_lines[] = preg_split('@(\s|,)+@', trim($line));
            }
        }
    }
    public function getLines() { return $this->_lines; }
    public function getPointNumber() { return count($this->_points); }
    
    public function deletePoints($pointArray) {
        $old_points = $this->getPointNumber();
        $pointmap = array();
    
        // order point array from high to low
        rsort($pointArray);
        
        foreach($pointArray as $i) {
            array_splice($this->_points, $i, 1);
            $pointmap[$i] = 'removed';
        }
        
        // adjust tem lines
        $newp = 0;
        for ($i = 0; $i < $old_points; $i++) {
            if ($pointmap[$i] !== 'removed') {
                $pointmap[$i] = $newp;
                $newp++;
            }
        }
        
        // remap and mark points to be removed
        foreach($this->_lines as $line => $points) {
            foreach($points as $i => $point) {
                $this->_lines[$line][$i] = $pointmap[$point];
            }
        }
        
        // remove deleted points
        $nlines = count($this->_lines) - 1;
        
        for ($line = $nlines; $line > 0; $line--) {
            $n = count($this->_lines[$line]) - 1;
            $removed = 0;
            foreach ($this->_lines[$line] as $p) {
                if ($p === 'removed') { $removed++; }
            }
            if ($n+1 == $removed) {
                // remove whole line
                array_splice($this->_lines, $line, 1);
            } else {
                // check individual points to remove
                for($i = $n; $i >= 0; $i--) {
                    if ($this->_lines[$line][$i] === 'removed') {
                        array_splice($this->_lines[$line], $i, 1);
                    }
                }
            }
        }
    }
    
    public function temConvert($old, $new) {
    
        /*
        CREATE TABLE tem_convert (
            new_tem INT(11),
            n INT(4),
            old_tem INT(11),
            x VARCHAR(255),
            y VARCHAR(255),
            UNIQUE INDEX (new_tem, old_tem, n)
        );
        */
            
        $oldpoints = $this->_points;
        $newpoints = array();
        $x = array();
        $y = array();
        
        foreach ($oldpoints as $n => $pt) {
            $x[$n] = $pt[0];
            $y[$n] = $pt[1];
        }
        
        $q = new myQuery("SELECT n, x, y FROM tem_convert WHERE new_tem=$new AND old_tem=$old");
        
        if ($q->get_num_rows() == 0) return false;
        
        $map = $q->get_assoc();
        
        foreach ($map as $m) {
            // x-coordinate
            $eq = preg_replace('/\s+/', '', $m['x']); // Remove whitespaces
            $eq = str_replace( array('x[', 'y['), array('$x[', '$y['), $eq );
            eval('$result = '.$eq.';');
            
            if (abs($result) >= 100) {
                $newpoints[$m['n']][0] = round($result, 1);
            } else {
                $newpoints[$m['n']][0] = round($result, ceil(0 - log10($result)) + 2);
            }
            
            // y-coordinate
            $eq = preg_replace('/\s+/', '', $m['y']); // Remove whitespaces
            $eq = str_replace( array('x[', 'y['), array('$x[', '$y['), $eq );
            eval('$result = '.$eq.';');
            
            if (abs($result) >= 100) {
                $newpoints[$m['n']][1] = round($result, 1);
            } else {
                $newpoints[$m['n']][1] = round($result, ceil(0 - log10($result)) + 2);
            }
        }
        
        $this->_points = $newpoints;
        
        // get new lines
        $q = new myQuery("SELECT points from line WHERE tem_id=$new ORDER BY n");
        
        $lines = $q->get_assoc(false, false, "points");
        $newline = array();
        foreach ($lines as $line) {
            $newline[] = explode(",", trim($line));
        }
        $this->_lines = $newline;
        
        return true;
    }
    
    public function resize($xResize, $yResize = null) {
        if ($yResize == null) { $yResize = $xResize; }
        if ($xResize <= 0 || $yResize<=0 || $xResize > 10 || $yResize > 10) {
            // resize is too small or too big
            return false;
        }
        
        // resize template
        foreach ($this->_points as $i => $p) {
            $this->_points[$i][0] = $p[0] * $xResize;
            $this->_points[$i][1] = $p[1] * $yResize;
        }
        
        return $this;
    }
    
    public function rotate($degrees, $origW, $origH, $newW, $newH) {
        $rotate = deg2rad($degrees);
        $n = $this->getPointNumber();
        
        $xm1 = $origW/2;
        $ym1 = $origH/2;
        $xm2 = $newW/2;
        $ym2 = $newH/2;
        
        for ($i = 0; $i < $n; $i++) {
            list($x, $y) = $this->_points[$i];

            // Subtract original midpoints, so that midpoint is translated to origin
            // and add the new midpoints in the end again
            $xr = ($x - $xm1) * cos($rotate) - ($y - $ym1) * sin($rotate)   + $xm2;
            $yr = ($x - $xm1) * sin($rotate) + ($y - $ym1) * cos($rotate)   + $ym2;
            
            $this->_points[$i] = array($xr, $yr);
        }
        
        return $this;
    }
    
    public function crop($xOffset, $yOffset) {
        foreach ($this->_points as $i => $pts) {
            $this->_points[$i] = array($pts[0] - $xOffset, $pts[1] - $yOffset);
        }
        
        return $this;
    }
    
    public function getID() {
        $ps = $this->getPointNumber();
        $ls = count($this->_lines);
        $user_id = $_SESSION['user_id'];
        
        $q = new myQuery("SELECT tem.id, 
                            COUNT(DISTINCT p.n) as ps, 
                            COUNT(DISTINCT l.n) as ls,
                            COUNT(DISTINCT sym) as syms 
                            FROM tem 
                            LEFT JOIN point as p on p.tem_id=tem.id 
                            LEFT JOIN line as l on l.tem_id=tem.id
                            WHERE tem.public=1 OR tem.user_id={$user_id}
                            GROUP BY tem.id
                            HAVING ps={$ps} AND ls={$ls} AND syms>0");
                            
        if ($q->get_num_rows() >= 1) {
            return intval($q->get_one());
        } else {
            return false;
        }
    }
    
    public function mirror($sym, $w) {
        // create the mirror-reversed version of the file
        // use the sym file in argument1
        
        if (empty($sym)) {
            $sym = $this->getID();
        }
        
        if (is_int($sym)) {
            // $sym is a tem_id, get the sym file from the database
            $q = new myQuery("SELECT n, sym FROM point WHERE tem_id={$sym} AND sym IS NOT NULL");
            $sym = $q->get_assoc(false, 'n', 'sym');
        }
        
        if ($this->getPointNumber() != count($sym)) {
            return false;
        }
        
        $mirror_pts = array();
        foreach ($this->_points as $i => $pts) {
            $mirror_pts[$i] = $this->getPoints($sym[$i]);
        }
        
        foreach ($mirror_pts as $i => $pts) {
            $pts[0] = $w - $pts[0]; // adjust for flipping
            $this->setPoints($i, $pts);
        }

        return $this;
    }
    
    public function printTem($addDesc = true) {
        $return = '';
        
        // return string in PsychoMorph .tem format
        $return .= $this->getPointNumber() . "\n";
        
        foreach ($this->_points as $p) {
            $x = round($p[0],2);
            $y = round($p[1],2);
            $return .= "$x\t$y\n";
        }
        
        $return .= count($this->_lines) . "\n";
        
        foreach ($this->_lines as $l) {
            $return .= "0\n";
            $return .= count($l) . "\n";
            $return .= implode(" ", $l) . "\n";
        }
        
        $return .= count($this->_lines)-1 . "\n";

        // add description (must be at end)
        if ($addDesc) {
            // update last_saved comment
            $this->setDescription('last_saved', date('Y-m-d H:i:s'));
            $json = $this->getDescription();
            $return .= "\n{$json}\n";
        }
        
        return $return;
    }
    
    public function tem2SVG($args) {
        // get width and height if not set
        if ( !array_key_exists('width', $args) || !array_key_exists('height', $args) ) {
            foreach ($this->_points as $i => $pt) {
                $allx[] = $pt[0];
                $ally[] = $pt[1];
            }
            $args['width'] = ceil(max($allx) + min($allx));
            $args['height'] = ceil(max($ally) + min($ally));
        }
        
        // start svg
        $svg = array();
        $svg[] = "<svg xmlns='http://www.w3.org/2000/svg'";
        $svg[] = "     width='{$args['width']}' height='{$args['height']}'";
        $svg[] = "     xmlns:xlink='http://www.w3.org/1999/xlink'>";
        
        // get image
        if (array_key_exists('image', $args)) {
            $svg[] = "<image width='{$args['width']}' height='{$args['height']}' xlink:href='file://".IMAGEBASEDIR."{$args['image']}' />";
        }
        
        // get lines
        if (array_key_exists('lines', $args)) {
            $svg[] = $this->lines2SVG($args['lines']);
        }
        
        // get points
        if (array_key_exists('points', $args)) {
            $svg[] = $this->points2SVG($args['points']);
        }
        
        $svg[] = '</svg>';
        return implode(PHP_EOL, $svg);
    } 
    
        
    public function points2SVG($args) {
        // set defaults if not specified
        $strokewidth = ifEmpty($args['strokewidth'], 1);
        $radius = ifEmpty($args['radius'], 5);
        $style = ifEmpty($args['style'], 'cross');
        $color = ifEmpty($args['color'], 'rgb(0,255,0)');
        $fill = ifEmpty($args['fill'], 'rgba(0,0,0,0)');
        $center = $radius + $strokewidth/2;
        
        // get point names
        $tem_id = $this->getID();
        $q = new myQuery("SELECT n, name FROM point WHERE tem_id={$tem_id}");
        $names = $q->get_key_val('n', 'name');
        
        // set up $style symbol in defs
        $svg[] = "<defs>";
        $svg[] = "    <symbol id='pt'>";
        if ($style == "circle") {
            $svg[] = "        <circle cx='{$center}' cy='{$center}' r='{$radius}' />";
        } else if ($style == "numbers") {
            $svg[] = "        <circle cx='$center' cy='$center' r='1' />";
        } else if ($style == "cross") {
            $svg[] = "        <line x1='{$center}' y1='{$strokewidth}' x2='{$center}' y2='" . ($radius*2) . "' />";
            $svg[] = "        <line x1='{$strokewidth}' y1='{$center}' x2='" . ($radius*2) . "' y2='{$center}' />";
        } else if ($style == "star") {
            $svg[] = "        <path  d='";
            $svg[] = "               M " . (0.000*$radius+$center) . " " . (0.500*$radius+$center);
            $svg[] = "               L " . (0.588*$radius+$center) . " " . (0.809*$radius+$center);
            $svg[] = "               L " . (0.476*$radius+$center) . " " . (0.155*$radius+$center);
            $svg[] = "               L " . (0.951*$radius+$center) . " " . (-0.309*$radius+$center);
            $svg[] = "               L " . (0.294*$radius+$center) . " " . (-0.405*$radius+$center);
            $svg[] = "               L " . (0.000*$radius+$center) . " " . (-1.000*$radius+$center);
            $svg[] = "               L " . (-0.294*$radius+$center) . " " . (-0.405*$radius+$center);
            $svg[] = "               L " . (-0.951*$radius+$center) . " " . (-0.309*$radius+$center);
            $svg[] = "               L " . (-0.476*$radius+$center) . " " . (0.155*$radius+$center);
            $svg[] = "               L " . (-0.588*$radius+$center) . " " . (0.809*$radius+$center);
            $svg[] = "               L " . (0.000*$radius+$center) . " " . (0.500*$radius+$center);
            $svg[] = "         '/>";
        }
        $svg[] = "    </symbol>";
        $svg[] = "</defs>";
    
        // start group for points
        $svg[] = "<g id='points' stroke='{$color}' fill='{$fill}'";
        $svg[] = "   stroke-width='{$strokewidth}' stroke-linecap='round'";
        $svg[] = "   transform='translate(-{$center}, -{$center})'>";

        foreach ($this->_points as $i => $pt) {
            $x = round($pt[0], 2);
            $y = round($pt[1], 2);
            $svg[] = "    <!-- {$names[$i]} -->";
            $svg[] = "        <use xlink:href='#pt' id='pt{$i}' x='{$x}' y='{$y}'/>";
        }
        
        $svg[] = "</g>";
        
        // add number
        if ($style == "numbers") {
            $svg[] = "<g id='pointnumbers' stroke='black' fill='black' font-size='12' font-family='monospace'>";
            foreach ($this->_points as $i => $pt) {
                $x = round($pt[0], 2);
                $y = round($pt[1], 2);
                $svg[] = "    <!-- {$names[$i]} -->";
                $svg[] = "        <text  id='n{$i}' x='{$x}' y='{$y}'>{$i}</text>";
            }
            $svg[] = "</g>";
        }
        
        return implode(PHP_EOL, $svg);
    }        
    
    public function lines2SVG($args) {
        $strokewidth = ifEmpty($args['strokewidth'], 1);
        $color = ifEmpty($args['color'], 'rgb(0,0,255)');
        
        $svg = array();
        $svg[] = "<g id='lines' stroke='{$color}' stroke-width='{$strokewidth}' stroke-linecap='round' fill='rgba(0,0,0,0)'>";
        
        // get point names
        $tem_id = $this->getID();
        $q = new myQuery("SELECT n, name FROM point WHERE tem_id={$tem_id}");
        $names = $q->get_key_val('n', 'name');

        
        foreach ($this->_lines as $i => $line) {
            $v = array();
            foreach ($line as $pt) {
                $x = $this->_points[$pt][0];
                $y = $this->_points[$pt][1];
                $v[] = array($x, $y);
            }

            $path = implode(PHP_EOL, svgBezier($v));
            
            $lineName = $names[$line[0]] . ' to ' . $names[$line[count($line)-1]];
            $svg[] = "    <!-- {$lineName} -->";
            $svg[] = "    <path id='line{$i}' d='{$path}' />";
        }
        
        $svg[] = '</g>';
        
        return implode(PHP_EOL, $svg);
    }
    
    public function _saveFile($filepath) {
        if (empty($filepath)) {
            $filepath = $this->getPath();
        }
    
        if ('tem' !== pathinfo($filepath, PATHINFO_EXTENSION)) { $filepath .= '.tem'; }
    
        if (   !($file = fopen($filepath, 'w'))        // file can be opened
            || !fwrite($file, $this->printTem())    // file can be written to
            || !fclose($file)                        // file can be closed
        ) {
            return false;
        }
        
        chmod($filepath, IMGPERMS);                // make sure only web user can access
        touch(IMAGEBASEDIR . $this->getProject()); // update filemtime for the project directory
        
        return true;
    }

}

?>