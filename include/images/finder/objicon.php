<?php
	header('Content-type: image/svg+xml');
	
	$hue = is_numeric($_GET['h']) ? $_GET['h']%361 : 0;
	if ($_GET['h'] == 362) $hue = 200;
?>

<svg version="1.1"
     baseProfile="full"
     width="130" height="160"
     xmlns="http://www.w3.org/2000/svg">

  <g fill="none" stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
  	
  	<polygon points="4,4 90,4 126,40 126,156 4,156 4,4" fill="rgba(255,255,255,.75)" />
  	<polyline points="90,4 90,40 126,40" />
  	
  	
  	<polygon points="65,50 95,65 65,80 35,65 65,50" />
  	<polyline points="65,52 65,78" />
  	
  	<polygon points="70,88 100,73 100,110 70,125 70,88" />
  	<polyline points="100,110 70,99" />
  	
  	<polygon points="60,88 30,73 30,110 60,125 60,88" />
  	<polyline points="30,110 60,99" />
  </g>
</svg>