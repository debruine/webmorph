<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
checkAllocation();

function imgEdit($edit, $data, $img) {
    global $default_rgb;
    
    switch ($edit) {
        case 'align':
            if (!$img->getTem()) {
                return false;
            } else if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,4}(?:\.\d+)?),(\d{1,4}(?:\.\d+)?),(\d{1,4}(?:\.\d+)?),(\d{1,4}(?:\.\d+)?),(\d{1,5}),(\d{1,5})(?:,rgb\((\d{1,3},\d{1,3},\d{1,3})\))?$/i', $data, $align)) {
                $align_pt1 = intval($align[1]);
                $align_pt2 = intval($align[2]);
                $aligned_x1 = $align[3];
                $aligned_y1 = $align[4];
                $aligned_x2 = $align[5];
                $aligned_y2 = $align[6];
                $align_width = $align[7];
                $align_height = $align[8];
                if (count($align) == 10) {
                    $rgb = explode(",", $align[9]);
                } else {
                    $rgb = $default_rgb;
                }
            } else if (preg_match('/^default$/i', $data)) {
                $q = new myQuery("SELECT pref, prefval FROM pref WHERE user_id='{$_SESSION['user_id']}'");
                $myprefs = $q->get_assoc(false, 'pref', 'prefval');
                $align_pt1 = $myprefs['align_pt1'];
                $align_pt2 = $myprefs['align_pt2'];
                $aligned_x1 = $myprefs['align_x1'];
                $aligned_y1 = $myprefs['align_y1'];
                $aligned_x2 = $myprefs['align_x2'];
                $aligned_y2 = $myprefs['align_y2'];
                $align_width = $myprefs['align_w'];
                $align_height = $myprefs['align_h'];
                $rgb = $default_rgb;
            } else if (preg_match('/^frl$/i', $data)) {
                $align_pt1 = 0;
                $align_pt2 = 1;
                $aligned_x1 = 496.98;
                $aligned_y1 = 825.688;
                $aligned_x2 = 853.02;
                $aligned_y2 = 825.688;
                $align_width = 1350;
                $align_height = 1800;
                $rgb = $default_rgb;
            } else {
                return false;
            }
            
            $img->alignEyes($align_width, $align_height,
                            array($aligned_x1, $aligned_y1),
                            array($aligned_x2, $aligned_y2),
                            $align_pt1, $align_pt2, $rgb);
            return true;

        case 'resize':
            $resize = explode(',', $data);
            
            // if only 1 value, must be a %
            if (count($resize) == 1 && preg_match('/^(\d+(?:\.\d+)?)\%$/', $resize[0], $resize1)) {
                $scale = $resize1[1]/100;
                $img->resize($scale, $scale);
                return $scale . '%';
            } else if (count($resize) == 2) {
                
                // if 1st value is null, second must be % or px (keep aspect ratio)
                if ($resize[0] == 'null') {
                    if (preg_match('/^(\d+(?:\.\d+)?)(\%|px)$/i', $resize[1], $resize2)) {
                        if ($resize2[2] == '%') {
                            $scale = $resize2[1]/100;
                            $img->resize($scale, $scale);
                            return $scale . '%';
                        } else if ($resize2[2] == 'px') {
                            $h = $img->getHeight();
                            $scale = $resize2[1]/$h;
                            $img->resize($scale, $scale);
                            return $scale . '%';
                        }
                    }
                    
                // if 2nd value is null, second must be % or px (keep aspect ratio)
                } else if ($resize[1] == 'null') {
                    if (preg_match('/^(\d+(?:\.\d+)?)(\%|px)$/i', $resize[0], $resize1)) {
                        if ($resize1[2] == '%') {
                            $scale = $resize1[1]/100;
                            $img->resize($scale, $scale);
                            return $scale . '%';
                        } else if ($resize1[2] == 'px') {
                            $w = $img->getWidth();
                            $scale = $resize1[1]/$w;
                            $img->resize($scale, $scale);
                            return $scale . '%';
                        }
                    }
                    
                // if neither value is null, must be same vale type (% or px)
                } else if (preg_match('/^(\d+(?:\.\d+)?)(\%|px)$/i', $resize[0], $resize1) 
                        && preg_match('/^(\d+(?:\.\d+)?)(\%|px)$/i', $resize[1], $resize2) 
                        && $resize1[2] == $resize2[2]) {
                    if ($resize1[2] == '%') {
                        $xResize = $resize1[1]/100;
                        $yResize = $resize2[1]/100;
                        $img->resize($xResize, $yResize);
                        return $xResize . '%, ' . $yResize . '%';
                    } else if ($resize1[2] == 'px') {
                        $w = $img->getWidth();
                        $h = $img->getHeight();
                        $xResize = $resize1[1]/$w;
                        $yResize = $resize2[1]/$h;
                        $img->resize($xResize, $yResize);
                        return $xResize . 'px, ' . $yResize . 'px';
                    }
                }
            }
            return false;

        case 'rotate':
            if (preg_match('/^(-?\d{1,3}(?:\.\d+)?)(?:,rgb\((\d{1,3},\d{1,3},\d{1,3})\))?$/i', $data, $rotate)) {
                if (count($rotate) == 3) {
                    $rgb = explode(",", $rotate[2]);
                } else {
                    $rgb = $default_rgb;
                }
        
                $img->rotate($rotate[1], $rgb);
                return true;
            }
            return false;
    
        case 'crop':
            if (preg_match('/^(-?\d+),(-?\d+),(-?\d+),(-?\d+)(?:,rgb\((\d{1,3},\d{1,3},\d{1,3})\))?$/i', $data, $crop)) {
                if (count($crop) == 6) {
                    $rgb = explode(",", $crop[5]);
                } else {
                    $rgb = $default_rgb;
                }
                
                $orig_w = $img->getImg()->getWidth();
                $orig_h = $img->getImg()->getHeight();
                
                $w = $orig_w + $crop[4] + $crop[2];
                $h = $orig_h + $crop[1] + $crop[3];
                $x = -1*$crop[4];
                $y = -1*$crop[1];
        
                $img->crop($x, $y, $w, $h, $rgb);
                return $crop;
            }
            return false;
            
        case 'mask':
            if (preg_match('/^\(([^\(\)]+)\),(\d{1,2})(?:,(?:(transparent)|rgb\((\d{1,3},\d{1,3},\d{1,3})\)))?$/i', $data, $mask)) {
                
                if (count($mask) == 5) {
                    $rgba = explode(",", $mask[4]);
                    array_push($rgba, 1);
                } else if (count($mask) == 4) {
                    $rgba = array(1,244,1,0);
                } else {
                    $rgba = $default_rgb;
                    array_push($rgba, 1);
                }
                
                $blur = $mask[2];

                $custom = null;
                if (preg_match('/^(?:[a-z_]+,)*[a-z_]+$/i', $mask[1])) {
                    $masks = explode(',', $mask[1]);
                    $possible_masks = array("oval", "face", "neck", 
                                            "ears", "eyes", "brows",
                                            "left_ear", "right_ear", 
                                            "left_eye", "right_eye", 
                                            "left_brow", "right_brow", 
                                            "mouth", "teeth", "nose");
                    foreach($masks as $i => $m) {
                        if (!in_array($m, $possible_masks)) {
                            return false;
                        } else if ($m == 'ears') {
                            $masks[$i] = 'left_ear';
                            $masks[] = 'right_ear';
                        } else if ($m == 'eyes') {
                            $masks[$i] = 'left_eye';
                            $masks[] = 'right_eye';
                        } else if ($m == 'brows') {
                            $masks[$i] = 'left_brow';
                            $masks[] = 'right_brow';
                        }
                    }
                } else if (preg_match('/^(((\d+,)*\d+;)*(\d+,)*\d+:)*((\d+,)*\d+;)*(\d+,)*\d+$/', $mask[1])) {
                    $custom = explode(':', $mask[1]);
                    $masks = array('custom');
                    foreach ($custom as $j => $m) {
                        $custom[$j] = explode(';', $m);
                        foreach($custom[$j] as $i => $m2) {
                            $custom[$j][$i] = explode(',', $m2);
                        }
                    }
                } else {
                    return false;
                }

                ini_set('max_execution_time', 30*($blur+1));
                $img->mask($masks, $rgba, $blur, $custom);
                return true;
            }
            
            return false;
            
        case 'sym':
            if ($img->getTem() && preg_match('/^(shape|color|colour)(?:,(shape|color|colour))?$/i', $data)) {
                $symtypes = explode(',', $data);
                $shape = (in_array('shape',$symtypes)) ? 0.5 : 0;
                $colortex = (in_array('color',$symtypes) || in_array('colour',$symtypes)) ? 0.5 : 0;
                $img->sym($shape, $colortex);
                return true;
            }
            return false;
            
        case 'mirror':
            if (preg_match('/^(true|t|1)$/i', $data)) {
                $img->mirror();
                return true;
            }
            return false;
    }
}


