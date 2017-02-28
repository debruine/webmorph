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

    <polygon points="5,20 70,20 70,80, 5,80, 5,20" />
    <polygon points="70,40 95,25 95,75 70,60" />
    
  </g>
</svg>