<?php
	header('Content-type: image/svg+xml');
	
	$hue = is_numeric($_GET['h']) ? $_GET['h']%361 : 0;
	if ($_GET['h'] == 362) $hue = 200;
?>

<svg version="1.1"
     baseProfile="full"
     width="100" height="100"
     xmlns="http://www.w3.org/2000/svg">

  <g fill="none" stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" 
     stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
    
    <circle r="32" cx="50" cy="39" stroke="none" fill="rgba(0,0,0,0.15)" />
  	<path d="M 70.57 63.51 A32 32 0 1 0, 29.43 63.51" />
  	<line x1="35" y1="75" x2="65" y2="75" />
    <line x1="35" y1="86" x2="65" y2="86" />
    <line x1="40" y1="97" x2="60" y2="97" />
  </g>
</svg>