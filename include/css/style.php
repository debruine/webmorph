<?php
	session_start();
	header("Content-Type: text/css");

/*-------------------------------------------------
PAGE COLORS 
-------------------------------------------------*/

	$theme_hue = (is_numeric($_SESSION['theme']) && $_SESSION['theme'] >= 0 && $_SESSION['theme'] <= 361) ? $_SESSION['theme'] : 0;  // theme hue 
	if ($theme_hue == 0) {
    	// light neutral theme
    	$theme_hue == 200;                  // coloured buttons are blue
        $bgcolor = 'hsl(200,0%,90%)';       // very light grey	
        $shade = 'hsl(200,0%,75%)';         // light grey
        $highlight = 'hsl(200,0%,40%)';     // medium grey
        $theme = 'hsl(200,0%,20%)';         // dark grey
        $text = 'hsl(200,0%,10%)';          // very dark grey
        $text_on_theme = 'hsl(200,0%,100%)';// white
        $border_color = 'hsl(200,0%,100%)'; // white
        $bgpattern = '/include/images/bgpatterns/escheresque_trans';
    } else if ($theme_hue == 361) {
        // dark theme
        $theme_hue == 200;                  // coloured buttons are blue
        $bgcolor = 'hsl(200,0%,10%)';       // very dark grey
        $shade = 'hsl(200,0%,25%)';         // dark grey	
        $highlight = 'hsl(200,0%,50%)';     // medium grey
        $theme = 'hsl(200,0%,70%)';         // light grey
        $text = 'hsl(200,0%,90%)';          // very light grey
        $text_on_theme = 'hsl(200,0%,0%)';  // black
        $border_color = 'hsl(200,0%,0%)';   // black
        $bgpattern = '/include/images/bgpatterns/congruent_outline';
    } else {
        // colourful themes
        $bgcolor = 'hsl(' . $theme_hue . ',10%,90%)';       // very light theme
        $shade = 'hsl(' . $theme_hue . ',25%,75%)';         // light theme	
        $highlight = 'hsl(' . $theme_hue . ',100%,40%)';    // medium theme
        $theme = 'hsl(' . $theme_hue . ',100%,20%)';        // dark theme
        $text = 'hsl(' . $theme_hue . ',0%,10%)';           // very dark grey
        $text_on_theme = 'hsl(' . $theme_hue . ',0%,100%)'; // white
        $border_color = 'hsl(' . $theme_hue . ',0%,100%)';  // white
        $bgpattern = '/include/images/bgpatterns/escheresque_trans';
    }

	$border = '3px solid ' . $border_color;
	$round_corners = 'padding: .25em; -moz-border-radius: .5em; -khtml-border-radius: .5em; -webkit-border-radius: .5em; border-radius: .5em;';
	$big_round_corners = 'padding: .5em; -moz-border-radius: 1em; -khtml-border-radius: 1em; -webkit-border-radius: 1em; border-radius: 1em;';
	
	function shadow($horiz='0px', $vert='0px', $blur='4px', $color='rgba(0,0,0,.5)') {
		return "box-shadow: $horiz $vert $blur $color;";
	}
	
	function roundCorners($r = '0.5em') {
		return "-moz-border-radius: $r; -khtml-border-radius: $r; -webkit-border-radius: $r; border-radius: $r;";
	}
	
	function alphaRGB($rgb, $a) {
    	$rgba = str_replace('rgb', 'rgba', $rgb);
    	$ret = str_replace(')', $a . ')', $rgba);
    	return $ret;
	}
	
	function alphaHSL($hsl, $a) {
    	$hsla = str_replace('hsl', 'hsla', $hsl);
    	$ret = str_replace(')', $a . ')', $hsla);
    	return $ret;
	}

?>

/*-------------------------------------------------
PAGE BODY and LAYOUT 
-------------------------------------------------*/

/* zero the margins, paddings and borders for all elements */
* { 
    margin:0; 
    padding:0; 
    border:0;
}

.shadow { <?= shadow() ?> }

body {
	margin: 34px 0;
	padding: 0 20px;
    font-family: "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "Lucida", "Trebuchet MS", verdana, helvetica, arial, sans-serif;
    font-size:100%; 
    color:<?= $text ?>;
    background: url(<?= $bgpattern ?>) 0 0 repeat;
    background-color:<?= $theme ?>;
}

body.loading {
	background-image: none;
	background-color:<?= $theme ?>;
}

.smallnote { 
	font-size: 80%; 
}

.ui-effects-transfer { 
	border: 1px dotted <?= $theme ?>; 
	background-color: rgba(255, 255, 255, .5);
}

textarea, input, select, td { 
	font: inherit;
}

pre, code, .file {
	font-size: 90%;
	font-family: Monaco, "Andale Mono", monospace;
	color: <?= $theme ?>;
}

p {
    background:<?= $bgcolor?>;
    border: 3px solid <?= $bgcolor?>;
	color:<?= $text ?>;
	border-top-left-radius: 0.5em;
    border-top-right-radius: 0.5em;
	max-width: 60em;
	margin: 0.5em auto 0 auto !important;
	padding: 0.5em 0.5em 0.1em 0.5em;
}

p + p {
    border-radius: 0;
    margin: 0 auto !important;
    padding: 0.1em 0.5em;
}

p:last-of-type {
    border-bottom-left-radius: 0.5em;
    border-bottom-right-radius: 0.5em;
    margin: 0 auto 0.5em auto !important;
    padding: 0.1em 0.5em 0.5em 0.5em;
}

.warning {
    border: 3px solid red;
}

.modal p, .modal p + p, .modal p:last-of-type {
    background: none;
    border: none;
    border-radius: 0;
}

/***** HEADERS AND TEXT *****/

h1, h2, h3, h4, h5, h6 { 
	font-size:110%; 
	color:<?= $theme ?>;
	text-align:center; 
	padding:.5em 0;
	clear: both;
	max-width:40em;
	margin: 1em auto;
}

div > h1:first-child, 
div > h2:first-child,
div > h3:first-child,
div > h4:first-child,
div > h5:first-child,
div > h6:first-child {
	margin-top: 0;
}

h1 {
	text-align:left;
	font-variant:small-caps;
	padding:.5em;	
}

h3, h4, h5, h6 { font-size:90%; }

p {
	margin:1em auto;
	text-align:left;
	line-height:1.5;
}

.modal { 
	display: none; 
	z-index: 2000;	
}

.default_button { 
	border: <?= $border ?>; 
	background: <?= $highlight ?>;
	font-weight: normal; 
	color: #ffffff; 
}

.feature {
	max-width:30em;
	background:<?= $bgcolor?>;
	color:<?= $text ?>;
	border: <?= $border ?>;
	text-align:center;
	margin:1em auto;
	<?= $round_corners ?>
	<?= shadow() ?>
}

.feature h2 {
	padding:0;
}

strong { 
	color:<?= $theme ?>; 
	font-style:normal; 
	font-weight:bold; 
}

.sub {
	font-size: 65%;
	vertical-align: sub;
}

/***** TEXT LINKS *****/
a { 
	outline: none;
}
a:link, a:visited, a:hover, a:active, #menu a:focus {
    text-decoration: none; 
    border-bottom: .1em solid <?= $theme ?>;
}
a:link { color:<?= $theme ?>; }
a:visited { color:<?= $theme ?>; }
a:hover, a:focus { 
	color:<?= $highlight ?>; 
}
a:active { 
	background-color:<?= $theme ?>; 
	color:<?= $text_on_theme ?>; 
	outline:none;
}

.tinybutton li, a.tinybutton, a.tinybutton:link, a.tinybutton:visited {
	font-size: 75%;
	border: 0.5px solid rgba(0,0,0,.5);
	padding: .1em .5em;
	<?= roundCorners('1em'); ?>
	background-color: rgba(0,0,0,.4);
	color: <?= $text_on_theme ?> !important;
	<?= shadow('1px', '1px', '2px'); ?>
	margin: .25em;
	display: inline-block;
}
.tinybutton li:hover, a.tinybutton:hover {
	background-color: <?= $theme ?>;
}
.tinybutton li:active, a.tinybutton:active {
	background-color: <?= $theme ?>;
	<?= shadow('0.5px', '0.5px', '1px'); ?>
}

/***** BASIC LISTS *****/
ul {
    list-style: none;
    padding:0;
    margin:.5em;
}
ol {
    padding:0;
    margin:.5em .5em .5em 1.5em;
}

