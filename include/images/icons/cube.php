<?php
	header('Content-type: image/svg+xml');
	
	$hue = is_numeric($_GET['h']) ? $_GET['h']%361 : 0;
	if ($_GET['h'] == 362) $hue = 200;
?>

<svg version="1.1"
     baseProfile="full"
     width="130" height="130"
     xmlns="http://www.w3.org/2000/svg">

  <g fill="none" stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
  	<polygon points="65,20 95,35 65,50 35,35 65,20" />
  	<polyline points="65,22 65,48" />
  	
  	<polygon points="70,58 100,43 100,80 70,95 70,58" />
  	<polyline points="100,80 70,69" />
  	
  	<polygon points="60,58 30,43 30,80 60,95 60,58" />
  	<polyline points="30,80 60,69" />
  </g>
</svg>