<?php
    
    function polarToCartesian($centerX, $centerY, $radius, $angleInDegrees) {
      $angleInRadians = ($angleInDegrees-90) * pi() / 180.0;
    
      return array(
        'x' => $centerX + ($radius * cos($angleInRadians)),
        'y' => $centerY + ($radius * sin($angleInRadians))
      );
    }
    
    function describeArc($x, $y, $radius, $startAngle, $endAngle){
    
        $start = polarToCartesian($x, $y, $radius, $endAngle);
        $end = polarToCartesian($x, $y, $radius, $startAngle);
    
        $largeArcFlag = $endAngle - $startAngle <= 180 ? "0" : "1";
    
        $d = "M {$start['x']} {$start['y']} A{$radius} {$radius} 0 {$largeArcFlag} 0, {$end['x']} {$end['y']}";
    
        return $d;       
    }

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
     
  	<path d="<?= describeArc(50, 39 ,32, 220, 500) ?>" />

  </g>
</svg>