li {
    background-repeat:no-repeat;
    background-position:0 5px;
    padding:2px 10px 2px 2px;
}

ol li {
    padding: 2px 10px 2px 2px;
}

dl { 
	margin: 0 0; 
	line-height: 1;
}
dt { 
	float: left; 
	clear: left; 
	width: 7em; 
	margin-left: 1em;
	text-align: right; 
	color: rgb(128, 128, 128);
	overflow: visible;
	white-space: nowrap;
	text-overflow: ellipsis;
}
dd {
	color: <?= $text ?>;
	margin: 0 1em .5em 8.5em;
}

dd > dl {
	margin: 0 0 0 -8.5em;
}

dd > dl dt {
	font-style: italic;
	position: relative;
	/* left: -1em; */
}

dd > dl dd { margin-right: 0; }

dd pre {
	width: 100%;
	line-height: 1;
	padding-top: 1.5em;
}

#whatsnewDialog ul {
    list-style: circle outside;
    margin:.5em .5em .5em 1.5em;
}
#whatsnewDialog ul li {
    padding: 2px 10px 2px 2px;
}
#whatsnewDialog dt { 
    padding-top: 1em;
    color: <?= $theme ?>; 
    font-size: 110%;
}
#whatsnewDialog dd {
	padding-top: 2.5em;
	margin: 0 0 .5em 2em;
	line-height: 1.2;
	font-size: 100%;
}
#labelDialog ol input {
	width: 100%;
}

input, select, textarea {
    border:1px dotted <?= $theme ?>;
}

input:focus, select:focus, textarea:focus {
	<?= shadow('2px', '2px', '4px') ?>
	border-color: <?= $highlight ?>;
}

input[type=text]:focus, input[type=number]:focus, textarea:focus {
	background-color: <?= $bgcolor ?>;
	color: <?= $text ?>;
}

input.error, textarea.error {
	background-color: red !important;
	color: black;
	border-color: red !important;
}

input[type=number] { 
	text-align: right; 
	width: 3em;
}
input[type=search] { <?= roundCorners('1em') ?> }

#menu_username { 
	display: inline-block;
	float: right; 
}


/***** TABLES *****/

table {
	border-collapse: separate;
	border-spacing:0;
	<?= $round_corners ?>
	padding:0;
	margin: auto;
}

table .ui-buttonset .ui-button span {
	font-size: 85%; 
	padding: .1em .5em;
}

thead {
    background-color: <?= $theme ?>;
    color: <?= $text_on_theme ?>;
    text-align: center;
}

tfoot {
    background-color: <?= $theme ?>;
    color: <?= $text_on_theme ?>;
}

table.sortable thead th:hover { 
	background-color: <?= $highlight ?>;
    color: <?= $text_on_theme ?>; 
}	

td, th { 
	padding: .25em; 
	vertical-align: top;
}

.odd {
	background-color: <?= $shade ?>;
	color: <?= $text ?>;
}
.even {
	background-color: <?= $bgcolor ?>;
	color: <?= $text ?>;
}

table.pca td:nth-child(3) { 
	text-align: right; 
	width: 15em;
}

/***** PSYCHOMORPH *****/

.growl {
	position: absolute;
	z-index: 1000;
	top: 100px;
	left: 50%;
	margin-left: -160px;
	width: 300px;
	padding: 10px;
	height: auto;
	border: 3px solid <?= $theme ?>;
	border-radius: 1em;
	background-color: <?= $bgcolor ?>;
	color: <?= $text ?>;
	<?= shadow('5px', '5px', '10px'); ?>
}

.growl a , .growl a:visited, .growl a:link, .growl a:hover {
	text-decoration: underline;
}

.growl p {
	margin:0.5em 0;
	line-height:1.2;
}
.growl p:first-child { margin: 0 0 0.5em 0; }
.growl p:last-child { margin: 0.5em 0 0; }

#footer {
	margin: 0; 
	padding: 0 1.5em; 
	position: fixed;
	text-shadow: 0px 1px 0px rgba(255,255,255,0.3);
	font-size: 14px;
	font-family:"Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "Lucida", "Trebuchet MS", verdana, helvetica, arial, sans-serif;
	left: 0;
	bottom: 0;
	right: 0; 
	z-index: 1000;
	height: 22px;
	line-height: 22px;
	background: -webkit-linear-gradient(top, rgba(250,250,250,0.8) 0%, rgba(206,206,206,0.8) 100%);
	background: -moz-linear-gradient(top, rgba(250,250,250,0.8) 0%, rgba(206,206,206,0.8) 100%);
	border-bottom: 1px solid rgba(255,255,255,0.5);
	border-top: 1px solid rgba(0,0,0,0.5);
	color: black;
	text-align: left;
}

.progressBox {
	height: 22px;
	width: 100%;
	border: 1px solid <?= $theme ?>;
	border-radius: 4px;
}
.progressBar {
	height: 22px;
	width: 0%;
	float: left;
	/* background-color: <?= $theme ?>; */
}

#queue li span.status {
	position: absolute; 
	left: 0;
	width: 1.5em; /* same as left margin of li */
	text-align: center;
	display: inline-block;
	background: transparent center center no-repeat url(/include/images/menu/queue_waiting.svg);
	background-size: 13px 13px;
}
#queue li.complete {
	color: grey; 
}
#queue li.complete span.status {
	background-image: url(/include/images/menu/checkmark_grey.svg);
}
#queue li.active span.status {
	background-image: url(/include/images/menu/queue_active.svg);
}
#queue li.paused  span.status {
	background-image: url(/include/images/menu/queue_paused.svg);
}
#queue li.waiting  span.status {
	background-image: url(/include/images/menu/queue_waiting.svg);
}
#queue li span.shortcut { 
	min-width: 20px;
	text-align: right;
	display: inline-block;
	font-size: 75%; 
	background: transparent center center no-repeat;
	background-size: 18px 18px;
}
#queue li.active span.shortcut {
	background-image: url(/include/images/menu/queue_loading.svg);
}
#queue_n {
	display: inline-block;
	url(/include/images/menu/queue_waiting.svg);
	height: 11px;
	min-width: 11px;
	font-size: 11px;
	color: <?= $text_on_theme ?>;
	background-color: <?= $theme ?>;
	border-radius: 999px;
	text-align: center;
	vertical-align: super;
	line-height: 11px;
	padding: 2px 3px 3px 3px;
	margin-left:2px;
	font-family: 'Helvetica Neue';
}

#pca_files li { 
	padding-left: 2.5ex; 
	background: transparent left center no-repeat;
	background-size: 2ex 2ex;
}
#pca_files li.active {
	background-image: url(/include/images/menu/queue_loading.svg);
}
#pca_files li.complete {
	background-image: url(/include/images/menu/checkmark_grey.svg);
}

#leftEye, #rightEye, #mouth {
	position: absolute;
	top: -100px;
	left: -100px;
	display: none;
	z-index: 9999;
}

.search-hidden { display: none; }

.interface {display: none;}

#loginBox { 
	margin 2em; auto;
}
#login-button, #register-button, #reset-password-button { 
	float: right; 
	margin-left: 0.5em; 
}
#loginInterface .reg_item {
	display: none;
}
#introLinks { 
	font-size: 80%;
	text-align: center;
}

#loginBox tbody tr td { text-align: right; }
#loginBox tbody tr td+td { text-align: left; }

#prefDialog-1 input[type=text], 
#prefDialog-1 input[type=password], 
#prefDialog-1 input[type=email],
#loginBox input[type=text], 
#loginBox input[type=password], 
#loginBox input[type=email] { width: 30em; }

#prefDialog table {
    padding: 0;
}

.hue_chooser, .rgb_chooser  { 
	height: 1em; 
	width: 30em;
	background-color: <?= $highlight ?>;
	background-size: contain !important;
	background-position: center center;
	background-repeat: repeat;
}
.mask_color:not(#mask_color) {
    width: 90%;
    margin-bottom: 0.5em;
}
.hue_chooser .ui-slider-handle, .rgb_chooser .ui-slider-handle { 
    width: 2em;
    text-align: center;
    padding-bottom: 0.25em;
    background-color: <?= $border_color ?>;
    color: <?= $text_one_theme ?>;
}
.hue_chooser .ui-slider-handle { 
    border: 2px solid black;
}
.rgb_chooser .ui-slider-handle:nth-child(1) { 
    border: 2px solid red;
}
.rgb_chooser .ui-slider-handle:nth-child(2) { 
    border: 2px solid green;
}
.rgb_chooser .ui-slider-handle:nth-child(3) { 
    border: 2px solid blue;
}

