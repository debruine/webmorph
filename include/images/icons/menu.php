<?php
	header('Content-type: image/svg+xml');
	
	$hue = is_numeric($_GET['h']) ? $_GET['h']%361 : 0;
	if ($_GET['h'] == 362) $hue = 200;
?>

<svg version="1.1"
     baseProfile="full"
     width="100" height="100"
     xmlns="http://www.w3.org/2000/svg">

  <g fill="none" stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" stroke-width="15" stroke-linecap="round" stroke-linejoin="round">
  
    <polyline points="5,20 95,20" />
    <polyline points="5,50 95,50" />
    <polyline points="5,80 95,80" />
    
  </g>
</svg>