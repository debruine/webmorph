<?php
	header('Content-type: image/svg+xml');
	
	$hue = is_numeric($_GET['h']) ? $_GET['h']%361 : 0;
	if ($_GET['h'] == 362) $hue = 200;
?>

<svg version="1.1"
     baseProfile="full"
     width="100" height="100"
     xmlns="http://www.w3.org/2000/svg">

  <g fill="none" stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
  	<ellipse cx="50" cy="60" rx="30" ry="35" />
  	<line x1="50" y1="30" x2="50" y2="95" />
  	
  	<ellipse cx="50" cy="30" rx="20" ry="15" fill="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" />
  	<line x1="42" y1="15" x2="40" y2="5" />
  	<line x1="58" y1="15" x2="60" y2="5" />
  	
  	<line x1="20" y1="45" x2="9" y2="40" />
  	<line x1="20" y1="60" x2="5" y2="60" />
  	<line x1="20" y1="75" x2="9" y2="80" />
  	
  	<line x1="80" y1="45" x2="91" y2="40" />
  	<line x1="80" y1="60" x2="95" y2="60" />
  	<line x1="80" y1="75" x2="91" y2="80" />
  </g>
</svg>