#login_error {
	text-align: left;
}
#modDelinPoints {
	max-height: 10em;
	overflow: auto;
}
#modDelinLines {
	width: 100%;
	height: 4em;
}
#facialmetricEQ {
	max-height: 400px;
	overflow: auto;
}
#fm_delete {
	float: right;
	width: 40px;
	height: 50px;
	background-image: url("/include/images/finder/trash.php?h=<?= $theme_hue ?>");
	background-position: center;
	background-repeat: no-repeat;
	background-size: contain;
}
#fm_new {
	float: right;
	clear: right;
}
#fm_delete.hover {
	background-image: url("/include/images/finder/trash_open.php?h=<?= $theme_hue ?>");
}
#fm_name {width: 93%;}
#fm_equation {
	width: 100%;
	height: 3em;
	margin: 0.3em 0;
}
#fm_results td+td, #fm_results th+th {
	max-width: 5em; 
	overflow: hidden;
}
#fm_results tr td:first-child {
	white-space: nowrap; 
	max-width: 10em; 
	overflow: hidden;
	text-overflow: ellipsis;
}
#webcam, #webcamvas {
	width: 640px;
	height: 480px;
	border: 3px solid <?= $theme ?>;
	border-radius: 4px;
}
#continuum {
	border-bottom: <?= $border ?>;
	border-top: <?= $border ?>;
}
.movie_settings label {
	display: inline-block;
	width: 5.5em;
}
#continuum:hover {
	background-color: <?= $text_on_theme ?>;
	color: <?= $theme ?>;
}
#movieDialog form { 
	width: 15em;
	float: left; 
	padding-right: 1em;
}
#movieDialog form div {
	margin: .5em 0;
}
#movieBox {
	background: black center contain no-repeat url(/include/images/loaders/circle.svg);
	height: 200px;
	width: auto;
	float: left;
	<?= shadow(); ?>
}
#batchWatchDialog .imageList {
	display: none;
	width: 100%;
	height: 80px;
	overflow-x: scroll; 
	overflow-y: none;
}
.imageList div {
	width: 9999px;
}
.imageList img {
	height: 80px;
	width: auto;
	float: left;
	margin: 0 5px 0 0;
}

.modal textarea {
	width: 100%; 
	font-size: 75%;
	display: block; 
	margin: 0 auto;
}
#BAdialog table img { 
	max-height: 20px;
}
#BAdialog table td, #BAdialog table th { min-width: 100px; }

#SBTtablescroll, #BAtablescroll {
	max-height:375px; 
	overflow: auto; 
	position: relative;
}

.batchList {
	width: 100%;
	max-height:375px; 
	overflow-y: auto;
}
.batchList table {
	width: 100%;
}

.custom_batch td+td { text-align: right; }
.custom_batch input { 
	width: 5em; 
}

.batchList {
	max-height: 250px;
}
.batchImages {
	height: 250px;
}
.batchImages img {
	margin: 0 5px 5px 0;
	max-height: 80px;
	max-width: 80px;
}

.batch_name { clear: both; margin-bottom: .5em; }
.batch_name span { border-bottom: 2px solid <?= THEME ?>; }

#custom_mask_box { 
	display: block; 
	width: 100%;
	font-size: 75%;
}
#maskExample {
	width: 300px;
	height: 400px;
	position: relative;
	float: right;
	<?= shadow(); ?>
}
#maskExample.trans {
	background-color: transparent;
}

#maskExample img {
	width: 300px,
	height: 400px;
	position: absolute;
	top: 0;
	left: 0;
	display: none;
}

#custom_mask{ }
.colorcheck { 
	display: inline-block;
	margin: .1em;
	border: 1px solid grey;
	background-color: black;
	width: 1.5em; 
	height: 1.5em; 
	vertical-align: middle;
	<?= shadow('0','0', '2px'); ?>
}
#faceMaskDialog {
	/* background-image: url('<?= $bgpattern ?>'); */
}

/* MENUBAR STYLES */

.menubar {
	margin: 0; 
	padding: 0 1.5em; 
	position: fixed;
	text-shadow: 0px 1px 0px rgba(255,255,255,0.3);
	font-size: 14px;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0; 
	z-index: 1000;
	height: 22px;
	background: -webkit-linear-gradient(top, rgba(250,250,250,0.8) 0%, rgba(206,206,206,0.8) 100%);
	background: -moz-linear-gradient(top, rgba(250,250,250,0.8) 0%, rgba(206,206,206,0.8) 100%);
	border-top: 1px solid rgba(255,255,255,0.5);
	border-bottom: 1px solid rgba(0,0,0,0.5);
	overflow: hidden;
}
.menubar:hover {
	overflow: visible;
}
.menubar ul {
	list-style: none;
}
.menubar a {border: 0;}
.menubar .menucategory ul li.separator, .context_menu li.separator {
	border-top: 1px solid rgba(0,0,0,0.15);
	height: 0; 
	padding: 0;
	display: block;
	margin: 5px 1px;
}
.menubar .menucategory ul li.separator:hover {background-color: rgba(255,255,255,.95);}
.menubar > li {
	display: inline; 
	position: relative; 
	padding: 0; 
	line-height: 22px;
}
.menubar .menucategory > span {
	padding: .25em .5em;
	z-index: 1100;
}
.menubar .menucategory:hover > span {
	background-color: <?= $theme ?>;
	color: <?= $text_on_theme ?>;
}
.menubar .menucategory ul {
	position: absolute; 
	left: 0;
	margin: 0; 
	padding-bottom: .25em;
	display: none; 
	z-index: 1000; 
	background-color: rgba(255,255,255,.95); 
	border: 1px solid rgba(0,0,0,0.25); 
	border-top: 1px solid rgba(0,0,0,0.5);
	<?= shadow('0px', '8px', '10px', 'rgba(0,0,0,.3)'); ?> 
	border-radius: 0 0 6px 6px;
}
.menubar .menucategory > ul { top: 1.4em; }
.menubar .menucategory ul li {
	margin: 0; 
	padding: 0 4.5em 0 1.5em; 
	text-wrap: none; 
	white-space: nowrap;
}
.menubar .menucategory ul li span.shortcut {
	position: absolute; 
	right: .75em; 
	padding: 0;
	font-family: Monaco, "Andale Mono", monospace;
}
#current_tem_name {
	display: none;
}
.menucategory:hover #current_tem_name {
	color: <?= $text_on_theme ?>;
}
span.checkmark {
	position: absolute; 
	left: 0;
	width: 1.5em; /* same as left margin of li */
	text-align: center;
	display: inline-block;
	background: transparent center center no-repeat url(/include/images/menu/checkmark_grey.svg);
	background-size: 13px 13px;
}
li:hover > span.checkmark {
	background-image: url(/include/images/menu/checkmark_white.svg);
}
li.disabled span.checkmark, li.disabled:hover > span.checkmark {
	background-image: url(/include/images/menu/checkmark_lightgrey.svg);
}
.menubar .menucategory ul li:hover, 
.menubar .menucategory:hover > span.shortcut, 
.menubar .menucategory li:hover a, 
.menubar .menucategory a:hover, 
.menubar .menucategory a:active,
.context_menu li:hover {
	background-color: <?= $theme ?>; 
	color: <?= $text_on_theme ?>; 
	text-shadow: none !important; 
	cursor: default;
}
.menubar .menucategory ul li.disabled {
	color: grey; 
	background-color: rgba(255,255,255,.95)!important;
}
.menubar .menucategory.disabled { display: none; }

.menubar ul.submenu { 
	margin-left: 100%;
	margin-top: -22px;
}
.menubar .menucategory ul li:hover .submenu {
	color: <?= $text ?>; 
	background-color: rgba(255,255,255,.95); 
	border: 1px solid rgba(0,0,0,0.5); 
	<?= shadow('1px', '1px', '2px'); ?> 
}

#menu_window .checkmark { display: none; }

