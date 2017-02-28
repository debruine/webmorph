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
    
    <polyline points="10,10 40,40" />
    <polyline points="10,10 30,10" />
    <polyline points="10,10 10,30" />
    
    <polyline points="90,10 60,40" />
    <polyline points="90,10 70,10" />
    <polyline points="90,10 90,30" />
    
    <polyline points="90,90 60,60" />
    <polyline points="90,90 90,70" />
    <polyline points="90,90 70,90" />
    
    <polyline points="10,90 40,60" />
    <polyline points="10,90 10,70" />
    <polyline points="10,90 30,90" />
    
  </g>
</svg>