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
  	<polygon points="50,5 95,20 50,35 5,20" />
  	<polyline points="27.5,12.5 72.5,27.5" />
  	<polyline points="72.5,12.5 27.5,27.5" />
  	
  	<polygon points="95,20 95,80 50,95 50,35 95,20" />
  	<polyline points="27.5,27.5 27.5,87.5" />
  	<polyline points="5,50 50,65" />
  	
  	<polygon points="5,20 5,80 50,95 50,35 5,20" />
  	<polyline points="72.5,27.5 72.5,87.5" />
  	<polyline points="95,50 50,65" />
  	
  </g>
</svg>