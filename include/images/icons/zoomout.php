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

    <circle cx="60" cy="40" r="30" />
    <polyline points="37,62 10,90" />
    
    <polyline points="45,40 75,40" />
    
  </g>
</svg>