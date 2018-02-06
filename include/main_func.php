<?php

/***************************************************/
/* !Functions and code for every page */
/***************************************************/
    if ($_SERVER['SERVER_NAME'] == 'psychomorph.facelab.org' || $_SERVER['SERVER_NAME'] == 'psychomorph.psy.gla.ac.uk') {
        header('Location: http://webmorph.org');
        die();
    }

    $initime=microtime(true);

    // set php environment variables and start the session
    #session_save_path("/tmp/php_session");
    session_start();
    ini_set("arg_separator.output", "&amp;");
    define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
    
    // get user-defined variables and main classes
    require_once DOC_ROOT.'/include/config.php';
    require_once DOC_ROOT.'/include/classes/mysqli.class.php';
    
    // check if user is logged in
    $loggedin = ($_COOKIE['user_id'] != '' && md5($_COOKIE['user_id']) == $_COOKIE['id_hash']);
    define('LOGGEDIN', $loggedin);
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    
    // send to return_location if status is not high enough for this page
    function auth($error = 'You need to be logged in to do this.') {
        if (!LOGGEDIN) {
            scriptReturn(array(
                'error' => true,
                'errorText' => $error
            ));
            exit();
        }
    }
    
    // send to return_location if guest is not high enough for this page
    function noguest($error = 'Guest accounts cannot do this.') {
        $q = new myQuery("SELECT status FROM user WHERE id='{$_SESSION[user_id]}'");
        if ($q->get_one() == 'guest') {
            scriptReturn(array(
                'error' => true,
                'errorText' => $error
            ));
            exit();
        }
    }
    
    // check permission
    function perm($perm) {
        $valid_perms = array("pca");
        
        if (!in_array($perm, $valid_perms)) { return FALSE; }
        
        $q = new myQuery("SELECT $perm FROM user WHERE id='{$_SESSION[user_id]}'");
        $permval = $q->get_one(); 
        
        return ($permval == 1);
    }
    
    // check project permissions
    function projectPerm($id) {
        $q = new myQuery("SELECT 1 FROM project_user WHERE project_id='{$id}' AND user_id='{$_SESSION[user_id]}'");

        return ($q->get_num_rows() == 1);
    }
    
    // finish a script, return values as json (or html), optionally close out the buffer
    function scriptReturn($return, $buffer = false, $json = true) {
        global $initime;
        
        if ($buffer) {
            // start and end user output so this can happen without the user waiting
            ob_end_clean();
            header("Connection: close");
            ignore_user_abort(); // optional
            ob_start();
        }
        
        if ($json) {
            $return['memory_usage'] = formatBytes(memory_get_usage());
            $return['memory_peak_usage'] = formatBytes(memory_get_peak_usage());
            $return['script_run_time'] = round((microtime(true)-$initime)*1000);
            
            header('Content-Type: application/json');
            echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        } else {
            header('Content-Type: text/html');
            echo htmlArray($return);
        }
        
        if ($buffer) {
            $size = ob_get_length();
            header("Content-Length: $size");
            ob_end_flush();     // Strange behaviour, will not work
            flush();            // Unless both are called !
        }
    }
    
/***************************************************/
/* !Variable Cleaning Functions */
/***************************************************/

    // check that a file path is actually a subpath of a given directory, defaults to IMAGEBASEDIR
    function underPath($pathtocheck, $dir = IMAGEBASEDIR) {
        $dir = realpath($dir);
        $rpath = realpath($pathtocheck);
        
        if (is_dir($dir) && substr($rpath, 0, strlen($dir)) != $dir) {
            return false;
        } else {
            return $rpath;
        }
    }
    
    function cleanData($unclean_array, $var, $valid_values, $default = '') {
        if (isset($unclean_array[$var]) && in_array($unclean_array[$var], $valid_values)) return $unclean_array[$var];
        return $default;
    }
    
    // check if ID is valid (i.e. a positive integer)
    function validID($id) {
        if (empty($id)) return false;
        if (!is_numeric($id)) return false;
        if ($id<1) return false;
        $x = trim($id, '0123456789');
        if (empty($x)) return true; 
        return false;
    }
    
    // check if a variable is of a certain type and (optionally) in an array of possible values, else return NULL for mySQL
    function check_null($var, $format = array()) {
        if (is_array($format)) {
            if (in_array($var, $format)) { return $var; }
        } else if ('numeric' == $format) {
            if (is_numeric($var)) { return $var; }
        } else if ('id' == $format) {
            if (validID($var)) { return $var; }
        } else if ('integer' == $format) {
            if (is_integer($var)) { return $var; }
        }
        
        return 'null';
    }


