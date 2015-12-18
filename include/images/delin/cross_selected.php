<?php
	header('Content-type: image/svg+xml');

	$color = 'rgb(255,0,0)';
	if (is_numeric($_GET['r']) && is_numeric($_GET['g']) && is_numeric($_GET['b'])) {
		$color = "rgb({$_GET['r']},{$_GET['g']},{$_GET['b']})";
	}

?>

<svg version="1.1"
     baseProfile="full"
     width="11" height="11"
     xmlns="http://www.w3.org/2000/svg">
     
	<g fill="none" stroke="<?= $color ?>" stroke-width="1.0" stroke-linecap="round">
		<line x1="5.5" y1="1.0" x2="5.5" y2="10.0" />
		<line x1="1.0" y1="5.5" x2="10.0" y2="5.5" />
	</g>
</svg>