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
 include_once 'png_reader.class.php';
 include_once('PEL/src/PelJpeg.php');
 
function mean($values) {
    // return the mean of an array of values
    $n = count($values);
    if (0 == $n) return false;
    $mean = array_sum($values) / $n;
    
    return $mean;    
}

function mode($values, $p = 0) {
    // returns modal value or average of $p % at the mode
    
    sort($values);
    $n = count($values);
    
    if ($p == 0) {
        if ($n%2 == 0) {
            $mode =  $values[($n/2)-1];
        } else {
            $mode = ( $values[floor($n/2)-1] + $values[ceil($n/2)-1] ) / 2;
        }
    } else {
        $get = round($n * $p);             // what percent of the total to average
        if ($n%2 != $get%2) { $get++; } // if $get doesn't centre on $n, add 1 to $get
        
        $start = ($n - $get)/2;
        $end = $start + $get;
        for ($i = $start; $i < $end; $i++) {
            $inmode[] = $values[$i];
        }
        $mode = mean($inmode);
    }
    
    return $mode;
}

function stdev($v1, $v2 = NULL) {
    // returns the SD of an array of values
    
    if (is_null($v2)) {
        // calculate SD for one array
        $values = $v1;
    } else {
        // calculate SD for difference between 2 arrays
        $values = array();
        foreach ($v1 as $k => $v) {
            $values[] = $v1[$k] - $v2[$k];
        }
    }
    
    $n = count($values);
    if (0 == $n) return false;
    
    $mean = mean($values);
    foreach ($values as $x) {
        $d2[] = ($mean - $x) * ($mean - $x);
    }
    $sum_squares = array_sum($d2);
    $stdev = sqrt($sum_squares/($n-1));
    
    return $stdev;
}

function sterror($values) {
    // returns the standard error of the mean for an array of values
    
    $n = count($values);
    if (0 == $n) return false;
    
    $sd = stdev($values);
    $sterror = $sd / sqrt($n);
    
    return $sterror;
}

function deltaE($lab1, $lab2, $l = 1, $c = 1) {
    // http://www.brucelindbloom.com/index.html?ColorCheckerCalcHelp.html

    $L1 = $lab1['L'];
    $a1 = $lab1['a'];
    $b1 = $lab1['b'];
    $L2 = $lab2['L'];
    $a2 = $lab2['a'];
    $b2 = $lab2['b'];
    
    $H1 = rad2deg(atan($b1/$a1));
    if ($H1 < 0) {
        $H1 = $H1 + 360;
    } else if ($H1 >= 360) {
        $H1 = $H1 - 360;
    }
    
    if ($H1 >= 164 && $H1 <= 345) {
        $T = 0.56 + abs( 0.2 * cos($H1 + 168) );
    } else {
        $T = 0.36 + abs( 0.4 * cos($H1 + 35) );
    }

    $C1 = sqrt(($a1*$a1) + ($b1*$b1));
    $C2 = sqrt(($a2*$a2) + ($b2*$b2));
    $deltaC = $C1 - $C2;
    
    $deltaL = $L1 - $L2;
    $deltaa = $a1 - $a2;
    $deltab = $b1 - $b2;
    
    $deltaH = sqrt( ($deltaa * $deltaa) + ($deltab * $deltab) - ($deltaC * $deltaC) );
    
    if ($L1 < 16) {
        $S_L = 0.511;
    } else {
        $S_L = (0.040976 * $L1) / (1 + (0.01765 * $L1));
    }
    
    $S_C = ((0.0638 * $C1) / (1 + (0.0131 * $C1))) + 0.638;
    
    $F = sqrt( pow($C1, 4) / (pow($C1, 4) + 1900) );
    
    $S_H = $S_C * (($F*$T) + 1 - $F);
    
    $deltaE = sqrt(
        pow(($deltaL / ($l * $S_L)), 2) +
        pow(($deltaC / ($c * $S_C)), 2) +
        pow(($deltaH / $S_H), 2)
    );
    
    return $deltaE;
}
 
