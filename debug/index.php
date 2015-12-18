<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

/****************************************************/
/* !Display Page */
/***************************************************/

if ($_SESSION['user_id'] !== 1) { 
	//header('Location: /');
	//exit;
}

?><!DOCTYPE html>

<html xmlns:fb='http://www.facebook.com/2008/fbml' lang='en'>
<head>
	<title>Psychomorph Online Debug</title>
	<meta charset='utf-8'>
	<meta name='author' content='Lisa DeBruine and Bernard Tiddeman' />
	<meta name='description' content='Online tools for manipulating faces' />
	<meta name='keywords' content='face research,faces,psychology,research,computer graphics,psychomorph' />
	<!--<meta name='verify-v1' content='oCEvWF1olBQ+/+nyyAZfRnSeVVGeEVlD0Qw8aHTRvAU=' />-->
	<meta property='og:site_name' content='Psychomorph'/>
	<meta property='og:image' content='/include/images/logo.png'/>
	<meta name="viewport" id="vp" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />
	<meta name='apple-mobile-web-app-capable' content='yes' />
	<link rel='apple-touch-startup-image' href='/include/images/logo.png' />
	<link rel='apple-touch-startup-image' sizes='640x920' href='/include/images/logo@2x.png' />
	<meta name='apple-mobile-web-app-status-bar-style' content='black' />
	<link rel='shortcut icon' href='/include/images/favicon.ico' />
	<link rel='apple-touch-icon-precomposed' href='/include/images/apple-touch-icon-precomposed.png' />
	<link rel='stylesheet' type='text/css' href='/include/css/style.php'>
	<link rel='stylesheet' type='text/css' href='<?= JQUERYUI_THEME ?>'>
</head>

<style>
</style>

<!-- START BODY -->

<body>
    
    <div class="rainbow-loader">
		<div><div></div></div>
		<div><div></div></div>
		<div><div></div></div>
		<div><div></div></div>
		<div><div></div></div>
		<div><div></div></div>
	</div>
	
	
	<!--<div class="spinner"></div>-->
	
	<div class="rainbow-spin">
	  <div>
	    <div>
	      <div>
	        <div>
	          <div>
	            <div>
	              <div>
	              </div>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	
	<!--<img src="test">-->

<?php 
	
echo "<h3>Last PHP Error</h3>";
htmlArray( debug_backtrace() );
htmlArray( error_get_last() );
	
// session Variables
$_SESSION['debug'] = true;
echo "<h3>\$_SESSION Variables</h3>\n";
htmlArray($_SESSION);

$php_time = date('Y-m-d H:i:s');
$q = new myQuery('SELECT NOW()');
$mysql_time = $q->get_one();

echo "<h3>Time Comparison</h3>
<ul>
	<li>MySQL time: $mysql_time</li>
	<li>PHP time: $php_time</li>
</ul>";
	
echo "<h3>MySQLi Test</h3>";

$q = new myQuery("SELECT id, firstname FROM user LIMIT 3");
echo '<table><thead><tr><th></th><th>Answer</th><th>True Value</th></thead></tr><tbody>';
echo '<tr><td>Query</td><td>' . $q->get_query() . '</td><td>SELECT id, firstname FROM user LIMIT 3</td></tr>';
echo '<tr><td>N Rows</td><td>' . $q->get_num_rows() . '</td><td>3</td></tr>';
echo '<tr><td>get_row()</td><td>';
print_r($q->get_row());
echo '</td><td>Array ( [id] => 1 [firstname] => Lisa )</td></tr>';
echo '<tr><td>get_row(2)</td><td>';
print_r($q->get_row(2));
echo '</td><td>Array ( [id] => 3 [firstname] => Amanda )</td></tr>';
echo '<tr><td>get_col("id")</td><td>';
print_r($q->get_col('id'));
echo '</td><td>Array ( [0] => 1 [1] => 2 [2] => 3 )</td></tr>';
echo '<tr><td>get_one()</td><td>' . $q->get_one() .  '</td><td>1</td></tr>';
echo '<tr><td>get_one(2, "firstname")</td><td>' . $q->get_one(2, 'firstname') .  '</td><td>Amanda</td></tr>';
echo '<tr><td>get_one(2, "error")</td><td>' . $q->get_one(2, 'error') .  '</td><td>Column <code>error</code> does not exist in row <code>2</code></td></tr>';
echo '<tr><td>get_assoc()</td><td>';
print_r($q->get_assoc());
echo '</td><td>Array ( [0] => Array ( [id] => 1 [firstname] => Lisa ) [1] => Array ( [id] => 2 [firstname] => Ben ) [2] => Array ( [id] => 3 [firstname] => Amanda ) )</td></tr>';
echo '<tr><td>get_result_as_table()</td><td>' . $q->get_result_as_table() . '</td><td>
<table class="query">
<thead><tr>	<th>id</th><th>firstname</th></tr></thead><tbody>
<tr><td>1</td> <td>Lisa</td></tr>
<tr><td>2</td><td>Ben</td></tr>
<tr><td>3</td><td>Amanda</td></tr>
</tbody></table></td></tr>';

