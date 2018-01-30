<?php
    $hue = is_numeric($_GET['h']) ? $_GET['h']%361 : 0;
    if ($_GET['h'] == 362) $hue = 200;
    
    function makePolygon($n = 6, $r = 45, $cx = 50, $cy = 50, $orient = 0) {
        // set orient to 0 for flat left, 0.5 for pointy left
        
        $points = array();
        for ($i = 0; $i < $n; $i++) {
            $x = round(($r * cos(2 * pi() * ($i + $orient) / $n)) + $cx,2);
            $y = round(($r * sin(2 * pi() * ($i + $orient) / $n)) + $cy,2);
            $points[] = "{$x},{$y}";
        }
        
        return implode(" ", $points);
    }
    
?>
    
<svg><defs>

<symbol id="center">
<g fill="none" 
    stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" 
    stroke-width="6" 
    stroke-linecap="round" 
    stroke-linejoin="round">
    <polyline points="50,95 50,5" />
    <polyline points="5,50 95,50" />
    <circle cx="50" cy="50" r="25" />
</g>
</symbol>
    
<symbol id="color">
<g fill="none" 
    stroke="hsl(0,80%,30%)" 
    stroke-width="6" 
    stroke-linecap="round" 
    stroke-linejoin="round">
    <polygon points='<?= makePolygon(3, 26, 50,24, .75) ?>' fill="hsla(0,100%,45%,.7)" />
    <polygon points='<?= makePolygon(3, 26, 72.52,37,  0.25) ?>' fill="hsla(30,100%,45%,.7)" />
    <polygon points='<?= makePolygon(3, 26, 72.52,63, .75) ?>' fill="hsla(50,100%,45%,.7)" />
    <polygon points='<?= makePolygon(3, 26, 50,76, .25) ?>' fill="hsla(100,100%,45%,.7)" />
    <polygon points='<?= makePolygon(3, 26, 27.48,63, .75) ?>' fill="hsla(200,100%,45%,.7)" />
    <polygon points='<?= makePolygon(3, 26, 27.483,37, .25) ?>' fill="hsla(280,100%,45%,.7)" />
</g>
</symbol>

<symbol id="cube">
<g fill="none" 
    stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" 
    stroke-width="6" 
    stroke-linecap="round" 
    stroke-linejoin="round">
    <polygon points="65,20 95,35 65,50 35,35 65,20" />
    <polyline points="65,22 65,48" />
    <polygon points="70,58 100,43 100,80 70,95 70,58" />
    <polyline points="100,80 70,69" />
    <polygon points="60,58 30,43 30,80 60,95 60,58" />
    <polyline points="30,80 60,69" />
</g>
</symbol>

</defs></svg>