<?php
	header('Content-type: image/svg+xml');
	
	$hue = is_numeric($_GET['h']) ? $_GET['h']%361 : 0;
	if ($_GET['h'] == 362) $hue = 200;
?>

<svg version="1.1"
     baseProfile="full"
     width="100" height="100"
     xmlns="http://www.w3.org/2000/svg">

  <g fill="none" stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
  	<polygon points="50,5 95,20 50,45 5,20" />
  	
  	<polygon points="95,20 93,76 50,95 50,45 95,20" />
  	
  	<polygon points="5,20 7,76 50,95 50,45 5,20" />
  	
  	<polygon points="50,95 95,20 5,20 50,95" />
  	
  </g>
</svg>