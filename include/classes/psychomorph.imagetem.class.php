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
 * PsychoMorph_ImageTem
 *
 * Associates an image and its tem file
 *************************************************************************/

class PsychoMorph_ImageTem {
    private $_img = false;
    private $_tem = false;
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
    public function getURL() { 
        $urls = array(
            $this->getImg()->getURL(),
            $this->getTem()->getURL()
        );
        return $urls; 
    }
    
    public function getWidth() { return $this->_img->getWidth(); }
    public function getHeight() { return $this->_img->getHeight(); }
    
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
    
    public function mask($mask = array("face", "neck", "left_ear", "right_ear"), 
                         $rgba = array(255,255,255,1), 
                         $blur = 0, $reverse = false, $custom = null) {
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
        
        // load original image
        $img = $this->_img;

        $masks = array();
        foreach ($mask as $sm) {
            if ($sm == 'custom') { 
                $masks = $custom;
            } else {
                $masks[] = $default_masks[$sm];
            }
        }

        $blur = ($blur < 0) ? 0 : ($blur > 30) ? 30 : $blur;

        // imcrease image size for smoother masks
        // resize removes alpha transparency
        if ($rgba[3] != 0) {
            $h = $img->getHeight();
            $w = $img->getWidth();
            
            $resize = floor(4000/max($h,$w));
            $resize = max($resize, 1);
            $resize = min($resize, 10);
            
            $this->resize($resize);
        }
        
        //$original_image = $img->getImage();
        $tem = $this->_tem;
        $temPoints = $tem->getPoints();
        $lineVectors = $tem->getLines();
        $original_width = $img->getWidth();
        $original_height = $img->getHeight();
        
        
        $original_image = imagecreatetruecolor($original_width, $original_height);
        imagecopy($original_image, $img->getImage(), 0, 0, 0, 0, $original_width, $original_height);
        
        // set transparent if alpha == 0
        if ($rgba[3] == 0) {
            //imagesavealpha($original_image, true);
            $otrans = imagecolorallocate($original_image, $rgba[0], $rgba[1], $rgba[2]);
            imagecolortransparent($original_image, $otrans);
        }

        // create mask image and allocate bg and transparent colours
        $maskimg = imagecreate($original_width, $original_height); 
        
        // make a color very close to mask color
        $r = ($rgba[0] == 255) ? 254 : $rgba[0] + 1;
        $g = ($rgba[1] == 255) ? 254 : $rgba[1] + 1;
        $b = ($rgba[2] == 255) ? 254 : $rgba[2] + 1;
        
        if ($reverse) {
            $bgcolor = imagecolorallocate($maskimg, $r, $g, $b);
            $fgcolor = imagecolorallocate($maskimg, $rgba[0], $rgba[1], $rgba[2]);
            imagecolortransparent($maskimg, $bgcolor);
        } else {
            $bgcolor = imagecolorallocate($maskimg, $rgba[0], $rgba[1], $rgba[2]);
            $fgcolor = imagecolorallocate($maskimg, $r, $g, $b);
            imagecolortransparent($maskimg, $fgcolor);
        }
        
        imagefill($maskimg, 0, 0, $bgcolor); // fill with mask color
        
        //imageantialias($maskimg, true);

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
            
            imagefilledpolygon($maskimg,$polygon,count($polygon)/2,$fgcolor);
            
            $polygons[] = $polygon;
        }
        //imageantialias($maskimg, false);
        
        imagecopymerge($original_image, $maskimg, 0, 0, 0, 0, $original_width, $original_height, 100);
        
        if ($blur) {
            // make a sharp copy of the image to superimpose over the blurred copy
            $sharp = imagecreatetruecolor($original_width, $original_height);
            imagecopy($sharp, $img->getImage(), 0, 0, 0, 0, $original_width, $original_height);
            $otrans = imagecolorallocate($sharp, $rgba[0], $rgba[1], $rgba[2]);
            imagecolortransparent($sharp, $otrans);    
            imagecopymerge($sharp, $maskimg, 0, 0, 0, 0, $original_width, $original_height, 100);
            imagedestroy($maskimg);
            
            for ($i = 0; $i < $blur; $i++) {
                imagefilter($original_image, IMG_FILTER_GAUSSIAN_BLUR);
            }
            
            imagecopymerge($original_image, $sharp, 0, 0, 0, 0, $original_width, $original_height, 100);
            imagedestroy($sharp);
        } else {
            imagedestroy($maskimg);
        }

        $img->setImage($original_image);
        // double image size for smoother masks
        // resize removes alpha transparency
        if ($rgba[3] != 0) {
            $this->resize(1/$resize);
        }
        
