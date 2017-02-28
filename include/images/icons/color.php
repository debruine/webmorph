<?php
    header('Content-type: image/svg+xml');
    
    $hue = 0;
    
    // make polygon
    
    function makePolygon($n = 6, $r = 45, $cx = 50, $cy = 50, $orient = 0) {
        // set orient to 0 for flat left, 0.5 for pointy left
        
        $points = array();
        for ($i = 0; $i < $n; $i++) {
            $x = ($r * cos(2 * pi() * ($i + $orient) / $n)) + $cx;
            $y = ($r * sin(2 * pi() * ($i + $orient) / $n)) + $cy;
            $points[] = "{$x},{$y}";
        }
        
        return implode(" ", $points);
    }
?>

<svg version="1.1"
baseProfile="full"
width="100" height="100"
xmlns="http://www.w3.org/2000/svg">

<g fill="none" stroke="hsl(<?= $hue ?>,<?= $hue ? 80 : 0 ?>%,30%)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
<polygon points='<?= makePolygon(3, 26, 50,24, .75) ?>' fill="hsla(0,100%,45%,.7)" />
<polygon points='<?= makePolygon(3, 26, 72.516660498395,37,  0.25) ?>' fill="hsla(30,100%,45%,.7)" />
<polygon points='<?= makePolygon(3, 26, 72.516660498395,63, .75) ?>' fill="hsla(50,100%,45%,.7)" />
<polygon points='<?= makePolygon(3, 26, 50,76, .25) ?>' fill="hsla(100,100%,45%,.7)" />
<polygon points='<?= makePolygon(3, 26, 27.483339501605,63, .75) ?>' fill="hsla(200,100%,45%,.7)" />
<polygon points='<?= makePolygon(3, 26, 27.483339501605,37, .25) ?>' fill="hsla(280,100%,45%,.7)" />

<animateTransform attributeName="transform" 
    attributeType="XML" 
    type="rotate" 
    values="0 50 50;180 50 50;360 50 50" 
    dur="60s"
    repeatCount="indefinite" />
</g>
</svg>
