<?php

/***************************************************/
/* !Functions and code for every page */
/***************************************************/
	/*if ($_SERVER['SERVER_NAME'] == 'psychomorph.facelab.org') {
		header('Location: http://webmorph.org');
		die();
	}*/

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
			echo json_encode(array(
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
			echo json_encode($return, JSON_NUMERIC_CHECK);
		} else {
			header('Content-Type: text/html');
			echo htmlArray($return);
		}
		
		if ($buffer) {
			$size = ob_get_length();
			header("Content-Length: $size");
			ob_end_flush(); 	// Strange behaviour, will not work
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

	function safeFileName($filename) {
		//$filename = strtolower($filename);
		$filename = str_replace(array("#", " ", "__"),"_",$filename);
		$filename = str_replace(array("'", '"', "\\", "?"),"",$filename);
		$filename = str_replace("//","/",$filename);
		//$filename = str_replace("/","_",$filename);
		return $filename;
	}
	
	function cleanTags($tags) {
		$tagArray = (is_array($tags)) ? $tags : explode(';', $tags);
		$tagArray = array_filter($tagArray, strlen);					// get rid of blank tags
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
	
	function htmlArray($array) {
		echo '<dl>' . ENDLINE;
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				
				if (is_numeric($k)) { $k = ''; }
				echo '<dt>' . $k . '</dt><dd>';
				if (is_array($v)) {
					echo "<br>";
					htmlArray($v);
				} else {
					echo $v;
				}
				echo '</dd>'. ENDLINE;
			}
		} else {
			echo $array;
		}
		echo '</dl>' . ENDLINE;
	}
	
	// parse paragraphs for html display
	function parsePara($p) {
		$ul = false;
		$return = "";
		
		$split = preg_split('/[\n\r]{3,}/', $p);
		
		foreach($split as $subp) { 
			if ("<"==substr($subp,0,1)) {
				if ($ul)  { $ul = false; $return .= "</ul>" . ENDLINE; }
				$return .= loc($subp) . ENDLINE;
			} elseif ("*"==substr($subp,0,1)) {
				if (!$ul)  { $ul = true; $return .= "<ul>" . ENDLINE; }
				
				$subsplit = preg_split('/[\n\r]{1,}/', $subp);
				foreach($subsplit as $subsubp) {
					if ("*"==substr($subsubp,0,1)) {
						$return .= "	<li class='new'>" . loc(substr($subsubp,1)) . "</li>" . ENDLINE;
					} else {
						$return .= "	<li>" . loc($subsubp) . "</li>" . ENDLINE;
					}
				}
			} else { 
				if ($ul)  { $ul = false; $return .= "</ul>" . ENDLINE; }
				$return .= "<p>" . loc($subp) . "</p>" . ENDLINE;
			}
		}
		if ($ul)  { $ul = false; $return .= "</ul>" . ENDLINE; }
		
		return $return;
	}
	
/***************************************************/
/* !Image Functions */
/***************************************************/
	
	// extract the commmon path for an array of paths
	function common_path($imagelist) {
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
				$taglist[] = "	<li style='font-size: $size%;'><a href='javascript:showTags(\"$tag\");' title='$n images'>" 
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
        } else {
            return true;
        }
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
		if ($back) echo "	<li class='back'><a href='$backurl'>$back</a></li>\n";
		if ($home) echo "	<li class='home'><a href='$homeurl'>$home</a></li>\n";	
		if ($next) echo "	<li class='next'><a href='$nexturl'>$next</a></li>\n";
		echo "</ul>\n\n";
	}
	
	// display a citation
	function apaCite($tags, $authors, $title, $year, $journal, $volume="", $issue="", $pages="") {
		// authors
		$citation = "		<span class='authors'>";
		if (is_array($authors)) {
			$lastauthor = array_pop($authors);
			$citation .= implode(", ", $authors);
			$citation .= " &amp; $lastauthor";
		} else {
			$citation .= $authors;
		}
		$citation .= "</span>\n";
		
		// year
		$citation .= "		<span class='year'>($year).</span>\n";
		
		// title
		$punctuation = array(".", "!", "?");
		if (!in_array(substr($title, -1), $punctuation)) $title .= ".";  
		$citation .= "		<span class='title'>$title</span>\n";
		
		// journal
		if (!$volume) {
			$journal .= ".";
		} else {
			$journal .= ",";
		}
		$citation .= "		<span class='journal'>$journal</span>\n";
		
		// volume, issue and pages
		if ($volume) {
			if (!$issue && $pages) $volume .= ":";
			$citation .= "		<span class='volume'>$volume</span>\n";
			if ($issue) {
				if ($pages) $issue .= ":";
				$citation .= "		<span class='issue'>($issue)</span>\n";
			}
			if ($pages) {
				$citation .= "		<span class='pages'>$pages.</span>\n";
			} else {
				$citation .= ".";
			}
		}
		
		if ($tags) $citation  = "	<$tags>\n$citation\n	</$tags>\n";
		
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