.dark .menubar {
	text-shadow: 0px 1px 0px rgba(255,255,255,0.3);
	background: -webkit-linear-gradient(top, rgba(5,5,5,0.8) 0%, rgba(50,50,50,0.8) 100%);
	background: -moz-linear-gradient(top, rgba(5,5,5,0.8) 0%, rgba(50,50,50,0.8) 100%);
	border-top: 1px solid rgba(0,0,0,0.5);
	border-bottom: 1px solid rgba(255,255,255,0.5);
}
.dark .menubar .menucategory ul li.separator, .context_menu li.separator {
	border-top: 1px solid rgba(255,255,255,0.15);
}
.dark .menubar .menucategory ul {
    background-color: rgba(0,0,0,.95); 
	border: 1px solid rgba(255,255,255,0.25); 
	border-top: 1px solid rgba(255,255,255,0.5);
}

.dark .menubar .menucategory ul li.disabled {
	background-color: rgba(0,0,0,.95)!important;
}
.dark .menubar .menucategory ul li:hover .submenu {
	background-color: rgba(0,0,0,.95); 
}

.context_menu {
	padding: 10px;
	z-index: 9000;
	position: absolute; 
	background-color: rgba(0,0,0,0);
} 
.context_menu ul {
	min-width: 10em;
	max-width: 15em;
	background-color: rgba(240,240,240,.95); 
	border: 1px solid rgba(0,0,0,0.25); 
	<?= shadow('0px', '8px', '10px', 'rgba(0,0,0,.3)'); ?> 
	border-radius: 6px;
	<?= shadow('1px', '1px', '2px'); ?> 
	font-size: 13px;
	padding: 0.25em 0em;
}
.context_menu li {
	padding: 0.1em 1em;;
}

.dark .context_menu ul {
    background-color: rgba(15,15,15,.95); 
	border: 1px solid rgba(255,255,255,0.25); 
}

#colorbox {
	width: 1em; 
	height: 1em; 
	background-color: black; 
	border: 1px solid grey;
	display: inline-block;
}
#cross, #selected_cross, #hover_cross, #pointer {
	display: none;
	z-index: 9999;
}
#pointer { 
	width: 21px;
	height: 16px;
	position: absolute; 
}
#delin {
	position: relative; 
	top: 35px;
	margin: 0; 
	padding: 0; 
	display: block;
	height: 400px;
	width: 300px;
	background: url(<?= $bgpattern ?>) 0 0 repeat; 
	overflow: visible;
	z-index: 70;
}

#template {
	position: absolute;
	top: 0;
	left: 0;
	height: 400px;
	width: 300px;
	z-index: 80;
}

.pt {
	box-sizing: border-box;
	width: 15px;
	height: 15px;
	z-index: 90;
	position: absolute;
	top: 0;
	left: 0;
	<?= roundCorners('100px'); ?>
	margin-left: -8px;
	margin-top: -8px;
	background: transparent center center no-repeat url(/include/images/delin/cross.php);
	background-size: contain;
}

.pt:hover, .pt.selected {
	box-shadow: 0 0 1px 2px rgba(255,255,127,0.15);
	background-image: url(/include/images/delin/cross.php?h=0);
}

.pt.couldselect {
	box-shadow: 0 0 1px 2px rgba(255,255,127,0.25);
	background-color: rgba(255,255,255,0.15);
}

#imgsearch { display: none; }

.toolbar {
	display: inline-block;
	padding: .25em .5em;
	margin-bottom: 0.5em;
	position: fixed;
	z-index: 900;
	top: 30px;
	left: 20px;
	right: 20px;
	background: white;
	border: 2px solid <?= THEME ?>;
	border-radius: 4px;
	font-size: 80%;
}
.toolbar button {
	position: relative;
}
.toolbar span {
	display: inline-block;
} 
#quickhelp {
	display: none;
}
#ptLabel {
	display: none;
	width: 15em;
}
#imgsize {
	width: 15em; 
	height: 15px; 
	margin: 0 .5em; 
	display: none;
	position: relative;
	top: 7px;
}

#size_value { 
	text-align: right;
}
#imgname { 
	float: right;
	max-width: 15em;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
}
/*
button.unsaved {
	background: rgb(255,78,78) !important;
}
*/
/* FINDER STYLES */

#finder {
	position: relative; 
	min-height: 200px; 
	overflow: auto;
	background: <?= $border_color ?> center center no-repeat; 
	background-size: 150px 150px;
	border: 1px solid <?= $text ?>; 
	font-size: 13px;
	<?= shadow(); ?>
}
#uploadbar { 
    position:absolute;
    top:-100px;
}
#searchbar { 
	display: none; 
	margin-bottom: .5em;
}

#finder ul {
	margin: 0; 
	height: 100%; 
	position: absolute; 
	top: 0;
	list-style: none outside none;
	z-index: 1;
}
#finder ul ul {
	border-left: 1px solid rgb(224,224,224);
	list-style: none outside none;
}
#finder li, #finder li:hover, .filehelper li {
	min-height: 20px;
	padding: 0 0 0 30px; 
	margin-bottom: 1px;
	background-color: <?= $highlight ?>;
	background-image: url("/include/images/finder/folder.php?h=<?= $theme_hue ?>"), url("/include/images/finder/folder_arrow_white.svg");
	background-position: 5px center, right 2px center;
	background-repeat: no-repeat;
	background-size: 20px, auto;
	color: <?= $text_on_theme ?>;
}
#finder li.folder.closed {
	background-color: transparent; 
	background-image: url("/include/images/finder/folder.php?h=<?= $theme_hue ?>"), url("/include/images/finder/folder_arrow_grey.svg");
	color: <?= $text ?>;
}
#finder li.file:hover {
	background-color: <?= $shade ?>;
	color: <?= $text ?>;
}
#finder li.folder.closed ul {display: none;}
#finder li.file, .filehelper li {
	background: transparent 5px 0px no-repeat url("/include/images/finder/imgicon.php?h=<?= $theme_hue ?>"); 
	color: <?= $text ?>; 
	background-size: contain;
}
#finder li.folder#trash {
	background-image: url("/include/images/finder/trash.php?h=<?= $theme_hue ?>"), url("/include/images/finder/folder_arrow_grey.svg");
	background-size: contain, auto;
}
#finder li.tem {
	background-image: url("/include/images/finder/temicon.php?h=<?= $theme_hue ?>"); 
}
#finder li.txt {
	background-image: url("/include/images/finder/txticon.php?h=<?= $theme_hue ?>"); 
}
#finder li.csv {
	background-image: url("/include/images/finder/csvicon.php?h=<?= $theme_hue ?>"); 
}
#finder li.pca {
	background-image: url("/include/images/finder/pcaicon.php?h=<?= $theme_hue ?>"); 
}
#finder li.pci {
	background-image: url("/include/images/finder/pciicon.php?h=<?= $theme_hue ?>"); 
}
#finder li.fimg {
	background-image: url("/include/images/finder/fimgicon.php?h=<?= $theme_hue ?>"); 
}
#finder li.file.selected, #average_list li.selected {
	background-color: <?= $highlight ?>;
	color: <?= $text_on_theme ?>;
}
#finder li.file.to_cut, 
#finder li.file.nosearch {
	background-color: rgba(0,0,0,.1) !important; 
	color: rgba(0,0,0,.3) !important;
}
#finder li.file.ui-selecting, #finder li.file.selected.ui-selecting  {
	background-color: <?= $highlight ?>;
	color: <?= $text ?>;
}
#finder li.file > span {
	max-width: 15em;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
}

.imghelper { 
	width: 50px;
	z-index: 9000;
}

.filehelper {
	border: none !important;
	height: auto !important;
	z-index: 9000;
	font-size: 13px;
}
.filehelper li, .filehelper li:hover {
	background-color: transparent !important; 
}

#finder li.folder.folderDrop {
	background-color: transparent;
}
#finder li.folder.folderDrop > span, .filehelper li span {
	background-color: <?= $theme ?>;
	color: <?= $text_on_theme ?>;
	border-radius: 1em;
}
.filehelper li span {
	padding: 2px 0 2px 3px !important;
	margin-left: 0px;
}

#finder li.folder ul.folderDrop {
	box-shadow: 0 0 5px <?= $theme ?> inset;
}

#finder span { 
	display: block; 
	width: 100%;
	max-width: 15em;
	position: relative;
	left: -30px;
	padding: 2px 0 2px 30px;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
}

#finder.hidetems li.tem {
	display: none;
}