function rgb2lab($r, $g, $b) {
     // checked at http://www.brucelindbloom.com/index.html?ColorCheckerCalcHelp.html
 
    /**********************************************************************
    * RGB to XYZ via http://www.easyrgb.com/index.php?X=MATH&H=02#text2
    ***********************************************************************/
    
     // change to 0-1
     $rgb = array(
        'r' => $r/255, 
        'g' => $g/255, 
        'b' => $b/255
    );

    // inverse sRGB companding
    foreach ($rgb as $i => $v) {
        if ( $v > 0.04045 ) {
            $rgb[$i] = pow( (( $v + 0.055 ) / 1.055 ), 2.4);
        } else { 
            $rgb[$i] = $v / 12.92;
        }
        
        $rgb[$i] = $rgb[$i] * 100;
    }

    //Observer. = 2°, Illuminant = D65
    $X = $rgb['r'] * 0.4124 + $rgb['g'] * 0.3576 + $rgb['b'] * 0.1805;
    $Y = $rgb['r'] * 0.2126 + $rgb['g'] * 0.7152 + $rgb['b'] * 0.0722;
    $Z = $rgb['r'] * 0.0193 + $rgb['g'] * 0.1192 + $rgb['b'] * 0.9505;
    
    /**********************************************************************
    * XYZ to CieL*ab via http://www.easyrgb.com/index.php?X=MATH&H=07#text7
    ***********************************************************************/
    
    // Observer= 2°, Illuminant= D65
    // see http://www.brucelindbloom.com/index.html?ColorCheckerCalcHelp.html for other values
    $ref_X =  95.047;  
    $ref_Y = 100.000;
    $ref_Z = 108.883;
    
    $xyz = array(
        'x' => $X / $ref_X,         
        'y' => $Y / $ref_Y,
        'z' => $Z / $ref_Z
    );
    
    foreach ($xyz as $i => $v) {
        if ( $v > 0.008856 ) {
            $xyz[$i] = pow($v, ( 1/3 ));
        } else {
            $xyz[$i] = ( 7.787 * $v ) + ( 16 / 116 );
        }
    } 
    
    $L = ( 116 * $xyz['y'] ) - 16;
    $a = 500 * ( $xyz['x'] - $xyz['y'] );
    $b = 200 * ( $xyz['y'] - $xyz['z'] );
    
    return array(
        'X' => $X,
        'Y' => $Y,
        'Z' => $Z,
        'L' => $L,
        'a' => $a,
        'b' => $b,
        'lab' => array('L' => round($L,2), 'a' => round($a,2), 'b' => round($b,2)),
    );
}

function rgb2rgbpoly($rgb, $number) {

    /*
     takes single pixel value and returns function of rgb values depending on 
     the value of number:
    %
    % 3 : r g b
    % 5 : r g b rgb 1
    % 6 : r g b rg rb gb
    % 8 : r g b rg rb gb rgb 1
    % 9 : r g b rg rb gb r2 g2 b2
    % 11: r g b rg rb gb r2 g2 b2 rgb 1
    */

    if ($number == 3) {
        $rgb_poly = array($rgb[0], $rgb[1], $rgb[2]);
    } else if ($number == 5) {
        $rgb_poly = array($rgb[0], $rgb[1], $rgb[2], $rgb[0]*$rgb[1]*$rgb[2], 1);
    } else if ($number == 6) {
        $rgb_poly = array($rgb[0], $rgb[1], $rgb[2], $rgb[0]*$rgb[1], $rgb[0]*$rgb[2], $rgb[1]*$rgb[2]);
    } else if ($number == 8) {
        $rgb_poly = array($rgb[0], $rgb[1], $rgb[2], $rgb[0]*$rgb[1], $rgb[0]*$rgb[2], $rgb[1]*$rgb[2], $rgb[0]*$rgb[1]*$rgb[2], 1);
    } else if ($number == 9) {
        $rgb_poly = array($rgb[0], $rgb[1], $rgb[2], $rgb[0]*$rgb[1], $rgb[0]*$rgb[2], $rgb[1]*$rgb[2], pow($rgb[0],2), pow($rgb[1],2), pow($rgb[2],2));
    } else if ($number == 11) {
        $rgb_poly = array($rgb[0], $rgb[1], $rgb[2], $rgb[0]*$rgb[1], $rgb[0]*$rgb[2], $rgb[1]*$rgb[2], pow($rgb[0],2), pow($rgb[1],2), pow($rgb[2],2), $rgb[0]*$rgb[1]*$rgb[2], 1);
    }
    
    return $rgb_poly;
}
 
 /**************************************************************************
 * PsychoMorph_Image
 *************************************************************************/
 