$return = array(
    'error' => true,
    'errorText' => '',
    'newFileName' => ''
);

$image = safeFileName($_POST['theData']['image']);

if ($image) {
    ini_set('memory_limit','512M');
    
    $default_rgb = array(255,255,255);
    $q = new myQuery("SELECT prefval FROM pref WHERE user_id='{$_SESSION['user_id']}' AND pref='mask_color'");
    if ($q->get_num_rows() == 1
        && preg_match('/rgb\((\d{1,3}),(\d{1,3}),(\d{1,3})\)$/i', $q->get_one(), $rgb)) {
        $default_rgb = array_slice($rgb, 1);
    }
    
    include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

    if (array_key_exists('hasTem', $_POST['theData'])) {
        $img = new PsychoMorph_ImageTem($image);
    } else {
        $img = new PsychoMorph_Image($image);
    }
        
    // set order
    $possible_edits = array('align', 'resize', 'rotate', 'crop', 'mask', 'sym', 'mirror');
    if ($_POST['theData']['order'] == '' || $_POST['theData']['order'] == 'false') {
        $edits = $possible_edits;
    } else {
        $edits = explode(',', $_POST['theData']['order']);
    }
    
    // run each edit in order
    foreach($edits as $edit) {
        if (in_array($edit, $possible_edits)) {
            $return[$edit] = imgEdit($edit, $_POST['theData'][$edit], $img);
        } else {
            $return['errorText'] .= "'{$edit}' is not a valid edit. ";
        }
    }

    // save image
    if (array_key_exists('outname', $_POST)) {
        $newFileName = safeFileName($_POST['outname']);
    }
    
    $img->setOverWrite(false);
    if ($img->save($newFileName)) {
        $return['error'] = false;
        $return['newFileName'] = $img->getURL();
    } else {
        $return['errorText'] .= 'The image was not saved. ';
    }
} else {
    $return['errorText'] .= 'The image to edit was not found.';
}

scriptReturn($return);

exit;

?>