#finder.imageView ul > li > ul li {
	height: 80px;
	width: 80px;
	border-radius: 7px;
	padding: 1px;
	border: 2px solid rgba(0,0,0,0);
	background-position: center center;
	margin: 5px 5px 20px 5px;
	float: left;
}
#finder.imageView li.file.selected{
	background-color: <?= $highlight ?>;
	border: 2px solid rgba(0,0,0,.1);
}
#finder.imageView li.folder {
	background-image: url("/include/images/finder/folder.php?h=<?= $theme_hue ?>");
	background-size: contain; 
}
#finder.imageView li > span {
	padding: 85px 0 0 0;
	left: -5px;
	text-align: center;
	width: 90px;
	text-overflow: ellipsis;
	overflow: hidden;
}

#finder.imageView li.selected > span, #finder.imageView li.folder:not(.closed) > span {
	color: <?= $text ?>;
}

#average_list {
    overflow-y: scroll;
    text-align: left;
}
#average_list li {
    min-height: 20px;
	padding: 0 0 0 30px; 
	margin-bottom: 1px;
    background: transparent 5px center no-repeat;
    background-size: 20px;
}

#average_list li.dupavg {
    color: red;
}

#defaultTemNotes { 
	width: 100%;
	height: 2em;
}

#help { font-size: 100%; }
#help h1 {
	text-align: center;
	font-size: 130%;
	color: <?= $text_on_theme ?>;
	background-color: <?= $theme ?>;
	width: 95%;
	display: block;
	<?= roundCorners('2em'); ?>
}
#help h1 + h2 {
    border-top: none;
}
#help h2, #help h3 { 
	text-align: left; 
	margin: 0;
	border-top: 1px solid <?= $theme ?>;
}

#help ul, #delinHelp ul {
	list-style: disc outside;
}
.tinyhelp, .projectOwnerDelete {
    display: inline-block;
    font-weight: 900;
    width: 1.3em;
    height: 1.3em;
    content: "?";
    color: <?= $theme ?>;
    background-color: <?= $border_color ?>;
    <?= roundCorners('100%'); ?>;
    <?= shadow('0px', '0px', '2px'); ?> 
    text-align: center;
    margin: 0;
    vertical-align: text-center;
}
.tinyhelp:hover, .projectOwnerDelete:hover {
    color: <?= $text_on_theme ?>;
    background-color: <?= $highlight ?>;
}
.tinyhelp:active, .projectOwnerDelete:active {
    <?= shadow('0px', '0px', '1px'); ?> 
}

/*

⌘ – &#x2318; – &#8984; – the Command Key symbol
⌥ – &#x2325; – &#8997; – the Option Key symbol
⇧ – &#x21E7; – &#8679; – the Shift Key symbol
⎋ – &#x238B; – &#9099; – the ESC Key symbol
⇪ – &#x21ea; – &#8682; – the Capslock symbol
⏎ – &#x23ce; – &#9166; – the Return symbol
⌫ – &#x232b; – &#9003; – the Delete / Backspace symbol
⇥ – &#x21e5; – &#8677; – the Tab symbol
⌃ – &#x2303; – 		   – the Ctrl symbol
	
*/

body.mac span.cmd:before {
	content: "\2318";
}
body.pc span.cmd:before {
	content: "\2303";
}
body.mac span.shiftcmd:before {
	content: "\21E7\2318";
}
body.pc span.shiftcmd:before {
	content: "21E7\2303";
}
span.opt:before {
	content: "\2325";
}
span.shiftopt:before {
	content: "\21E7\2325";
}

/* IMAGEBOX */

#imagebox {
	width: 310px; 
	overflow: auto;
	display: none;
	position: fixed;
}
#imagebox #selectedImage, #imagebox textarea {
	max-width: 300px;
	max-height: 400px;
	border: 1px solid grey; 
	<?= shadow('2px', '2px', '4px'); ?> 
	display: none;
}
#selectedImage {
	background: url(<?= $bgpattern ?>) 0 0 repeat;
}
#imagebox textarea { 
	height: 400px; 
	width: 300px;
}
#imagebox #imagedesc {
	width: 100%; 
	margin-bottom: .5em; 
	font-size: 85%;
}
#individual_image_box {
	width: 100%; 
	padding-left: 330px; 
	-moz-box-sizing: border-box; 
	-webkit-box-sizing: border-box; 
	box-sizing: border-box;
}
#avginfo p {
	max-width: 100em; 
	font-size: smaller;
}
#selectBox {
	border: 1px solid #00FF00;
	z-index: 9999;
	display: none;
	position: absolute;
}

#img_text_toggle {
	font-size: 75%;
}	

.copyright {
	font-size: xx-small; 
	width: 300px;
}
.chosen {background-color: <?= $bgcolor ?>;}
.disabled {color: #666;}

#grid { display: none; }
#grid .dim { text-align: left; }
#grid .dim input { float: right; }
#grid .dim label, #grid .dim input { width: 45%; }

#transform {
	object-fit: contain;
	overflow: hidden;
}

#destimages, #grid, #avg_image_box {
	margin: 0px 0px 0 0;
	font-size:75%; 
	position: absolute; 
	width: 310px; 
	max-width: 95%;
}
#avg_image_box { height: 450px; }
#average {
	position: absolute;
	top: 10px;
	right: 10px;
	left: 10px;
	bottom: 40px;
	background: transparent center center no-repeat url("/include/images/blankface.php?h=<?= $theme_hue ?>"); 
	background-size: contain !important;
	display: block; 
}
#avg_buttons {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 10px;
	text-align: center; 
	margin-top: 1em; 
	font-size: 75%;
}
#destimages img, #grid img {
	width: 100%; 
	box-sizing: border-box; 
	height: auto; 
	border: <?= $border ?>; 
	padding:0;
}
.hoverdrag, #destimages li.hoverdrag  {
    border-color: <?= $theme ?>;
}
#destimages li, #grid li {
	padding: 1% 0; 
	display: block; 
	clear: both;
}
#destimages li > div, #grid li div {
	width: 45%; 
	text-align: center; 
	display: inline-block; 
	float: left; 
	padding: 2%;
}
#destimages li.movie_settings {
	display: none; 
	text-align: left;
}
#destimages li.movie_settings div {
	width: 90%;
	margin: 1px auto;
}
#trans_settings span { 
	display: inline-block;
	width: 32%;
	text-align: center; 
}
#trans_settings label {
	display: inline-block;
	width: 4em;
}
#trans_settings input[type=number] { 
	width: 4em;
}

#recent_creations, #taglist {
	clear: both; 
	max-width: 100%;
	width: 100%;
}
#recent_creations {
	text-align: left;
	display: block;
}
#recent_creations img {
	width: auto; 
	height: 120px; 
	margin: 0 5px 2px 0; 
	padding: 0; 
	<?= shadow(); ?>
}
#recent_creations img:active { <?= shadow('1px', '1px', '2px'); ?> }

#resizer { font-size: 150%; }
#resizer input[type=number] {
	font-size: 100%;
	width: 3em;
}

#cropDiagram {
	text-align: center;
	position: relative;
}

#cropBox {
	display: inline-block;
	background: <?= $bgcolor ?> center no-repeat url(/include/images/cropBox.svg);
	<?= shadow(); ?>
	position: relative;
	width: 150px;
	height: 150px;
	border: 1px solid rgba(0,0,0,0.8);
	margin: 0;
}
#cropDiagram input { margin: 10px; }
#cropDiagram input[name=left], #cropDiagram input[name=right] { 
	position: relative;
	top: -70px;
}
#cropBox * {
	position: absolute;
}
#cropBoxWidth {
	left: 10px;
	bottom: 20px;
}
#cropBoxHeight{
	top: 10px;
	right: 20px;
}

#mask_builder_box {
	width: 100%;
	display: none;
	padding-top: 2em;
}

#custom_mask {
	width: 50%;
	float: right;
	height: 3em;
	border: 2px solid <?= $theme ?>;
}

#project_list {
	border: <?= $border ?>;
	background-color: <?= $bgcolor ?>;
	<?= shadow(); ?>
	max-width: 60em;
	display: none;
}

#project_list tr td:nth-child(3) { max-width: 25%; }
#project_list tr td:nth-child(4) { min-width: 5em; }

#project_list ul {
	margin: 0;
}

/**************************** START OS X BUTTON STYLE **********************************/
/******************** http://jsfiddle.net/reitermarkus/xLQ2F/ **************************/