class PsychoMorph_Image extends PsychoMorph_File {
    private $_image = false;
    public $_description = '';
    private $_embeddedTem = '';
    private $_id;
    
    public function __destruct() {
        if ($this->_image) imagedestroy($this->_image);
    }
    
    public function _loadFile() {
        $this->_image = false;
        $path = $this->getPath();
        if (!file_exists($path)) { 
            return false; 
        }
        
        // check all image types in order: jpg, png, gif
        if (exif_imagetype($path) == IMAGETYPE_JPEG) {
            $img = @imagecreatefromjpeg($path);
        } else if (exif_imagetype($path) == IMAGETYPE_PNG) {
            $img = @imagecreatefrompng($path);
        } else if (exif_imagetype($path) == IMAGETYPE_GIF) {
            $img = @imagecreatefromgif($path);
        } else {
            return false;
        }
            
        $this->_image = $img;
        
        // set description
        //$exif = $this->_readExif();
        //if (empty($exif['ImageDescription'])) {
            $this->setDescription(array('original' => $this->getUserPath()));
        //} else {
        //    $this->setDescription($exif['ImageDescription']));
        //}
        
        return $this;
    }
    
    public function setImage($img) {
        if ($this->_image) imagedestroy($this->_image);
        $this->_image = $img;
        
        return $this;
    }
    
    public function setImageBase64($b64) {
        $data = base64_decode($b64);
        
        $this->_image = imagecreatefromstring($data);
        
        return $this;
    }
        
    public function setDescription($v) {
        // sets description of image for exif
        // now formatted as an array to be saved in exif as JSON
    
        if (!is_array($v)) {
            $v = array('desc' => $v);
        }
    
        if (!is_array($this->_description)) {
            $this->_description = array($this->_description);
        }
        
        array_push($this->_description, $v);
        
        return $this;
    }
    
    public function getDescription() { 
        $d = $this->_description;
        
        if (!is_array($d)) {
            $d = array('desc' => $d);
        }
    
        return json_encode($d, true);
    }
    
    public function setEmbeddedTem($v) { 
        $this->_embeddedTem = $v; 
        return $this;
    }
    public function getEmbeddedTem() { return $this->_embeddedTem; }
    
    public function getImage() { return $this->_image; }
    
    public function getWidth() { 
        if (!$this->_image) { return false; }
        
        return imagesx($this->_image); 
    }
    
    public function getHeight() { 
        if (!$this->_image) { return false; }
        
        return imagesy($this->_image); 
    }
    
    public function getImg() { return $this; }
    
    private function _makeThumb($original_image) {
        // Get new dimensions
        $width = imagesx($original_image);
        $height = imagesy($original_image);
        
        $new_height = 100;
        $new_width = $width * $new_height / $height;
    
        // Resample
        $thumbnail_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled(
            $thumbnail_image, $original_image, 
            0, 0, 
            0, 0, 
            $new_width, $new_height, 
            $width, $height
        );
        
        return $thumbnail_image;
    }
    
