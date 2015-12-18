<?php
	header('Content-type: image/svg+xml');
	
	if (is_numeric($_GET['r']) && is_numeric($_GET['g']) && is_numeric($_GET['b'])) {
		$color = "rgb({$_GET['r']},{$_GET['g']},{$_GET['b']})";
	} else {
		$h = array_key_exists('h', $_GET) ? $_GET['h'] : 120;
		$s = array_key_exists('s', $_GET) ? $_GET['s'] : 100;
		$l = array_key_exists('l', $_GET) ? $_GET['l'] : 40;
		$a = array_key_exists('a', $_GET) ? $_GET['a'] : 1.0;
		$color = "hsl({$h}, {$s}%, {$l}%)";
		//echo $color; exit;
	}
?>


<svg version="1.1"
     baseProfile="full"
     width="29" height="29"
     xmlns="http://www.w3.org/2000/svg">
     
	<g fill="none" stroke="<?= $color ?>" stroke-width="1.5" stroke-linecap="round">
		<line x1="14.5" y1="5.0" x2="14.5" y2="24.0" />
		<line x1="5.0" y1="14.5" x2="24.0" y2="14.5" />
	</g>
</svg>