.ui-button, .ui-button:hover {
	
	color: #000; text-decoration: none; text-shadow: 0 1px rgba(255,255,255,.2);
	font:  400 13px/19px "Helvetica Neue", Arial, sans-serif;
	
	-webkit-tap-highlight-color: transparent;
	
	padding: 0 12px;
	
	border: 1px solid;
	border-top-color: #9d9d9d; border-bottom-color: #939393;
	border-left-color: #949494; border-right-color: #949494;
	
	box-shadow: 0 1px rgba(0,0,0,0.1);
	-moz-box-shadow: 0 1px rgba(0,0,0,0.1);
	-webkit-box-shadow: 0 1px rgba(0,0,0,0.1);
	
	-webkit-appearance: none;
	
	background: #ffffff; /* Old browsers */
	
	background: -webkit-gradient(linear, left top, left bottom, 
				/* Chrome, */    				color-stop( 0%, #ffffff),
				/* Safari4+ */ 					color-stop(25%, #ffffff),       
												color-stop( 30%, #fcfcfc), 
												color-stop(35%, #f9f9f9),            
												color-stop(40%, #f7f7f7),    
												color-stop(45%, #f5f5f5),         
												color-stop( 50%, #f2f2f2), 
												color-stop(50%, #ececec),                 
												color-stop(80%, #ededed),
												color-stop(95%, #efefef),
												color-stop(100%, #f2f2f2)); 
	background: -webkit-linear-gradient(top,   #ffffff  0%, #ffffff 25%, #fcfcfc  30%, #f9f9f9 35%,
	           /* Chrome10+, */                #f7f7f7 40%, #f5f5f5 45%, #f2f2f2  50%, #ececec 50%,
	           /* Safari5.1+ */                #ededed 80%, #efefef 95%, #f2f2f2 100%);
	background: -moz-linear-gradient(top,      #ffffff  0%, #ffffff 25%, #fcfcfc  30%, #f9f9f9 35%, 
	           /* FF3.6+ */                    #f7f7f7 40%, #f5f5f5 45%, #f2f2f2  50%, #ececec 50%,
	                                           #ededed 80%, #efefef 95%, #f2f2f2 100%); 
	background: -o-linear-gradient(top,        #ffffff  0%, #ffffff 25%, #fcfcfc  30%, #f9f9f9 35%,
	           /* Opera 11.10+ */              #f7f7f7 40%, #f5f5f5 45%, #f2f2f2  50%, #ececec 50%,
	                                           #ededed 80%, #efefef 95%, #f2f2f2 100%); 
	background: -ms-linear-gradient(top,       #ffffff  0%, #ffffff 25%, #fcfcfc  30%, #f9f9f9 35%,
	           /* IE10+ */                     #f7f7f7 40%, #f5f5f5 45%, #f2f2f2  50%, #ececec 50%,
	                                           #ededed 80%, #efefef 95%, #f2f2f2 100%);
	background: linear-gradient(top,           #ffffff  0%, #ffffff 25%, #fcfcfc  30%, #f9f9f9 35%,
	          /* W3C */                        #f7f7f7 40%, #f5f5f5 45%, #f2f2f2  50%, #ececec 50%,
	                                           #ededed 80%, #efefef 95%, #f2f2f2 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( 
	      /* IE6-9 */           startColorstr='#ffffff',    endColorstr='#f2f2f2',GradientType=0 );
	
	cursor: default; -webkit-user-select: none;
	-moz-user-select: none;  user-select: none;
	
}


/* theme buttons */
<?php $hue = $theme_hue; ?>
.ui-button:active, .ui-button.ui-state-focus, .ui-button.ui-state-focus:hover, .progressBar {
	background: hsl(<?= ($hue - 6.0)%360 ?>,61.4%,83.7%); /* Old browsers */
background: -webkit-gradient(linear, left top, left bottom, 
				  								color-stop( 0%, hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)), 
				  								color-stop( 5%, hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)), 
												color-stop(10%, hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%)), 
												color-stop(15%, hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%)), 
												color-stop(20%, hsl(<?= ($hue - 1  )%360 ?>2,68.3%,72.7%)), 
												color-stop(25%, hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%)), 
												color-stop(30%, hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%)), 
												color-stop(35%, hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%)), 
												color-stop(40%, hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%)), 
												color-stop(45%, hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%)),
												color-stop(50%, hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%)), 
												color-stop(50%, hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%)), 
												color-stop(70%, hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%)), 
												color-stop(75%, hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%)), 
												color-stop(80%, hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%)), 
												color-stop(85%, hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%)), 
												color-stop(90%, hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%)), 
												color-stop(95%, hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%)), 
												color-stop(100%, hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%))); 
	/* Chrome10+ */ /* Safari5.1+ */
	background: -webkit-linear-gradient(top,   	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%); 
	/* FF3.6+ */
	background: -moz-linear-gradient(top,      	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	/* Opera 11.10+ */
	background: -o-linear-gradient(top,       	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	/* IE10+ */
	background: -ms-linear-gradient(top,       	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	/* W3C */
	background: linear-gradient(top,           	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(
	      /* IE6-9 */           startColorstr='#bcd6ef',    endColorstr='#abd4ef',GradientType=0 );
	
	border-top-color: 		hsl(<?= ($hue + 20)%360 ?>,35.7%,49.4%); 
	border-bottom-color: 	hsl(<?= ($hue + 22)%360 ?>,23.9%,36.1%);
	border-left-color: 		hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%); 
	border-right-color: 	hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%);	
}

.ui-button.ui-state-focus, .ui-button.ui-state-focus:hover, .progressBar {
    -webkit-animation: pulsate 730ms infinite alternate ease-in-out;
       -moz-animation: pulsate 730ms infinite alternate ease-in-out;
            animation: pulsate 730ms infinite alternate ease-in-out;
}

.ui-button.ui-state-focus:active, .ui-button.ui-state-error:active {
          animation: none; 
     -moz-animation: none;
  -webkit-animation: none;
}

@-webkit-keyframes pulsate {
   0% { -webkit-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); 
                box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); }
 100% { -webkit-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); 
                box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); }
}

@-moz-keyframes pulsate {
   0% { -moz-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); 
		     box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); }
 100% { -moz-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); 
		     box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); }
}

@keyframes pulsate {
   0% { box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); }
 100% { box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); }
}


/* red buttons */
<?php $hue = 350; ?>
.ui-dialog-titlebar-close, .ui-dialog-titlebar-close.ui-state-focus, button.unsaved, .ui-button.ui-state-error, .ui-button.ui-state-error:hover {
	background: hsl(<?= ($hue - 6.0)%360 ?>,61.4%,83.7%); /* Old browsers */
	/* Chrome,  */ /* Safari4+ */ 
	background: -webkit-gradient(linear, left top, left bottom, 
				  								color-stop( 0%, hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)), 
				  								color-stop( 5%, hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)), 
												color-stop(10%, hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%)), 
												color-stop(15%, hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%)), 
												color-stop(20%, hsl(<?= ($hue - 1  )%360 ?>2,68.3%,72.7%)), 
												color-stop(25%, hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%)), 
												color-stop(30%, hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%)), 
												color-stop(35%, hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%)), 
												color-stop(40%, hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%)), 
												color-stop(45%, hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%)),
												color-stop(50%, hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%)), 
												color-stop(50%, hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%)), 
												color-stop(70%, hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%)), 
												color-stop(75%, hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%)), 
												color-stop(80%, hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%)), 
												color-stop(85%, hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%)), 
												color-stop(90%, hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%)), 
												color-stop(95%, hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%)), 
												color-stop(100%, hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%))); 
	/* Chrome10+ */ /* Safari5.1+ */
	background: -webkit-linear-gradient(top,   	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%); 
	/* FF3.6+ */
	background: -moz-linear-gradient(top,      	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	/* Opera 11.10+ */
	background: -o-linear-gradient(top,       	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	/* IE10+ */
	background: -ms-linear-gradient(top,       	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	/* W3C */
	background: linear-gradient(top,           	hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%, 
												hsl(<?= ($hue - 0  )%360 ?>,70.4%,78.8%)  5%, 
												hsl(<?= ($hue - 0  )%360 ?>,64.8%,75.5%) 10%, 
												hsl(<?= ($hue - 0  )%360 ?>,66.7%,74.1%) 15%,
												hsl(<?= ($hue - 1.0)%360 ?>,68.3%,72.7%) 20%, 
												hsl(<?= ($hue - 1.5)%360 ?>,69.6%,72.9%) 25%, 
	           									hsl(<?= ($hue - 0  )%360 ?>,68.7%,71.2%) 30%, 
	           									hsl(<?= ($hue - 2.5)%360 ?>,71.2%,70.0%) 35%,
			   									hsl(<?= ($hue - 2.5)%360 ?>,71.8%,68.0%) 40%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.0%,66.9%) 45%, 
			   									hsl(<?= ($hue - 3.0)%360 ?>,74.7%,65.9%) 50%, 
			   									hsl(<?= ($hue - 4.0)%360 ?>,79.6%,59.6%) 50%,
			   									hsl(<?= ($hue - 4.5)%360 ?>,79.7%,63.3%) 70%, 
			   									hsl(<?= ($hue - 5.0)%360 ?>,80.2%,66.3%) 75%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,79.0%,68.2%) 80%, 
			   									hsl(<?= ($hue - 6.0)%360 ?>,78.1%,71.4%) 85%,
			   									hsl(<?= ($hue - 8.0)%360 ?>,76.3%,73.5%) 90%, 
			   									hsl(<?= ($hue - 11.0)%360 ?>,73.3%,76.5%) 95%, 
	                                            hsl(<?= ($hue - 12.0)%360 ?>,68.0%,80.4%) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(
	      /* IE6-9 */           startColorstr='#bcd6ef',    endColorstr='#abd4ef',GradientType=0 );
	
	border-top-color: 		hsl(<?= ($hue + 20)%360 ?>,35.7%,49.4%); 
	border-bottom-color: 	hsl(<?= ($hue + 22)%360 ?>,23.9%,36.1%);
	border-left-color: 		hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%); 
	border-right-color: 	hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%);	
}