    private function _nextImg() {
        $exp_path = explode("/", $this->getUserPath());
        $project_id = $exp_path[0];
        unset($exp_path[0]);
        $name = "/" . implode("/", $exp_path);
        
        // get next id from uploads table
        $img_query = new myQuery(sprintf(
            "INSERT INTO img (user_id, dt, name, project_id) 
            VALUES ('%d', NOW(), '%s', '%s')
            ON DUPLICATE KEY UPDATE
            user_id='%d', dt=NOW()",
            
            $_SESSION['user_id'],
            $name,
            $project_id,
            $_SESSION['user_id']
        ));
        $this->_id = $img_query->get_insert_id();
        
        return $this->_id;
    }
    
    private function _readExif() {
        $filename = $this->getPath();
        if (file_exists($filename) && exif_imagetype($filename) == IMAGETYPE_JPEG) {
            $exif = exif_read_data($filename);
            return $exif;
        }
        
        return false;
    }
    
    private function _addExif($filename) {
        //if ($_SERVER['SERVER_NAME'] == 'test.psychomorph') return true;    
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
                // Create exif Image File Directory and associate it with the TIFF data.
                $exif_ifd = new PelIfd(PelIfd::EXIF);
                $ifd0->addSubIfd($exif_ifd);    
            }
            
            if (!$ifd1 = $ifd0->getNextIfd()) {
                // thumbnail does not exist
                $ifd1 = new PelIfd(1);
                $ifd0->setNextIfd($ifd1);
                //$original = ImageCreateFromString($jpeg->getBytes()); # create image resource of original
                //$thumb = makeThumb($original);
                $thumb = $this->_makeThumb($this->getImage());
                
                // start writing output to buffer
                ob_start();       
                // outputs thumb resource contents to buffer
                ImageJpeg($thumb);   
                // create PelDataWindow from buffer thumb contents (and end output to buffer)
                $window = new PelDataWindow(ob_get_clean()); 
                
                if ($window) {   
                    $ifd1->setThumbnail($window); # set window data as thumbnail in ifd1
                }
                
                //imagedestroy($original);
                imagedestroy($thumb);
            }
            
            $exifdata = array(
                PelTag::IMAGE_DESCRIPTION => $this->getDescription(),
                PelTag::COPYRIGHT => "webmorph.org: " 
                                    . $_SESSION['user_id'] 
                                    . ': IMG_ID: ' ,
                                    //. $this->_id,
                PelTag::USER_COMMENT => $this->_embeddedTem,
            );
            
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

    
    public function resize($xResize, $yResize = null) {
        if ($yResize == null) { $yResize = $xResize; }
        if ($xResize <= 0 || $yResize<=0 || $xResize > 10 || $yResize > 10 || !$this->_image) {
            // resize is too small or too big or image doesn't exist
            return false;
        }
        
        // resize image
        $width = $this->getWidth();
        $height = $this->getHeight();
        $new_width = $width * $xResize;
        $new_height = $height * $yResize;
        $resized_image = imagecreatetruecolor($new_width, $new_height);
    
        imagecopyresampled(
            $resized_image, $this->_image, 
            0, 0, 
            0, 0, 
            $new_width, $new_height, 
            $width, $height
        );
        $this->setImage($resized_image);
        if ($xResize == $yResize) {
            $this->setDescription(array('resize' => round($xResize*100,2) . '%'));
        } else {
            $this->setDescription(array('resize' => array(
                    'x' => round($xResize*100,2) . '%',
                    'y' => round($yResize*100,2) . '%'
            )));
        }
        
        return $this;
    }
    
    public function rotate($degrees, $rgb = null) {
        // positive numbers rotate counter-clockwise, 
        // so reverse because clockwise seems more natural
        $deg = -1 * $degrees;  
        if (empty($deg) || !$this->_image) { return false; }
        
        // get background color from $rgb or upper right corner
        if (is_array($rgb) && count($rgb) == 3) {
            $color = imagecolorallocate($this->_image, $rgb[0], $rgb[1], $rgb[2]);
        } else {
            $color = imagecolorat($this->_image, 1, 1); 
        }
        
        $rotated_image = imagerotate($this->_image, $deg, $color);
        $this->setImage($rotated_image);
        $this->setDescription(array("rotate" => round($degrees,2) . "°"));
        
        return $this;
    }
    
