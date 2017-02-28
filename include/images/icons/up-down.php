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
    
    <polyline points="50,5 50,95" />
    <polyline points="35,25 50,5 65,25" />
    <polyline points="35,75 50,95 65,75" />

    
  </g>
</svg>