/***************************************************/
/* !Text Functions */
/***************************************************/

    function serializeForTomcat($theData) {
        $paramsJoined = array();
        foreach($theData as $param => $value) {
            if (is_array($value)) {
                foreach($value as $subvalue) {
                    $paramsJoined[] = "$param=$subvalue";
                }
            } else {
                $paramsJoined[] = "$param=$value";
            }
        }
        $query = implode('&', $paramsJoined);
        
        return($query);
    }

    function safeFileName($filename) {
        //$filename = strtolower($filename);
        $filename = preg_replace('@\.+@', ".",$filename);
        $filename = str_replace(array("#", " ", "__"),"_",$filename);
        $filename = str_replace(array("'", '"', "\\", "?"),"",$filename);
        $filename = preg_replace('@/+@',"/",$filename);
        //$filename = str_replace("/","_",$filename);
        return $filename;
    }
    
    function cleanTags($tags) {
        $tagArray = (is_array($tags)) ? $tags : explode(';', $tags);
        $tagArray = array_filter($tagArray, strlen);                    // get rid of blank tags
        foreach ($tagArray as $i => $t) {
            $t = trim($t);
            $t = str_replace(array('"', "'", "\\"), '', $t);
            $t = str_replace(' ', '_', $t);
            $t = my_clean($t);
        
            $tagArray[$i] = $t;
        }
        
        return $tagArray;
    }
    
    // set a variable to something if it is empty (not set, or equal to 0, false or '')
    function ifEmpty(&$var, $value='', $strict=false) {
        if (empty($var) && (!$strict || ($var!==0 && $var !=='0' && $var !== FALSE))) { 
            $var = $value;
            return $value;
        } else {
            return $var;
        }
    }
    
    function htmlArrayOld($array) {
        $return = '<dl>' . PHP_EOL;
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                
                if (is_numeric($k)) { $k = ''; }
                $return .= '<dt>' . $k . '</dt><dd>';
                if (is_array($v)) {
                    $return .= "<br>";
                    $return .= htmlArray($v);
                } else {
                    $return .= $v;
                }
                $return .= '</dd>'. PHP_EOL;
            }
        } else {
            $return .= $array;
        }
        $return .= '</dl>' . PHP_EOL;
        
        return($return);
    }
    
    function htmlArray($array, $table = true) {
        $return = '';
        
        if (is_array($array)) {
            if ($table) {
                $return .= '<table>' . PHP_EOL;
            } else {
                if (array_key_exists('label', $array)) {
                    $return .= $array['label'];
                    unset($array['label']);
                }
                $return .= '</td><tr>' . PHP_EOL;
            }
            foreach ($array as $k => $v) {
                
                //if (is_numeric($k)) { $k = ''; }
                $return .= '<tr><td>' . $k . '</td><td>';
                $return .= htmlArray($v, false);
                $return .= '</td></tr>'. PHP_EOL;
            }
            if ($table) {
                $return .= '</table>' . PHP_EOL;
            } else {
                $return .= '<tr><td></td><td>';
            }
        } else {
            $return .= $array;
        }
        
        return($return);
    }
    
    // parse paragraphs for html display
    function parsePara($p) {
        $ul = false;
        $return = "";
        
        $split = preg_split('/[\n\r]{3,}/', $p);
        
        foreach($split as $subp) { 
            if ("<"==substr($subp,0,1)) {
                if ($ul)  { $ul = false; $return .= "</ul>" . PHP_EOL; }
                $return .= loc($subp) . PHP_EOL;
            } elseif ("*"==substr($subp,0,1)) {
                if (!$ul)  { $ul = true; $return .= "<ul>" . PHP_EOL; }
                
                $subsplit = preg_split('/[\n\r]{1,}/', $subp);
                foreach($subsplit as $subsubp) {
                    if ("*"==substr($subsubp,0,1)) {
                        $return .= "    <li class='new'>" . loc(substr($subsubp,1)) . "</li>" . PHP_EOL;
                    } else {
                        $return .= "    <li>" . loc($subsubp) . "</li>" . PHP_EOL;
                    }
                }
            } else { 
                if ($ul)  { $ul = false; $return .= "</ul>" . PHP_EOL; }
                $return .= "<p>" . loc($subp) . "</p>" . PHP_EOL;
            }
        }
        if ($ul)  { $ul = false; $return .= "</ul>" . PHP_EOL; }
        
        return $return;
    }
    
/***************************************************/
/* !Image Functions */
/***************************************************/
    
    // extract the commmon path for an array of paths
    function commonPath($imagelist) {
        $paths = explode('/', $imagelist[0]);
        $common_path = array();
        foreach ($paths as $n => $p) {
            if (!empty($p)) {
                $search_path = implode('/', array_slice($paths, 0, $n+1));
                $check = true;
                foreach($imagelist as $i) {
                    if (strpos($i, $search_path) !== 0) $check = false;
                }
                if ($check) $common_path[] = $p;
            }
        }
        return '/' . implode('/', $common_path) . '/';
    }