    public function crop($xOffset, $yOffset, $width, $height, $rgb = null) {
        $newimg = imagecreatetruecolor($width, $height);
        
        // get background color from $rgb or upper right corner
        if (is_array($rgb) && count($rgb) == 3) {
            $color = imagecolorallocate($newimg, $rgb[0], $rgb[1], $rgb[2]);
        } else {
            $color = imagecolorat($this->_image, 1, 1); 
        }
        
        imagefill($newimg, 0, 0, $color);
        
        $w = $this->getWidth();
        $h = $this->getHeight();
        
        $dst_x = ($xOffset < 0) ? -1*$xOffset : 0;
        $dst_y = ($yOffset < 0) ? -1*$yOffset : 0;
        $src_x = ($xOffset < 0) ? 0 : $xOffset;
        $src_y = ($yOffset < 0) ? 0 : $yOffset;
        $src_w = min($width, $w - $src_x);
        $src_h = min($height, $h - $src_y);
        
        imagecopy($newimg, $this->_image, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        
        $this->setImage($newimg);
        
        $this->setDescription(array(
            "crop" => array(
                "x-offset" => round($xOffset,2),
                "y-offset" => round($yOffset,2),
                "width"    => round($width,2),
                "height"   => round($height,2)
            )
        ));
        
        return $this;
    }
    
    public function mirror($sym = null) {
        // create the mirror-reversed version of the file
        // doesn't use the sym file in argument1, but included for consistency
        
        $width = $this->getWidth();
        $height = $this->getHeight();
        
        $newimg = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $newimg,     $this->_image, 
            0,             0, 
            $width-1,     0, 
            $width,     $height, 
            -$width,     $height
        );
        
        $this->setImage($newimg);    

        $this->setDescription(array("mirror" => "mirror"));
        
        return $this;
    }
    