echo '<tr><td>prepare()</td><td>';
$query = "SELECT id, email FROM user WHERE firstname!=? AND lastname!=?";
$params = array("ss", "Lisa", "DeBruine");
$return = array("id", "email");
$data = $q->prepare($query, $params, $return);

echo "{$data['id']}: {$data['email']}";
echo '</td><td>1: lisa.debruine@glasgow.ac.uk</td></tr>';
echo '</tbody></table>';




//include_once DOC_ROOT . '/include/classes/psychomorph.class.php';

/*
$img = new PsychoMorph_ImageTem('/monkey_female');
$img->getImg()->setDescription('My new image');
echo 'desc: ' . $img->getImg()->getDescription();
echo '<br />imgpath: ' . $img->getImg()->getUserPath();
echo '<br />tempath: ' . $img->getTem()->getUserPath();
echo '<br />n: ' . $img->getTem()->getPointNumber();
$img->alignEyes();
$img->setOverWrite(true);
$img->save('/a_monkey.jpg');
echo '<br />imgpath: ' . $img->getImg()->getPath();
echo '<br />tempath: ' . $img->getTem()->getPath();
echo '<br />width: ' . $img->getImg()->getWidth() . ' height: '. $img->getImg()->getHeight();
echo '<pre>' .  $img->getTem()->printTem() . '</pre>';
*/


echo "<h3>Users</h3>";