.ui-dialog-titlebar-close:hover, .ui-dialog-titlebar-close.ui-state-focus,
button.unsaved, .ui-button.ui-state-error, .ui-button.ui-state-error:hover {
    -webkit-animation: pulsate_red 730ms infinite alternate ease-in-out;
       -moz-animation: pulsate_red 730ms infinite alternate ease-in-out;
            animation: pulsate_red 730ms infinite alternate ease-in-out;
}

@-webkit-keyframes pulsate_red {
   0% { -webkit-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); 
                box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); }
 100% { -webkit-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); 
                box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); }
}

@-moz-keyframes pulsate_red {
   0% { -moz-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); 
		     box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); }
 100% { -moz-box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); 
		     box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); }
}

@keyframes pulsate_red {
   0% { box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 2px hsl(<?= ($hue-25)%360 ?>,100%,90%); }
 100% { box-shadow: 0 1px rgba(0,0,0,0.1), inset 0 0 19px 0px hsl(<?= ($hue)%360 ?>,70%,80%); }
}

.ui-button:focus {
  -webkit-box-shadow: 0 1px rgba(0,0,0,0.1), 0 0 4px hsl(204, 100%, 88%);
     -moz-box-shadow: 0 1px rgba(0,0,0,0.1), 0 0 4px hsl(204, 100%, 88%);
          box-shadow: 0 1px rgba(0,0,0,0.1), 0 0 4px hsl(204, 100%, 88%);
        border-color: hsl(204, 68%, 58%);   
}

.ui-button.round {
  -webkit-border-radius: 9999px !important;
     -moz-border-radius: 9999px !important;
          border-radius: 9999px !important;
		  padding: 0 10px;
}
.ui-button-icon-only .ui-button-text,
.ui-button-icons-only .ui-button-text {
	text-indent: -9999999px;
}
.ui-button .ui-button-text {
	display: block;
	line-height: normal;
}

.ui-button.single{
    padding: 0 6px;
}

.ui-button b, .ui-button strong {
  font-weight: 500;
}

/**************************** END OS X BUTTON STYLE **********************************/

/**** Loader ****/

.spinner {
	font-size: 150px;
	overflow: hidden;
	width: 1em;
	height: 1em;
	text-align: center;
	color: <?= $text_on_theme ?>;
	border-radius: 50%;
	margin: 72px auto;
	position: relative;
	-webkit-transform: translateZ(0);
	-ms-transform: translateZ(0);
	transform: translateZ(0);
	-webkit-animation: load6 2s infinite ease;
	animation: load6 2s infinite ease;
}

<?php 
	$c1 = -0.35;
	$c2 = -0.38;
	$c3 = -0.41;
	$c4 = -0.44;
	$c5 = -0.47;
?>