    public function colourCalibrate($colours, $expand = 25, $SD = 2) {
        // $colours lists x-coordinate, y-coordinate, and L, a* and b* values for each colour on the checker chart
        // $expand = number of pixels plus or minus the reference x and y-corrdinates
        // $SD = exclude pixels more than $sd standard deviations from the mean for that area
        
        if (!imageistruecolor($this->_image)) { return false; }
        
        $w = $this->getWidth();
        $h = $this->getHeight();
        $rgb255 = array();

        foreach ($colours as $cname => $c) {
            // get color from x and y coordinates
            
            $imgcolor = array();
            for ($x = $c[0] - $expand; $x <= $c[0] + $expand; $x++) {
                for ($y = $c[1] - $expand; $y <= $c[1] + $expand; $y++) {
                    $rgb = imagecolorat($this->_image, $x, $y);
                    $imgcolor['r'][] = ($rgb >> 16) & 0xFF;
                    $imgcolor['g'][] = ($rgb >> 8) & 0xFF;
                    $imgcolor['b'][] = $rgb & 0xFF;
                }
            }
            
            foreach ($imgcolor as $rgb => $valuearray) {
                $mode = mode($valuearray, .25);
                $stdev = stdev($valuearray);
                
                foreach ($valuearray as $i => $v) {
                    if ($v > ($mode + ($SD * $stdev)) || $v < ($mode - ($SD * $stdev)) ) {
                        // remove from list
                        unset($valuearray[$i]);
                    }
                }
                
                $averages[$rgb] = round(mean($valuearray), 12);
                $stdevs[$rgb] = round(stdev($valuearray), 4);
                $counts[$rgb] = count($valuearray);
            }
            
            $rgb255[] = $averages;
            $lab = rgb2lab($averages['r'], $averages['g'], $averages['b']);
            $deltaE = deltaE($lab, array($c[2], $c[3], $c[4]));
            
            
            $check[$cname] = sprintf("Lab(%.1f, %.1f, %.1f)\n",
                round($lab['L'], 1),
                round($lab['a'], 1),
                round($lab['b'], 1)
            );
            
            /*
            $check .= "--myLab: (" . implode(',', $lab['lab']) . ")\n";
            $check .= "--chartLab: ($c[2], $c[3], $c[4])\n";
            $check .= "--deltaE = $deltaE\n";
            $check .= "--RGB: (" . implode(',', $averages) . ")\n";
            $check .= "--SD: (" . implode(',', $stdevs) . ")\n";
            $check .= "--N: (" . implode(',', $counts) . ")\n";
            */
            
            if ( !is_nan($deltaE) ) $deltaEs[] = $deltaE;
        }
        
        $meanDeltaE = round(mean($deltaEs), 4);
        $maxDeltaE = round(max($deltaEs), 4);
        
        $patch = ($expand * 2) + 1;
        $this->setDescription(array("colour calibrate" => array(
            "path dimension" => "$patch x $patch",
            "SD" => $SD,
            "deltaE" => array("M" => $meanDeltaE, "max" => $maxDelta),
            "check" => $check
        )));
        
        /* MATLAB code ************************************
        % Calculate camera Characterisation - Least Squares fit of rgb to Lab patches:
        
        rgb = Rgb_vals/255;
        
        for i = 1:length(rgb)
            train(i,:) = rgb2rgbpoly(rgb(i,:),11);
        end

        R=train\spectrod65;
        
        % Calculate predicted lab values from characterisation
        lab_calc = train*R;
        squaredDifference = (lab_calc-spectrod65).^2;
        meanDeltaE = mean(sqrt(sum(squaredDifference')))
        maxDeltaE = max(sqrt(sum(squaredDifference')))
        **************************************************/
        include_once "Math/Matrix.php";
        
        foreach ($colours as $v) {
            $spectrod65[] = array($v[2], $v[3], $v[4]);
        }
        
        foreach($rgb255 as $i => $v) {
            foreach ($v as $j => $c) {
                $rgb1[$i][] = $c/255;
            }
        }
        
        foreach ($rgb1 as $v) {
            $train[] = rgb2rgbpoly($v, 11);
        }
        
        $rgbM = new Math_Matrix($rgb1);
        $trainM = new Math_matrix($train);
        
        return $this;
    }
    
    public function convert($ext) {
        $allowed_ext = array('jpg', 'png', 'gif');
        
        if (in_array($ext, $allowed_ext)) {
            $oldname = $this->getPath();
            $newname = preg_replace('@\.(jpg|png|gif)$@', '.' . $ext, $oldname);
            $this->setDescription(array("convert" => $ext));
            return $this->save($newname);
        } else {
            return false;
        }
    }
    
    public function _saveFile($filepath) {
        $png_compression = 0;
        $jpg_compression = 100;
    
        if (!$this->_image) {
            //echo '{"error": "no image (' . $filepath . ')"},';
            return false;    
        }
        
        if (empty($filepath)) {
            $filepath = $this->getPath();
        }
    
        $success = false;
        
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        
        
        if ('png' == $ext) {
            $success = imagepng($this->_image, $filepath, $png_compression);
        } else if ('gif' == $ext) {
            $success = imagegif($this->_image, $filepath);
        } else {
            // default to jpeg if no extension given
            if ('jpg' !== $ext) { $filepath .= '.jpg'; }
            if (imagejpeg($this->_image, $filepath, $jpg_compression)) {
                // add exif data to jpegs
                $this->_addExif($filepath);
                $success = true;
            } else {
                echo '{"error": "no jpeg"},';
            }
        }
        
        if ($success) {
            chmod($filepath, IMGPERMS);            // make sure the file is nly readable by the web user
            $this->_setPath($filepath);
            $this->_nextImg();                // add to or update img table
            
            return true;
        }
        
        return false;
    }
}
