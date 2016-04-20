<?php

/**************************************************************************
 * PsychoMorph Classes
 *
 * PHP version 5
 *
 * @author     Lisa DeBruine <debruine@gmail.com>
 * @copyright  2013 Face Research Lab
 *************************************************************************/

require_once 'psychomorph.image.class.php';
require_once 'psychomorph.tem.class.php';

  /**************************************************************************
 * Masking functions
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
  
 /**************************************************************************
 * PsychoMorph_ImageTem
 *
 * Associates an image and its tem file
 *************************************************************************/

class PsychoMorph_ImageTem {
    private $_img;
    private $_tem;
    private $_overWrite = false;
    
    public function __construct($imgpath, $tempath = null) {
        $this->_img = new PsychoMorph_Image($imgpath);        
        if (empty($tempath)) { 
            $tempath = preg_replace('@\.(jpg|png|gif)$@', '.tem', $this->_img->getPath()); 
        }
        $this->_tem = new PsychoMorph_Tem($tempath);
    }
    
    public function getImg() { return $this->_img; }
    public function getTem() { return $this->_tem; }
    public function getURL() { return $this->getImg()->getURL(); }
    
    public function getWidth() { return $this->_img->getWidth(); }
    public function getHeight() { return $this->_img->getHeight(); }
    
    public function setDescription($v) {
        return $this->getImg()->setDescription($v);
    }
    
    public function setOverWrite($o) { 
        if ($o == true || $o == 'true' || $o == 1) {
            $this->_overWrite = true;
        } else {
            $this->_overWrite = false;
        }
        
        return $this;
    }
    public function getOverWrite() { return $this->_overWrite; }
    
    public function resize($xResize, $yResize = null) {
        if ($yResize == null) { $yResize = $xResize; }
        if ($xResize <= 0 || $yResize<=0 || $xResize > 10 || $yResize > 10) {
            // resize is too small or too big
            return false;
        }
        
        if ($this->_img) { $this->_img->resize($xResize, $yResize); }
        if ($this->_tem) { $this->_tem->resize($xResize, $yResize); }
        
        return $this;
    }
    
    public function rotate($degrees, $rgb = null) {
        if (empty($degrees)) { return false; }
        
        if ($this->_img) { 
            $origW = $this->_img->getWidth();
            $origH = $this->_img->getHeight();
            $this->_img->rotate($degrees, $rgb);
            
            if ($this->_tem) { 
                $this->_tem->rotate(
                    $degrees, 
                    $origW,
                    $origH,
                    $this->_img->getWidth(), 
                    $this->_img->getHeight()
                ); 
            }
        }
        
        return $this;
    }
    
    public function crop($xOffset, $yOffset, $width, $height, $rgb = null) {
        if ($this->_img) {
            $this->_img->crop($xOffset, $yOffset, $width, $height, $rgb);
            if ($this->_tem) {
                $this->_tem->crop($xOffset, $yOffset);    
            }
        }
        
        return $this;
    }
    