/***************************************************/
/* !Statistical Functions */
/***************************************************/
 
    function apa_round($x) {
        // round a value to APA-style number of significant digits
        $y = abs($x);
        
        if ($y>100) return round($x, 0);
        if ($y>10) return round($x, 1);
        if ($y>0.1) return round($x, 2);
        if ($y>0.001) return round($x, 3);
        return $x;
    }
    
/***************************************************/
/* !Other Functions */
/***************************************************/

    function recursive_delete($dir) {
        global $return;
        
        // only delete directories in the images path
        if (strpos($dir, IMAGEBASEDIR) === 0) {
            $dir = str_replace(IMAGEBASEDIR, '', $dir); 
        }
        
        $deletedir = realpath(IMAGEBASEDIR . $dir);
        
        if (strpos($deletedir, IMAGEBASEDIR) !== 0) {
            $return['delete']['error'][] = $dir . ' is not in the image directory';
        } else if (is_dir($deletedir)) {
            $handle = opendir($deletedir);
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $ext = pathinfo($entry, PATHINFO_EXTENSION);
                    $path = $deletedir . '/' . $entry;
                    if (!is_dir($path)) {
                        unlink($path);
                        $return['delete']['files'][] = $path;
                    } else {
                        recursive_delete($path);
                    }
                }
            }
            closedir($handle);
            rmdir($deletedir);
            $return['delete']['dirs'][] = $deletedir;
        } else {
            $return['delete']['error'] = $dir . ' does not exist';
        }
    }

    // add default images to a project
    function addProjImages($proj_id, $userid = 0) {
        if ($userid == 0) { $userid = $_SESSION['user_id']; }
        $mydir = IMAGEBASEDIR . $proj_id;
        
        mkdir($mydir . '/.tmp', DIRPERMS);
        mkdir($mydir . '/.trash', DIRPERMS);
        
        mkdir($mydir . '/composites', DIRPERMS);
        $faces = array(
            "f_african",
            "f_easian",
            "f_multi",
            "f_wasian",
            "f_white",
            "m_african",
            "m_easian",
            "m_multi",
            "m_wasian",
            "m_white",
        );
        
        foreach ($faces as $face) {
            copy(DOC_ROOT . "/include/examples/{$face}.jpg", "{$mydir}/composites/{$face}.jpg");
            copy(DOC_ROOT . "/include/examples/{$face}.tem", "{$mydir}/composites/{$face}.tem");
        }
        
        mkdir($mydir . '/3d', DIRPERMS);
        $d3s = array(
            "average_easian_female", 
            "average_easian_male", 
            "average_white_female", 
            "average_white_male"
        );
        
        foreach ($d3s as $d3) {
            copy(DOC_ROOT . "/include/3d/{$d3}.jpg", "{$mydir}/3d/{$d3}.jpg");
            copy(DOC_ROOT . "/include/3d/{$d3}.obj", "{$mydir}/3d/{$d3}.obj");
        }

        // add templates
        mkdir($mydir . '/templates', DIRPERMS);
        $templates = array(
            "batchAvg",
            "batchTrans",
            "batchEdit"
        );
        foreach ($templates as $template) {
            copy(DOC_ROOT . '/include/examples/webmorph_template_{$template}.txt', 
                 $mydir . '/templates/_{$template}_template.txt');
        }
    }
     
     // make a list of tag words sized according to frequency
     // tags = array of word => frequency pairs
    function tagList($tags, $minsize = 50) {
        if (!empty($tags)) {
            ksort($tags);
        
            $maxtag = max($tags);
            $mintag = min($tags);
            $taglist = array();
        
            foreach($tags as $tag => $n) {
                $size = ($maxtag == $mintag) ? $minsize : $minsize + ( (100-$minsize) * (($n-$mintag) / ($maxtag-$mintag)) );
                $taglist[] = "    <li style='font-size: $size%;'><a href='javascript:showTags(\"$tag\");' title='$n images'>" 
                    . str_replace(" ", "&nbsp;", $tag) . "</a>\n";
            }
            return $taglist;
        } else {
            return array();
        }
    }
    
    function formatBytes($bytes, $precision = 1) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow)); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 
    
    function userAllocation($user_id) {
        $q = new myQuery("SELECT project.id as id, allocation 
                          FROM project 
                          LEFT JOIN user ON user.id=user_id 
                          WHERE user_id='$user_id'");
        $projects = $q->get_one_col('id');
        $allocation = $q->get_one(0, 'allocation');

        $size = 0;
        foreach($projects as $id) {
            $size += exec('du -sm ' . IMAGEBASEDIR . $id);
        }
        
        return array(
            'size' => $size,
            'allocation' => $allocation
        );
    }
    
    function checkAllocation() {
        // check permissions for this project
        $q = new myQuery("SELECT perm
                          FROM project_user
                          WHERE user_id='{$_SESSION['user_id']}'
                            AND project_id='{$_SESSION['project_id']}'");
        $perm = $q->get_one();
        if ($perm !== 'all') {
            $return = array(
                "error" => true,
                "errorText" => "You do not have permission to save files to this project."
            );
            scriptReturn($return);
            exit;
        }

        // check overall allocation
        $ua = userAllocation($_SESSION['user_id']);

        if ($ua['size'] > $ua['allocation']) {
            $return = array(
                "error" => true,
                "size" => $ua['size'],
                "allocation" => $ua['allocation'],
                "errorText" => "You have exceeded your allocation of " . round($ua['allocation']/1024,1) . " GB"
            );
            scriptReturn($return);
            exit;
        }
        
        // return true if all fine
        return true;
    }
    
    // update filemtime for entire project
    function updateDirMod() {
        touch(IMAGEBASEDIR . $_SESSION['project_id']);
    }
    
    // rotate an array
    function rotate_array($array) {
        $rotated_array = array();
        foreach ($array as $row => $a) {
            foreach ($a as $header => $value) {
                $rotated_array[$header][0] = $header;
                $rotated_array[$header][$row+1] = $value;
            }
        }
        return $rotated_array;
    }
    
    // display a navigation bar for ordered content
    function navBar($back, $backurl, $home, $homeurl, $next, $nexturl) {
        echo "<ul class='navBar'>\n";
        if ($back) echo "    <li class='back'><a href='$backurl'>$back</a></li>\n";
        if ($home) echo "    <li class='home'><a href='$homeurl'>$home</a></li>\n";    
        if ($next) echo "    <li class='next'><a href='$nexturl'>$next</a></li>\n";
        echo "</ul>\n\n";
    }
    
    // display a citation
    function apaCite($tags, $authors, $title, $year, $journal, $volume="", $issue="", $pages="") {
        // authors
        $citation = "        <span class='authors'>";
        if (is_array($authors)) {
            $lastauthor = array_pop($authors);
            $citation .= implode(", ", $authors);
            $citation .= " &amp; $lastauthor";
        } else {
            $citation .= $authors;
        }
        $citation .= "</span>\n";
        
        // year
        $citation .= "        <span class='year'>($year).</span>\n";
        
        // title
        $punctuation = array(".", "!", "?");
        if (!in_array(substr($title, -1), $punctuation)) $title .= ".";  
        $citation .= "        <span class='title'>$title</span>\n";
        
        // journal
        if (!$volume) {
            $journal .= ".";
        } else {
            $journal .= ",";
        }
        $citation .= "        <span class='journal'>$journal</span>\n";
        
        // volume, issue and pages
        if ($volume) {
            if (!$issue && $pages) $volume .= ":";
            $citation .= "        <span class='volume'>$volume</span>\n";
            if ($issue) {
                if ($pages) $issue .= ":";
                $citation .= "        <span class='issue'>($issue)</span>\n";
            }
            if ($pages) {
                $citation .= "        <span class='pages'>$pages.</span>\n";
            } else {
                $citation .= ".";
            }
        }
        
        if ($tags) $citation  = "    <$tags>\n$citation\n    </$tags>\n";
        
        return $citation;
    }
    
    function duplicateTable($table, $type, $old_id, $new_id) {
        
        $q = new myQuery("SELECT * FROM $table WHERE {$type}_id={$old_id}");
        $old_data = $q->get_assoc();
        if (count($old_data) > 0) {
            unset($old_data[0]["{$type}_id"]);
            $fields = array_keys($old_data[0]);
            $query = sprintf("INSERT INTO {$table} ({$type}_id, %s) SELECT {$new_id}, %s FROM {$table} WHERE {$type}_id={$old_id}",
                implode(", ", $fields),
                implode(", ", $fields)
            );
            $q = new myQuery($query);
        }
        
        return $q->get_affected_rows();
    }
    
    function check_file_duplicate($f, $n = 0) {
        if (file_exists($f)) {
            // file already exists, so save as a copy
            $path = pathinfo($f);
            if ($n > 0) {
                $newfile = $path['dirname'] . '/' 
                         . preg_replace("/_copy\d*$/", "_copy" 
                         . $n, $path['filename']) 
                         . ($path['extension']=='' ? '' : '.') 
                         . $path['extension'];
            } else {
                $newfile = $path['dirname'] . '/' 
                         . $path['filename'] . '_copy' 
                         . ($path['extension']=='' ? '' : '.') 
                         . $path['extension'];
            }
            $n++;
            return check_file_duplicate($newfile, $n);
        } else {
            return $f;
        }
    }

?>