$q = new myQuery(array('CREATE TEMPORARY TABLE tmp_users 
				SELECT user.id, 
				    IF((lastname = "" OR lastname IS NULL), email, CONCAT(lastname, ", ", firstname)) as user, 
					MAX(logintime) as last_login,
					COUNT(*) as logins
				FROM user 
				LEFT JOIN login ON login.user_id=user.id 
				GROUP BY user.id', 'SELECT tmp_users.*, COUNT(*) as images
				FROM tmp_users
				LEFT JOIN img ON tmp_users.id=img.user_id
				GROUP BY tmp_users.id
				ORDER BY last_login DESC;'));
echo '<div id="usertable">' . $q->get_result_as_table() . '</div>';

echo "<h3>My Image Base Dir</h3>";

class VisibleOnlyFilter extends RecursiveFilterIterator
{
    public function accept()
    {
        $fileName = $this->getInnerIterator()->current()->getFileName();
        $firstChar = $fileName[0];
        return $firstChar !== '.';
    }
}

class FilesOnlyFilter extends RecursiveFilterIterator
{
    public function accept()
    {
        $iterator = $this->getInnerIterator();

        // allow traversal
        if ($iterator->hasChildren()) {
            return true;
        }

        // filter entries, only allow true files
        return $iterator->current()->isFile();
    }
}

$fileinfos = new RecursiveIteratorIterator(
    new FilesOnlyFilter(
        new VisibleOnlyFilter(
            new RecursiveDirectoryIterator(
                IMAGEBASEDIR,
                FilesystemIterator::SKIP_DOTS
                    | FilesystemIterator::UNIX_PATHS
            )
        )
    ),
    RecursiveIteratorIterator::LEAVES_ONLY,
    RecursiveIteratorIterator::CATCH_GET_CHILD
);

foreach ($fileinfos as $pathname => $fileinfo) {
    //echo $fileinfos->getSubPathname(), "<br>";
}

/*
echo "<h3>My Tmp Dir</h3>";

$tmpdir = IMAGEBASEDIR . '/.tmp';
if (is_dir($tmpdir)) {
	//chmod($tmpdir, 0777);

	$perms = fileperms($tmpdir);

	if (($perms & 0xC000) == 0xC000) {
	    // Socket
	    $info = 's';
	} elseif (($perms & 0xA000) == 0xA000) {
	    // Symbolic Link
	    $info = 'l';
	} elseif (($perms & 0x8000) == 0x8000) {
	    // Regular
	    $info = '-';
	} elseif (($perms & 0x6000) == 0x6000) {
	    // Block special
	    $info = 'b';
	} elseif (($perms & 0x4000) == 0x4000) {
	    // Directory
	    $info = 'd';
	} elseif (($perms & 0x2000) == 0x2000) {
	    // Character special
	    $info = 'c';
	} elseif (($perms & 0x1000) == 0x1000) {
	    // FIFO pipe
	    $info = 'p';
	} else {
	    // Unknown
	    $info = 'u';
	}
	
	// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
	            (($perms & 0x0800) ? 's' : 'x' ) :
	            (($perms & 0x0800) ? 'S' : '-'));
	
	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
	            (($perms & 0x0400) ? 's' : 'x' ) :
	            (($perms & 0x0400) ? 'S' : '-'));
	
	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
	            (($perms & 0x0200) ? 't' : 'x' ) :
	            (($perms & 0x0200) ? 'T' : '-'));


	echo "$tmpdir exists and has permissions $info";
	$handle = opendir($tmpdir);
	echo "<ul>";
	while (false !== ($entry = readdir($handle))) {
	    if ($entry != "." && $entry != "..") {
	    	echo "	<li>$entry</li>\n";
	    }
	}
	echo "</ul>";
	closedir($handle);
} else {
	echo "$tmpdir does not exist";
}
*/


echo '<h3>Apache/PHP Limits</h3>';

$ini_all = ini_get_all();

$vars = array(
	'memory_limit' => 'Memory Limit',
	'max_execution_time' => 'Max Execution Time',
	'max_input_time' => 'Max Input Time',
	'max_input_vars' => 'Max Input Variables',
	'upload_max_filesize' => 'Upload Max Size',
	'post_max_size' => 'Post Max Size',
);

echo '<ul>';

foreach ($vars as $v => $label) {
	echo '<li>' . $label . ': ' . $ini_all[$v]['local_value'] . ' (global: ' . $ini_all[$v]['global_value'] . ', access: ' . $ini_all[$v]['access'] . ') </li>';
}

echo '<li>PHP Memory Allocated: ' .  formatBytes(memory_get_usage()) . ', peak: ' . formatBytes(memory_get_peak_usage()) . '</li>';
echo '</ul>';

// check Matrix functions service
echo '<h3>Checking Matrix Functions</h3>';

try {
	include_once "Math/Matrix.php";
	
	$xorig = array(0 => 165.659, 1 => 284.339, 96 => 225.359);
	$yorig = array(0 => 275.234, 1 => 275.231, 96 => 407.61);
	
	$original = array(
        array($xorig[0], $yorig[0], 1.0),
        array($xorig[1], $yorig[1], 1.0),
        array($xorig[96], $yorig[96], 1.0,),
    );
	$m = new Math_Matrix($original);
	
	$xnew = array(170.0, 280.0, 222.0);
	$ynew = array(270.0, 270.0, 402.0);
	
	// identical to original, test to make sure
	// a = 1, b = 0, c = 0, d = 0, e = 1, f = 0
	//$xnew = array(165.659, 284.339, 225.359);  
	//$ynew = array(275.234, 275.231, 407.61);
	
	$xvector = new Math_Vector($xnew);
	$yvector = new Math_Vector($ynew);


	echo "<pre>Original Template Points\n";
	echo "x1 = 165.659, x2 = 284.339, x3 = 225.359\n";
	echo "y1 = 275.234, y2 = 275.231, y3 = 407.61\n";
	echo "\nNew 3-Point Delineation\n";
	echo "X1 = $xnew[0],     X2 = $xnew[1],     X3 = $xnew[2]\n";
	echo "Y1 = $ynew[0],     Y2 = $ynew[1],     Y3 = $ynew[2]\n\n";

	//echo "Matrix of original positions\n";
	//echo $m->toString()."\n";
	
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
	
	echo "Calculated Solution (correct answers in parentheses)\na =  $a ( 0.927)\nb = $b (-0.025)\nc = $c (23.388)\nd =  $d     ( 0.000)\ne =  $e ( 0.997)\nf = $f (-4.453)\n\n";
	
	echo "X<sub>i</sub> = {$a}x<sub>i</sub>+{$b}y<sub>i</sub>+{$c}\n";
	echo "Y<sub>i</sub> = {$d}x<sub>i</sub>+{$e}y<sub>i</sub>+{$f}\n";
	

	echo "</pre>";

} catch (Exception $e) {
    // Handle exception
    echo $e;
}


if (isset($_GET['phpinfo'])) {
	echo "<h3>PHP Info</h3>";
	phpinfo(); 
}

?>

<!-- !Javascripts for this page -->

<script src='<?= JQUERY ?>'></script> 
<script src='<?= JQUERYUI ?>'></script>

<script>
	$('#usertable').css({
		'max-height': '200px',
		'overflow-y': 'scroll'
	});
	$('#usertable table tr td:first-child').css('text-align', 'right');
	$('#usertable table tr td:nth-child(4)').css('text-align', 'right');
	$('#usertable table tr td:last-child').css('text-align', 'right');
	$('table tbody tr:odd').addClass('odd');
	$('table tbody tr:even').addClass('even');
</script>

</body>
</html>