    public function mask($mask = array("face", "neck", "ears"), $rgba = array(255,255,255,1), $blur = 0, $custom = null) {
        ini_set('memory_limit','1024M');
        
        $default_masks = array(
            "oval" =>         array(
                                array(145,146,147,148,149,150,151,152,153,154,155,156,157,184,183,145)
                            ),
            "face" =>         array(
                                array(111,186,110,185,109,134,135,136,137,138,139,140,141,142,143,
                                  144,112,187,113,188,114,133,132,131,130,129,128,127,126,125,111)
                            ),
            "left_ear" =>     array(
                                array(111,119,118,117,116,115,109),
                                array(109,185,110,186,111)
                            ),
            "right_ear" =>    array(
                                array(112,120,121,122,123,124,114), 
                                array(114,188,113,187,112)
                            ),
            "neck" =>         array(
                                array(132,157), // right side of neck
                                array(157,184,183,145), // bottom of neck
                                array(145, 126), // left side of neck
                                array(126,127,128,129,130,131,132) // chin
                            ),
            "left_eye" =>     array(
                                array(18,19,20,21,22),
                                array(22,30,29,28,18)
                            ),
            "right_eye" =>     array(
                                array(23,24,25,26,27),
                                array(27,33,32,31,23)
                            ),
            "left_brow" =>     array(
                                array(71,72,73,74,75,76),
                                array(76,84,83,71)
                            ),
            "right_brow" => array(
                                array(77,78,79,80,81,82),
                                array(82,86,85,77)
                            ),
            "mouth" =>        array(
                                array(87,88,89,90,91,92,93),
                                array(93,108,107,106,105,104,87)
                            ),
            "teeth" =>        array(
                                array(87,94,95,96,97,98,93),
                                array(93,103,102,101,100,99,87)
                            ),
            "nose" =>         array(
                                array(50,61,62,63,64,180,55,181,69,68,67,66,56,50)
                            )
        );
        
        if (!($this->_img) || !($this->_tem)) { 
            return false;
        }
        
        $masks = array();
        foreach ($mask as $sm) {
            if ($sm == 'custom') { 
                $masks = $custom;
            } else {
                $masks[] = $default_masks[$sm];
            }
        }
        
        //print_r($masks);

        $blur = ($blur < 0) ? 0 : ($blur > 30) ? 30 : $blur;
        
        $this->resize(2.0);
        
        // load original image
        $img = $this->_img;
        $original_image = $img->getImage();
        $tem = $this->_tem;
        $pointNumber = $tem->getPointNumber();
        $temPoints = $tem->getPoints();
        $lineVectors = $tem->getLines();
        $original_width = $img->getWidth();
        $original_height = $img->getHeight();
        
        
        
        // create mask image and allocate bg and transparent colours
        $maskimg = imagecreate($original_width, $original_height); 
        $bgcolor = imagecolorallocate($maskimg, $rgba[0], $rgba[1], $rgba[2]);
        $r = ($rgba[0] == 255) ? 254 : $rgba[0] + 1;
        $g = ($rgba[1] == 255) ? 254 : $rgba[1] + 1;
        $b = ($rgba[2] == 255) ? 254 : $rgba[2] + 1;
        $trans = imagecolorallocate($maskimg, $r, $g, $b); // make transform colour very close to bgcolor
        imagecolortransparent($maskimg, $trans);
        imageantialias($maskimg, true);
        
        // construct sets of Bezier curves
        $polygons = array();
        foreach ($masks as $mask) {
            $polygon = array();
            foreach ($mask as $m) {
                $subpolygon = array();
                $mask_pts = array();
                foreach ($m as $pt) {
                    $mask_pts[] = $temPoints[$pt][0];
                    $mask_pts[] = $temPoints[$pt][1];
                }
                
                if (count($m) == 2) {
                    // just a straight line, no need for curves
                    $subpolygon = $mask_pts;
                } else {
                    $subpolygon = drawSpline($mask_pts);
                }
                $polygon = array_merge($polygon, $subpolygon);
            }
            
            imagefilledpolygon($maskimg,$polygon,count($polygon)/2,$trans);
            $polygons[] = $polygon;
        }
        imagefill($maskimg, 0, 0, $bgcolor); // fill with mask color
        
        imageantialias($maskimg, false);
        $gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
        
        for ($blurlevel = 0; $blurlevel <= $blur; $blurlevel++) {
            imagesetthickness($maskimg, $blurlevel);
        
            // construct sets of Bezier curves
            foreach ($polygons as $polygon) {                
                imagepolygon($maskimg,$polygon,count($polygon)/2,$trans);
            }

            //imagefilter($maskimg, IMG_FILTER_GAUSSIAN_BLUR);
            //imageconvolution($maskimg, $gaussian, 16, 0);
            
            $blurtrans = ($blur == $blurlevel || $blur == 0) ? 100 : 100/$blur;
            imagecopymerge($original_image, $maskimg, 0, 0, 0, 0, $original_width, $original_height, $blurtrans);
        }
        imagedestroy($maskimg);    
        
        $this->resize(0.5);

        // set transparent if alpha == 0
        if ($rgba[3] == 0) {
            imagecolortransparent($original_image, $bgcolor);
        }
        
        //$img->setImage($original_image);

        $img->setDescription(array(
            "mask" => array(
                "mask" => $mask,
                "color" => $rgba,
                "blur" => $blur
            )
        ));
        
        return $this;
    }