        return $this;
    }

    // defaults to facelab alignment
    public function alignEyes(  $aWidth = 1350, $aHeight = 1800, 
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
    
    public function tem2SVG($args) {
        // get width and height if not set
        if ( !array_key_exists('width', $args) || !array_key_exists('height', $args) ) {
            $args['width'] = $this->getWidth();
            $args['height'] = $this->getHeight();
        }
        
        if ($args['image'] == 'true' && $this->_img) {
            // start buffering
            ob_start();
            imagejpeg($this->_img);
            $contents =  ob_get_clean();
            ob_end_clean();

            $args['image'] = 'data:image/jpeg;base64,' . base64_encode($contents);
        } else {
            unset($args['image']);
        }
        
        $svg = $this->getTem()->tem2SVG($args);
        
        return $svg;
    }
    
    public function mirror($sym = null) {
        // create the mirror-reversed version of the file
        // use the sym file in argument1 or get the best match from the database
        
        if ($this->_img) $this->getImg()->mirror();
        $w = $this->getImg()->getWidth();
        if ($this->_tem) $this->getTem()->mirror($sym, $w);
        
        return $this;
    }
    
    public function sym($shape, $colortex, $sym = null) {
        // $shape and $colortex are transform proportions, usually 0 or 0.5
        
        if (preg_match('/^(\d{1,11})(\/.+)(.jpg|.gif|.png)$/', $this->getImg()->getURL(), $url)) {
            $project_id = $url[1];
            $ext = $url[3];
        } else {
            return false;
        }
        
        // get descriptions to replace into new sym files
        $imgdesc = $this->getImg()->_description;
        $temdesc = $this->getTem()->_description;

        // make a mirrored image
        $clone = clone $this;
        $cloneName = IMAGEBASEDIR . $project_id . '/.tmp/clone_' . time() . $ext;
        if ($clone->save($cloneName)) {
            $cloneURL = str_replace(IMAGEBASEDIR . $project_id, '', $cloneName);
            unset($clone);
        }
        
        $mirror = clone $this;
        $mirror->mirror($sym);
        $mirrorName = IMAGEBASEDIR . $project_id . '/.tmp/mirror_' . time() . $ext;
        if ($mirror->save($mirrorName)) {
            $mirrorURL = str_replace(IMAGEBASEDIR . $project_id, '', $mirrorName);
            unset($mirror);
        } else {
            return false;
        }
        
        $url = 'https://' . $_SERVER["SERVER_NAME"] . '/tomcat/psychomorph/trans?';
        // get this user's default prefs from database
        $q = new myQuery("SELECT pref, prefval FROM pref WHERE user_id='{$_SESSION['user_id']}'");
        $myprefs = $q->get_assoc(false, 'pref', 'prefval');
        
        $theData = array(
            'subfolder' => $project_id,
            'savefolder' =>  '/.tmp/',
            'count' => 1,
            'shape0' => $shape,
            'color0' => $colortex,
            'texture0' => $colortex,
            'sampleContours0' => $myprefs['sample_contours'],
            'transimage0' => $cloneURL,
            'fromimage0' => $cloneURL,
            'toimage0' => $mirrorURL,
            'norm0' => 'none', // other norms can produce tilted images
            'warp0' => $myprefs['warp'],
            'normPoint0_0' => 0,
            'normPoint1_0' => 1,
            'format' => $myprefs['default_imageformat']
        );
        
        $paramsJoined = array();
        foreach($theData as $param => $value) {
           $paramsJoined[] = "$param=$value";
        }
        $query = implode('&', $paramsJoined);
        $ch = curl_init();
        if ($_SERVER['SERVER_NAME'] == 'webmorph.test') {
            // workaround for local server problem with self-signed certificates
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        $data = curl_exec($ch);
        curl_close($ch);
        
        // delete the tmp files
        unlink($cloneName);
        unlink(preg_replace('@\.(jpg|png|gif)$@', '.tem', $cloneName));
        unlink($mirrorName);
        unlink(preg_replace('@\.(jpg|png|gif)$@', '.tem', $mirrorName));
        
        $transdata = json_decode($data, true);
        
        $symimg = $project_id . '/.tmp/' . $transdata[0]['img'];
        $symtem = $project_id . '/.tmp/' . $transdata[0]['tem'];

        $this->_img = new PsychoMorph_Image($symimg); 
        $this->_tem = new PsychoMorph_Tem($symtem);
        
        // replace description with original file description
        $this->_img->_description = $imgdesc;
        $this->_tem->_description = $temdesc;
        
        return $this;
    }
    
    public function addHistory($history) {
        // add an entry to history on the json comments
        $this->_img->addHistory($history);
        $this->_tem->addHistory($history);
        
        return $this;
    }
    
    public function setDescription($v, $v2 = null) {
        $this->_img->setDescription($v, $v2);
        $this->_tem->setDescription($v, $v2);
        
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
    
        $this->_img->setEmbeddedTem($this->_tem->printTem(false));
        //$this->_img->setDescription('tem', $this->_tem->getURL(false));
        if (!$this->_img->save($imgname, $this->_overWrite)) {
            return false;
        }
        
        // add JSON description to tem
        $this->_tem->setDescription('image', $this->_img->getURL(false));

        $this->_tem->save($temname, $this->_overWrite);
        
        return $this;
    }
}

?>