@-webkit-keyframes load6 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
		box-shadow: 		0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	5%,
	95% {
		box-shadow: 		0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	10%,
	59% {
		box-shadow:        0 -0.830em 0 <?= $c1 ?>em <?= $theme ?>, 
		            -0.087em -0.825em 0 <?= $c2 ?>em <?= $theme ?>, 
		            -0.173em -0.812em 0 <?= $c3 ?>em <?= $theme ?>, 
		            -0.256em -0.789em 0 <?= $c4 ?>em <?= $theme ?>, 
		            -0.297em -0.775em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	20% {
		box-shadow:        0 -0.830em 0 <?= $c1 ?>em <?= $theme ?>, 
		            -0.338em -0.758em 0 <?= $c2 ?>em <?= $theme ?>, 
		            -0.555em -0.617em 0 <?= $c3 ?>em <?= $theme ?>, 
		            -0.671em -0.488em 0 <?= $c4 ?>em <?= $theme ?>, 
		            -0.749em -0.340em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	38% {
		box-shadow:        0 -0.830em 0 <?= $c1 ?>em  <?= $theme ?>, 
		            -0.377em -0.740em 0 <?= $c2 ?>em <?= $theme ?>, 
		            -0.645em -0.522em 0 <?= $c3 ?>em <?= $theme ?>, 
		            -0.775em -0.297em 0 <?= $c4 ?>em <?= $theme ?>, 
		            -0.820em -0.090em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	100% {
		-webkit-transform: rotate(360deg);
		transform: rotate(360deg);
		box-shadow: 		0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
	}
}
@keyframes load6 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
		box-shadow: 		0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	5%,
	95% {
		box-shadow: 		0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	10%,
	59% {
		box-shadow:        0 -0.830em 0 <?= $c1 ?>em <?= $theme ?>, 
		            -0.087em -0.825em 0 <?= $c2 ?>em <?= $theme ?>, 
		            -0.173em -0.812em 0 <?= $c3 ?>em <?= $theme ?>, 
		            -0.256em -0.789em 0 <?= $c4 ?>em <?= $theme ?>, 
		            -0.297em -0.775em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	20% {
		box-shadow:        0 -0.830em 0 <?= $c1 ?>em <?= $theme ?>, 
		            -0.338em -0.758em 0 <?= $c2 ?>em <?= $theme ?>, 
		            -0.555em -0.617em 0 <?= $c3 ?>em <?= $theme ?>, 
		            -0.671em -0.488em 0 <?= $c4 ?>em <?= $theme ?>, 
		            -0.749em -0.340em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	38% {
		box-shadow:        0 -0.830em 0 <?= $c1 ?>em  <?= $theme ?>, 
		            -0.377em -0.740em 0 <?= $c2 ?>em <?= $theme ?>, 
		            -0.645em -0.522em 0 <?= $c3 ?>em <?= $theme ?>, 
		            -0.775em -0.297em 0 <?= $c4 ?>em <?= $theme ?>, 
		            -0.820em -0.090em 0 <?= $c5 ?>em <?= $theme ?>;
	}
	100% {
		-webkit-transform: rotate(360deg);
		transform: rotate(360deg);
		box-shadow: 		0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>, 
				            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
	}
}

.rainbow-spin {
	font-size: 200px;
	margin: 50px auto;
	width: 1em;
	height: 1em;
	display: block; 
	float: none; 
	padding: 0;
}

.rainbow-spin > div {
	height: 1em;
	width: 1em;
	background: #612e8d;
}

.rainbow-spin > div > div {
	height: 0.83em;
	width: 0.83em;
	background: #c22286;
}

.rainbow-spin > div > div > div {
	height: 0.67em;
	width: 0.67em;
	background: #ea225e;
}

.rainbow-spin > div > div > div > div {
	height: .5em;
	width: .5em;
	background: #ed5b35;
}

.rainbow-spin > div > div > div > div > div {
	height: 0.33em;
	width: 0.33em;
	background: #f5b52e;
}

.rainbow-spin > div > div > div > div > div > div {
	height: 0.17em;
	width: 0.17em;
	background: #81c540;
}

.rainbow-spin > div > div > div > div > div > div > div {
	height: 0.08em;
	width: 0.08em;
	background: #00a396;
}

.rainbow-spin > div > div > div > div > div > div > div > div {
	height: 0.04em;
	width: 0.04em;
	background: #1674bc;
}

.rainbow-spin div {
	border-bottom: none;
	border-radius: 50%;
	box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
	-webkit-animation-name: spin;
	-webkit-animation-duration: 4000ms;
	-webkit-animation-iteration-count: infinite;
	-webkit-animation-timing-function: linear;
	animation-name: spin;
	animation-duration: 4000ms;
	animation-iteration-count: infinite;
	animation-timing-function: linear;
}

@keyframes spin {
	from { transform: rotate(0deg); }
	to { transform: rotate(360deg); }
}

@-webkit-keyframes spin {
	from { transform: rotate(0deg); }
	to { transform: rotate(360deg); }
}

.rainbow-loader {
    font-size: 300px;
	bottom: 0;
	height: 1em;
	left: 0;
	margin: auto;
	position: relative;
	right: 0;
	top: 0;
	width: 1em;
}

.rainbow-loader > div {
	animation: rainbow-loader 5000ms cubic-bezier(.175, .885, .32, 1.275) infinite;
	-o-animation: rainbow-loader 5000ms cubic-bezier(.175, .885, .32, 1.275) infinite;
	-ms-animation: rainbow-loader 5000ms cubic-bezier(.175, .885, .32, 1.275) infinite;
	-webkit-animation: rainbow-loader 5000ms cubic-bezier(.175, .885, .32, 1.275) infinite;
	-moz-animation: rainbow-loader 5000ms cubic-bezier(.175, .885, .32, 1.275) infinite;
	box-sizing: border-box;
	-o-box-sizing: border-box;
	-ms-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	height: 0.5em;
	left: 0;
	overflow: hidden;
	position: absolute;
	top: 0;
	transform-origin: 50% 100%;
	-o-transform-origin: 50% 100%;
	-ms-transform-origin: 50% 100%;
	-webkit-transform-origin: 50% 100%;
	-moz-transform-origin: 50% 100%;
	width: 1em;
}
.rainbow-loader > div > div {
	border: 0.06em solid transparent;
	box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
	border-radius: 100%;
	-o-border-radius: 100%;
	-ms-border-radius: 100%;
	-webkit-border-radius: 100%;
	-moz-border-radius: 100%;
	box-sizing: border-box;
	-o-box-sizing: border-box;
	-ms-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	height: 1em;
	left: 0;
	margin: 0 auto;
	position: absolute;
	right: 0;
	top: 0;
	width: 1em;
}
.rainbow-loader > div:nth-child(1) { 
    animation-delay: -57.5ms;
	-o-animation-delay: -57.5ms;
	-ms-animation-delay: -57.5ms;
	-webkit-animation-delay: -57.5ms;
	-moz-animation-delay: -57.5ms; 
}
.rainbow-loader > div:nth-child(2) { 
    animation-delay: -115ms;
	-o-animation-delay: -115ms;
	-ms-animation-delay: -115ms;
	-webkit-animation-delay: -115ms;
	-moz-animation-delay: -115ms; 
}
.rainbow-loader > div:nth-child(3) { 
    animation-delay: -172.5ms;
	-o-animation-delay: -172.5ms;
	-ms-animation-delay: -172.5ms;
	-webkit-animation-delay: -172.5ms;
	-moz-animation-delay: -172.5ms; 
}
.rainbow-loader > div:nth-child(4) { 
    animation-delay: -230ms;
	-o-animation-delay: -230ms;
	-ms-animation-delay: -230ms;
	-webkit-animation-delay: -230ms;
	-moz-animation-delay: -230ms; 
}
.rainbow-loader > div:nth-child(5) { 
    animation-delay: -287.5ms;
	-o-animation-delay: -287.5ms;
	-ms-animation-delay: -287.5ms;
	-webkit-animation-delay: -287.5ms;
	-moz-animation-delay: -287.5ms; 
}
.rainbow-loader > div:nth-child(6) { 
    animation-delay: -345ms;
	-o-animation-delay: -345ms;
	-ms-animation-delay: -345ms;
	-webkit-animation-delay: -345ms;
	-moz-animation-delay: -345ms; 
}

.rainbow-loader > div:nth-child(1) > div {
		border-color: hsla(0, 100%, 45%, .8);
		height: 0.9em;
		width: 0.9em;
		top: 0.07em;
}
.rainbow-loader > div:nth-child(2) > div {
		border-color: hsla(30, 100%, 50%, .8);
		height: 0.76em;
		width: 0.76em;
		top: 0.14em;
}
.rainbow-loader > div:nth-child(3) > div {
		border-color: hsla(50, 100%, 50%, .8);
		height: 0.62em;
		width: 0.62em;
		top: 0.21em;
}
.rainbow-loader > div:nth-child(4) > div {
		border-color: hsla(100, 100%, 30%, .8);
		height: 0.48em;
		width: 0.48em;
		top: 0.28em;
}
.rainbow-loader > div:nth-child(5) > div {
		border-color: hsla(200, 100%, 35%, .8);
		height: 0.34em;
		width: 0.34em;
		top: 0.35em;
}
.rainbow-loader > div:nth-child(6) > div {
		border-color: hsla(275, 100%, 40%, .8);
		height: 0.20em;
		width: 0.20em;
		top: 0.42em;
}

@keyframes rainbow-loader {
	0%, 15% {
		transform: rotate(0);
		transform: rotate(0);
	}
	50% {
		transform: rotate(360deg);
		transform: rotate(360deg);
	}
	100% {
		transform: rotate(0);
		transform: rotate(0);
	}
}

@-o-keyframes rainbow-loader {
	0%, 15% {
		-o-transform: rotate(0);
		transform: rotate(0);
	}
	50% {
		-o-transform: rotate(360deg);
		transform: rotate(360deg);
	}
	100% {
		-o-transform: rotate(0);
		transform: rotate(0);
	}
}

@-ms-keyframes rainbow-loader {
	0%, 15% {
		-ms-transform: rotate(0);
		transform: rotate(0);
	}
	50% {
		-ms-transform: rotate(360deg);
		transform: rotate(360deg);
	}
	100% {
		-ms-transform: rotate(0);
		transform: rotate(0);
	}
}

@-webkit-keyframes rainbow-loader {
	0%, 15% {
		-webkit-transform: rotate(0);
		transform: rotate(0);
	}
	50% {
		-webkit-transform: rotate(360deg);
		transform: rotate(360deg);
	}
	100% {
		-webkit-transform: rotate(0);
		transform: rotate(0);
	}
}

@-moz-keyframes rainbow-loader {
    0%, 15% {
		-moz-transform: rotate(0);
		transform: rotate(0);
	}
	50% {
		-moz-transform: rotate(360deg);
		transform: rotate(360deg);
	}
	100% {
		-moz-transform: rotate(0);
		transform: rotate(0);
	}
}


.ui-state-active a,
.ui-state-active a:link,
.ui-state-active a:visited,
.ui-state-active,
.ui-widget-content .ui-state-active,
.ui-widget-header .ui-state-active,
.ui-state-hover a,
.ui-state-hover a:hover,
.ui-state-hover a:link,
.ui-state-hover a:visited,
.ui-state-focus a,
.ui-state-focus a:hover,
.ui-state-focus a:link,
.ui-state-focus a:visited,
.ui-state-hover,
.ui-widget-content .ui-state-hover,
.ui-widget-header .ui-state-hover,
.ui-state-focus,
.ui-widget-content .ui-state-focus,
.ui-widget-header .ui-state-focus,
.ui-state-default a,
.ui-state-default a:link,
.ui-state-default a:visited,
.ui-state-default,
.ui-widget-content .ui-state-default,
.ui-widget-header .ui-state-default,
.ui-widget-header a,
.ui-widget-content,
.ui-widget-content a,
.ui-widget-header {
	color: <?= $text ?>;
}

.ui-dialog .ui-dialog-content {
    background-color: <?= $border_color ?>;
}

.ui-widget-content {
	background: <?= $border_color ?>;
}