    // defaults to facelab alignment
    public function alignEyes(    $aWidth = 1350, $aHeight = 1800, 
                                $aLeftEye = array(496.98, 825.688), 
                                $aRightEye = array(853.02,825.688),
                                $pt1 = 0, $pt2 = 1, $rgb = null) {
        
        $oLeftEye = $this->_tem->getPoints($pt1);
        $oRightEye = $this->_tem->getPoints($pt2);
        
        if ($pt1 == $pt2) {
            // align points are the same, so no resize needed, 
            // make sure that left and right align coordinates are the same, too 
            // (defaults to 1st entry if different)
            $aRightEye = $aLeftEye;
        }
        
        // add to image description
        $this->_img->setDescription(array("aligned" => array(
            "pt1" => array(
                "pt" => $pt1,
                "x" => $aLeftEye[0],
                "y" => $aLeftEye[1]
            ),
            "pt2" => array(
                "pt" => $pt2,
                "x" => $aRightEye[0], 
                "y" => $aRightEye[1]
            ),
            "size" => array(
                "width" => $aWidth, 
                "height" => $aHeight
            )
         )));
        
        // calculate rotation
        $rotate = ($oLeftEye[0] - $oRightEye[0] == 0) ? 1.57069633 : atan(($oLeftEye[1] - $oRightEye[1])/($oLeftEye[0] - $oRightEye[0]));
        $rotate_aligned = ($aLeftEye[0] - $aRightEye[0] == 0) ? 1.57069633 : atan(($aLeftEye[1] - $aRightEye[1])/($aLeftEye[0] - $aRightEye[0]));
        $rotate -= $rotate_aligned;    
        $degrees = rad2deg($rotate);
        $this->rotate(-1*$degrees, $rgb);    
        
        // calculate resize needed
        $aEyeWidth = sqrt(pow($aLeftEye[0]-$aRightEye[0], 2) + pow($aLeftEye[1]-$aRightEye[1], 2));
        $oEyeWidth = sqrt(pow($oLeftEye[0]-$oRightEye[0], 2) + pow($oLeftEye[1]-$oRightEye[1], 2));
        $resize = ($aEyeWidth == 0 || $oEyeWidth == 0) ? 1 : $aEyeWidth / $oEyeWidth;
        $this->resize($resize);
        
        // recalculate eye position
        $newLeftEye = $this->_tem->getPoints($pt1);
        $xOffset = $newLeftEye[0] - $aLeftEye[0];
        $yOffset = $newLeftEye[1] - $aLeftEye[1];
        $this->crop($xOffset, $yOffset, $aWidth, $aHeight, $rgb);
        
        return $this;
    }
    
    public function convert($ext) {
        return $this->getImg()->convert($ext);
    }
    
    public function deletePoints($pointArray) {
        return $this->getTem()->deletePoints($pointArray);
    }
    
    public function setLines($newLines) {
        return $this->getTem()->setLines($newLines);
    }
    
    public function mirror($sym = null) {
        // create the mirror-reversed version of the file
        // use the sym file in argument1 or get the best match from the database
        
        if ($this->_img) $this->getImg()->mirror();
        $w = $this->getImg()->getWidth();
        if ($this->_tem) $this->getTem()->mirror($sym, $w);
        
        return $this;
    }
    
    public function save($imgname = null, $temname = null) {
        // if no image name is set, set to current path
        if (empty($imgname)) {
            $imgname = $this->getImg()->getPath();
        }
        
        // if the path is still empty, quit and do not save
        if (empty($imgname)) {
            return false;
        }
        
        if (is_array($imgname)) {
            if (!$this->_img->save($imgname, $this->_overWrite)) {
                return false;
            } else {
                $imgname = $this->_img->getPath();
            }
        }
                
        // if image name does not have a valid extension, default to jpg
        if (!preg_match('@\.(jpg|png|gif)$@', $imgname)) {
            $imgname .= '.jpg';
        }
        
        // if tem name is not specified, set to default name
        if (empty($temname)) { 
            $temname = preg_replace('@\.(jpg|png|gif)$@', '.tem', $imgname);
        }
    
        $this->_img->setEmbeddedTem($this->_tem->printTem());
        if (!$this->_img->save($imgname, $this->_overWrite)) {
            return false;
        }

        $this->_tem->save($temname, $this->_overWrite);
        
        return $this;
    }
}

?>
