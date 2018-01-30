<?php
    header('Content-type: image/svg+xml');
?>

<svg version="1.1"
baseProfile="full"
width="100" height="100"
xmlns="http://www.w3.org/2000/svg">

<g fill="none" stroke="hsl(200,0%,30%)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
<polygon points='88.97,72.5 50,95 11.03,72.5 11.03,27.5 50,5 88.97,27.5' />
<polyline points="50,5 50,95" />
<polyline points="11.03,27.5 88.97,72.5" />
<polyline points="88.97,27.5 11.03,72.5" />

<polyline points="11.03,72.5 11.03,27.5 50,50 88.97,27.5 88.97,72.5" 
    stroke-width="15" 
    stroke="hsla(200,80%,50%, .5)">
    <animate id="a" 
        attributeName="stroke" 
        values="hsla(200,80%,50%, .0);hsla(200,80%,50%, .50);hsla(200,80%,50%, .0)" 
        dur="5s"
        repeatCount="indefinite" />
</polyline>

<polyline points="11.03,27.5 11.03,72.5 50,50 88.97,72.5 88.97,27.5" 
    stroke-width="15" 
    stroke="hsla(250,80%,50%, .0)">
    <animate attributeName="stroke" 
        values="hsla(250,80%,50%, .5);hsla(250,80%,50%, .0);hsla(250,80%,50%, .5)" 
        dur="5s"
        repeatCount="indefinite" />
</polyline>

</g>
</svg>
