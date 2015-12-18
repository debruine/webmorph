<?php
	require_once $_SERVER['DOCUMENT_ROOT'] .'/include/config.php';
	header("Content-Type: text/css");

/*-------------------------------------------------
CSS for all pages in my php_practice webpage

PAGE COLORS 
-------------------------------------------------*/
    $bgcolor = 'hsl(' . THEME_HUE . ',10%,90%)'; // very light theme
    $text = '#222'; // (very dark grey)
    $theme = 'hsl(' . THEME_HUE . ',100%,20%)'; // dark theme
    $highlight = 'hsl(' . THEME_HUE . ',100%,30%)'; // bright theme
    $text_on_theme = 'white';
    $shade = 'hsl(' . THEME_HUE . ',25%,75%)'; // light theme
    $border_color = 'white';
	
	$border = ((MOBILE) ? '3' : '5') . 'px solid ' . $border_color;
	$round_corners = 'padding: .5em; -moz-border-radius: 1em; -khtml-border-radius: 1em; -webkit-border-radius: 1em; border-radius: 1em; ';
	$big_round_corners = 'padding: .5em; -moz-border-radius: 1em; -khtml-border-radius: 1em; -webkit-border-radius: 1em; border-radius: 1em;';
	
	function shadow($horiz='3px', $vert='3px', $blur='6px', $color='rgba(0,0,0,.5)') {
		return "box-shadow: $horiz $vert $blur $color;";
	}
	
	function roundCorners($r = '1em') {
		return "-moz-border-radius: $r; -khtml-border-radius: $r; -webkit-border-radius: $r; border-radius: $r;";
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

.color1, .color2, .color3, .color4, .color5, .color6, .color7, .color8, .color9 {
	color: <?= $text_on_theme ?>;
}
.color1 { background-color: hsl(200,100%,20%); }
.color2 { background-color: hsl(240,100%,20%); }
.color3 { background-color: hsl(280,100%,20%); }
.color4 { background-color: hsl(320,100%,20%); }
.color5 { background-color: hsl(  0,100%,20%); }
.color6 { background-color: hsl( 30,100%,20%); }
.color7 { background-color: hsl( 60,100%,20%); }
.color8 { background-color: hsl(120,100%,20%); }

a.color1:hover { background-color: hsl(200,100%,30%); }
a.color2:hover { background-color: hsl(240,100%,30%); }
a.color3:hover { background-color: hsl(280,100%,30%); }
a.color4:hover { background-color: hsl(320,100%,30%); }
a.color5:hover { background-color: hsl(  0,100%,30%); }
a.color6:hover { background-color: hsl( 30,100%,30%); }
a.color7:hover { background-color: hsl( 60,100%,30%); }
a.color8:hover { background-color: hsl(120,100%,30%); }

.multi_face, .white_face, .african_face, .easian_face, .wasian_face {
	background-image: url(/images/bg/faces_small);
	width: 50px; height: 130px;
	background-repeat: no-repeat;
}

.multi_face 	{ background-position: 0px 0px; }
.african_face 	{ background-position: -50px 0px; }
.easian_face 	{ background-position: -100px 0px; }
.white_face 	{ background-position: -200px 0px; }
.wasian_face 	{ background-position: -150px 0px; }

.multi_female, .white_female, .african_female, .easian_female, .wasian_female,
.multi_male, .white_male, .african_male, .easian_male, .wasian_male {
	background-image: url(/images/bg/faces_small);
	width: 50px; height: 65px;
	background-repeat: no-repeat;
}

.multi_female 	{ background-position: 0px 0px; }
.african_female { background-position: -50px 0px; }
.easian_female 	{ background-position: -100px 0px; }
.white_female 	{ background-position: -200px 0px; }
.wasian_female 	{ background-position: -150px 0px; }
.multi_male 	{ background-position: 0px -65px; }
.african_male 	{ background-position: -50px -65px; }
.easian_male 	{ background-position: -100px -65px; }
.white_male 	{ background-position: -200px -65px; }
.wasian_male 	{ background-position: -150px -65px; }

body {
    font-family:"Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "Lucida", "Trebuchet MS", verdana, helvetica, arial, sans-serif;
    font-size:100%; 
    color:<?= $text ?>;
    background: url(/images/bg/textures/foil) 0 0 repeat;
    background-color:<?= $bgcolor ?>;
    width:100%;
}

textarea, input, select, td { 
	font: inherit;
}

body.logo { 

	background: url(/images/bg/faceline) -50px 50px repeat-y, url(/images/bg/faceline) right -50px top 680px repeat-y, 0px 0px repeat url(/images/bg/textures/foil);
	/* background: url(/images/bg/faceline) -50px 50px repeat-y, 0px 0px repeat url(/images/bg/textures/foil); */
}

/* Heights for Sticky Footer */
html, body {height: 100%;}
#wrap {min-height: 100%;}

#header {
	clear:both;
	float:left;
	width: 100%;
	padding: 10px 0 15px;
	background-color: <?= $theme ?>;
	color: <?= $text_on_theme ?>;
	border-bottom: <?= $border ?>;
	<?= shadow(); ?>
	margin-bottom: 1em;
}

#header.minimal {
	padding-bottom: 10px;
	font-size: 75%;
}

#header.minimal .fb_profile_pic { display: none; }

.female_mx { background-position:0px 0px !important; }
.male_mx { background-position:0px -150px !important; }
.female_af { background-position:-120px 0px !important; }
.male_af { background-position:-120px -150px !important; }
.female_ea { background-position:-240px 0px !important; }
.male_ea { background-position:-240px -150px !important; }
.female_wa { background-position:-360px 0px !important; }
.male_wa { background-position:-360px -150px !important; }
.female_wh { background-position:-480px 0px !important; }
.male_wh { background-position:-480px -150px !important; }

#breadcrumb { 
	float: left; 
	text-align: left; 
	font-size: 1.2em;
	/*width: 65%; */
	padding: 0 0 0 20px; 
	margin: 0;
	color: <?= $text_on_theme ?>;
}

#breadcrumb li { 
	display: inline; 
	padding: 0; 
	margin: 0;
}

ul#login_info {
	font-size: smaller;
	float: right;
	height: 50px;
	/*width: 30%;*/
	text-align: right;
	padding: 0 20px 0 0;
	margin: 0;
}

#header_username {
	position:relative; /* for fb_profile_pic positioning */
}

div.fb_profile_pic {
	vertical-align: text-top;
	width:50px;
	height:50px;
	position: absolute;
	left:-50px;
	top:0;
	background: no-repeat center center;
}


#login_info li { 
	/*display: block; 
	float: left;*/
	margin: 0 0 5px 0;
	padding: 0 .5em 0 .5em;
	/*white-space: nowrap;*/
}

ul#login_info li label { display: block; text-align: left; }
ul#login_info li input { display: block; text-align: left; }

#loginbox {
	text-align: left;
	display: none;
}

#loginbox label {
	font-size:smaller;
	display: block;
}

#loginbox input {
	width: 100%;
	font-size: 200%;
}

#login_error {
	display: none;
	width: 95%;
	margin: 1em auto;
}

/* Facebook Connect button styles */

#fb-login {
	height: 2em;
	width: 10em;
	overflow: hidden;
}

a:hover.fb_button, a:hover.fb_button_rtl {
	text-decoration: none;
	border: none;
}

.users_online {
	font-size: x-small; 
	float: right; 
	clear: right; 
	padding: .5em 20px 0 0;
	color: <?= $bgcolor ?>;
}

#contentmask {
	position:relative;				/* This fixes the IE7 overflow hidden bug */
	clear:both;
	float:left;
	width:100%;						/* width of whole page */
	overflow:hidden;				/* This chops off any overhanging divs */
	padding-bottom: 55px;			/* must be same height as the footer */	
}

#content {
	float:left;
	width:100%;
	position:relative;
	right: 0;
}

#maincontent, #menu {
	float:left;
	position:relative;
}

#maincontent {
	box-sizing:border-box;
	-moz-box-sizing:border-box; 	/* Firefox */
	-webkit-box-sizing:border-box; 	/* Safari */
	width: 100%;
	min-height: 600px;
	padding-right: 280px;
	padding-left: 50px;
	overflow:auto;
	padding-bottom: 1em;  				
}

#menu {
	position: absolute;
	width: 220px;
	right: 50px;
	min-width: 220px;	
}


.nomenu #content {
	right:0;						/* altered widths with no right menu */
}

.nomenu #maincontent {
	padding-right: 50px;		
}

#footer {
	position: relative;
	margin-top: -55px; 				/* negative value of footer height */
	height: 40px;					/* footer height minus border-width and padding */
	clear:both;
	float:left;
	width:100%;
	padding:5px 0;
	border-top: <?= $border ?>;
	background-color: <?= $theme ?>;
	color: <?= $text_on_theme ?>;
	text-align: center;
	<?= shadow(); ?>
}

#footer a, #footer a:visited, th a, th a:visited, tr.radiorow_values a, tr.radiorow_values a:visited { 
	color: <?= $text_on_theme ?>; 
	border-color: <?= $text_on_theme ?>;
}

#footer a:hover, #footer a:active, th a:hover,th a:active, tr.radiorow_values a:hover, tr.radiorow_values a:active { 
	background-color: <?= $text_on_theme ?>; 
	color: <?= $theme ?>; 
}

tr.radiorow_values a {
	border:none;
}

.disclaimer {
	color:#444;
	font-size:x-small;
	width:auto;
	max-width:100%;
	text-align:center;
}

#social_bookmarks {
	float: left;
	margin-left: 20px;
	padding:0;
	vertical-align: middle;
	height:24px;
}

#social_bookmarks li {
	display: inline;
	padding:0;
}

#social_bookmarks li a {
	width:24px;
	height:24px;
	background: transparent 0 0 no-repeat url(/images/icons/social/social);
	border: none;
	overflow: hidden;
	display: inline-block;
}

#social_bookmarks li a.delicious { background-position: 0px 0px; }
#social_bookmarks li a.digg { background-position: -24px 0px; }
#social_bookmarks li a.facebook { background-position: -48px 0px; }
#social_bookmarks li a.feed { background-position: -72px 0px; }
#social_bookmarks li a.stumbleupon { background-position: -96px 0px; }
#social_bookmarks li a.twitter { background-position: -120px 0px; }

#social_bookmarks a {
	border: none;
}

#social_bookmarks a:active, #social_bookmarks a:hover {
	background-color: transparent;
}

/***** RIGHT COLUMN MENU *****/

#menu ul, #menu #fb_facepile, #menu #fb_rec, #menu #google_ad {
	width:200px;
}

#menu #google_ad {
	height: 200px;
	margin-top: 1em;
}

#menu > ul, #dash { 
	float: none; 
	border: <?= $border ?>;
	<?= $round_corners ?>
	<?= shadow() ?>
	background-color: <?= $theme ?>;
	color: white;
	padding: .5em 1em;
	margin: 1em 0;
	width: 170px;
}

#menu li { 
	font-weight: bold; 
	padding: .55em 0 .55em 40px;
	background:url("/images/icons/glyphish/icons/icons-theme/28-star") no-repeat left center;	
}

#menu li.this_section {  }

#menu li.home 		{ background-image: url("/images/icons/glyphish/icons/icons-white/53-house"); }
#menu li.exp 		{ background-image: url("/images/icons/glyphish/icons/icons-white/91-beaker-2"); }
#menu li.quest 		{ background-image: url("/images/icons/glyphish/icons/icons-white/179-notepad"); }
#menu li.demos 		{ background-image: url("/images/icons/glyphish/icons/icons-white/269-happyface"); }
#menu li.psychomorph{ background-image: url("/images/icons/glyphish/my/my-white/lucha_libre_4877"); }
#menu li.tech 		{ background-image: url("/images/icons/glyphish/icons/icons-white/269-happyface"); }
#menu li.feedback 	{ background-image: url("/images/icons/glyphish/icons/icons-white/137-presentation"); }
#menu li.map 		{ background-image: url("/images/icons/glyphish/icons/icons-white/07-map-marker"); }
#menu li.students 	{ background-image: url("/images/icons/glyphish/icons/icons-white/140-gradhat"); }
#menu li.faq 		{ background-image: url("/images/icons/glyphish/icons/icons-white/112-group"); }
#menu li.res 		{ background-image: url("/images/icons/glyphish/icons/icons-white/164-glasses-2"); }
#menu li.my 		{ background-image: url("/images/icons/glyphish/icons/icons-white/123-id-card"); }

#menu a { 
	border:none;
	font-weight:bold;
}
#menu a:link, #menu a:visited { 
    color:#FFF;
}
#menu a:hover, #menu a:active, #menu a:focus {
    color: white; /* <?= $theme ?>; */
    background-color:transparent;
    border-bottom: 1px solid white;
}

/* UNCOMMENT FOR BOTTOM MENU */
/*
	#content { right:0%; }
	#maincontent {
		width:90%;
		left:5%;
	}
	#menu {
		text-align:center;
		clear:both;
		width:90%;
		left:5%;
		border:2px solid <?= $border_color ?>;
		border-left:none;
		border-right:none;
		padding:0;
	}
	#menu ul { margin:.25em 0; }
	#menu li { 
		display:inline; 
		padding:0 .5em;
		font-size:90%;
	}
	#menu li.this_section { 
		border-bottom:2px solid <?= $theme ?>; 
		background:none;
	}
*/

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
	max-width:40em;
}

p.fullwidth { width:auto; max-width:100%; }

.modal { display: none; }

/* feature boxes for making information stand out */
.feature {
	max-width:30em;
	background:<?= $theme ?>;
	color:<?= $text_on_theme ?>;
	border: <?= $border ?>;
	text-align:center;
	margin:1em auto;
	<?= $big_round_corners ?>
	<?= shadow() ?>
}

.main {
	width:42%;
	min-width:200px;
	margin:0;
	float:left;
	text-align: left;
	margin: 1em 0 1em 3%;
}

.main h2 { margin: 0; }

.main ul li { padding: .25em 0; }

.feature h2 {
	background-color:<?= $theme ?>;
	color:<?= $text_on_theme ?>;
	padding:0;
}

.feature a:link, .feature a:visited {
	color:<?= $text_on_theme ?>;
	border-color: <?= $text_on_theme ?>;
}

.feature a:hover, .feature a:active {
	color:<?= $theme ?>;
	background-color:<?= $text_on_theme ?>;
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

.reference {
	font-size: 75%;
	width:auto; max-width:100%;
}

.journal { font-style: italic; }

hr { 
	clear:both; 
	color:<?= $text ?>;
	background-color:<?= $text ?>;
	width:100%;
	height:2px;
	margin:.5em 0;
}

hr.invisible { height:0; margin:0; }

.hidden {
	display:none;
}

/***** TEXT LINKS *****/
a { 
	outline: none;
}
a:link, a:visited, a:hover, a:active, #menu a:focus {
    text-decoration:none; 
    border-bottom: .1em solid <?= $theme ?>;
}
a:link { color:#000; }
a:visited { color:#000; }
a:hover, a:focus { 
	color:<?= $theme ?>; 
}
a:active { 
	background-color:<?= $theme ?>; 
	color:<?= $text_on_theme ?>; 
	outline:none;
}

/***** SPECIAL LINKS *****/

#header a:link, #header a:visited {
	<?= $round_corners ?>
	padding: 0 .25em;
	color: <?= $text_on_theme ?>;
	text-decoration: underline;
}

#header a:hover, #header a:active, #header a:focus {
	color:<?= $theme ?>;
	background-color:<?= $text_on_theme ?>;
	text-decoration: none;
}

/***** IMAGE LINKS *****/
a img {
    border: none;
}
a:hover img { border:none; }

a.no_underline { border: none; }

/***** BASIC LISTS *****/
ul {
    list-style:none;
    padding:0;
    margin:.5em;
    /* float: left; */
}
li {
    background-repeat:no-repeat;
    background-position:0 5px;
    padding:2px 10px 2px 20px;
}

dl { margin: 1em 0; }
dt { 
	float: left; 
	clear: left; 
	width: 9em; 
	margin-left: 1em;
	text-align: right; 
	font-weight: bold;
	overflow: hidden;
	
}
dd {
	margin: 0 1em .5em 10.5em;
}

dd > dl {
	margin: 2em 0 0 -10.5em;
	border: 3px solid <?= THEME ?>;
}

dd > dl dd { margin-right: 0; }

ul.pdf li, li.pdf { background-image: url(/images/icons/my/pdf.gif); }

.bigbuttons li {
	display: inline;
	float: left;
	margin: .5em;
	padding: 0;
}
.bigbuttons li a {
	display: block; 
	word-wrap: break-word;
	position: relative; 
	overflow: hidden;
	width: 8em;
	height: 8em;
	background: <?= $theme ?> center 70% no-repeat;
	color: <?= $text_on_theme ?>;
	text-align: center;
	border: <?= $border ?>;
	<?= $big_round_corners ?>
	<?= shadow() ?>
}

.bigbuttons li.hide {
	display: none;
}

.bigbuttons li.done a {
	background-color: hsl(200, 10%, 50%);
}

.bigbuttons li.hide a, .bigbuttons li.hide.done a {
	background-color: hsl(0, 100%, 30%);
}

.bigbuttons li.inactive a, .bigbuttons li.test a {
	background-color: hsl(70, 100%, 30%);
}

.bigbuttons li.inactive a:hover, .bigbuttons li.test a:hover {
	background-color: hsl(70, 100%, 40%) !important;
}

.bigbuttons li a .corner {
	font-size: 70%; 
	position: absolute; 
	bottom: 1em; 
	right: -3em; 
	color: <?= $theme ?>;
	background-color: white; 
	display: block; 
	width: 10em;
	text-align: center;
	-webkit-transform: rotate(-45deg); 
	-moz-transform: rotate(-45deg);
}

.bigbuttons li.done a .corner { color: hsl(200, 10%, 50%); }
.bigbuttons li.hide a .corner { color: hsl(0, 100%, 30%); }


.bigbuttons li a.exp 	{ background-image: url("/images/icons/glyphish/icons/icons-white/91-beaker-2@2x"); }
.bigbuttons li a.quest	{ background-image: url("/images/icons/glyphish/icons/icons-white/179-notepad@2x"); }
.bigbuttons li a.econ	{ background-image: url("/images/icons/glyphish/icons/icons-white/130-dice@2x"); }
.bigbuttons li a.set	{ background-image: url("/images/icons/glyphish/icons/icons-white/300-orgchart@2x"); }

input.fav + label { 
	width: 20px; height: 20px;
	border: 3px solid white;
	<?= roundCorners('13px') ?>
	background: <?= $highlight ?> url();
	<?= shadow('1px','1px','2px') ?>
}

input.fav:active + label {
	<?= shadow('0','0','1px') ?>
}

input.fav + label .ui-icon, input.fav:hover + label .ui-icon {
	background-image: none;
}

input.fav:hover:checked + label .ui-icon, input.fav:checked + label .ui-icon {
	background-image: url("/include/css/images/ui-icons_ffffff_256x240.png"); /* cd0a0a for red */
}

.bigbuttons li a:hover {
	background-color: <?= $highlight ?>;
}
.bigbuttons li a:active {
	<?= shadow('1px','1px','2px') ?>
}

.bigbuttons li.disabled a, .bigbuttons li.disabled a:hover, .bigbuttons li.disabled a:active {
	<?= shadow('1px','1px','2px') ?>
	background-color: gray;
}

.bigbuttons li a .biginit {
	font-size: 4em;
	background-position: 50% 50%; 
	background-repeat: no-repeat;
}


/***** BUTTONS *****/

.buttons { 
    padding:1em 6px;
    text-align:center; 
    clear:both;
}

form > .buttons:first-child {
	padding-top: 0;
}


/*
.buttons input, .buttons a {
    border:<?= $border ?>;
    background-color:<?= $shade ?>;
    <?= shadow() ?>
    color:#000;
    text-align:center;
    margin:2px 0;
    <?= $round_corners ?>
    -moz-user-select: none; -webkit-user-select: none; -ms-user-select: none;
}

.buttons input:hover, .buttons input:active, .buttons input:focus, 
.buttons a:hover, .buttons a:active, .buttons a:focus {
    border-color:<?= $theme ?>;
    outline: none;
}
.buttons input:active, .buttons a:active {
	<?= shadow('1px','1px','2px') ?>
}
*/

img.loading { 
	width:200px;
	height:200px;
	background: url(/images/loaders/circle_column_theme) no-repeat;
}

div.themeloader {
	display: block;
	width: 200px;
	height: 200px;
	margin: .5em auto;
	background: transparent center center no-repeat url('/images/loaders/circle_column_theme');
}

.searchline {
	text-align: center;
}

.toolbar { margin-bottom: 1em; }
.toolbar-line { margin: .5em 0; }
.toolbar .ui-button span { 
	font-size: 90%;
	padding: 0 .5em; 
}

.toolbar .ui-button-icon-only span {
	padding: 0;
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

th {
	border-bottom: <?= $border ?>;
}

td, th { 
	padding: .25em; 
	vertical-align: top;
}

table.expTable { width: 90%; }

table.expTable td + td + td {
	text-align: right;
}

/***** jquery.tablesorter.js styles *****/

table.tablesorter thead tr .header {
	background-image: url(/images/icons/my/bg.gif);
	background-repeat: no-repeat;
	background-position: center right;
	padding-right:20px;
	cursor: pointer;
}

table.tablesorter thead tr .header + .header {
	border-left: <?= $border ?>;
}

table.tablesorter tbody td + td {
	border-left: 1px dotted hsl(<?= THEME_HUE ?>,25%,70%);
}

table.tablesorter thead tr .headerSortUp {
	background-image: url(/images/icons/my/asc.gif);
}
table.tablesorter thead tr .headerSortDown {
	background-image: url(/images/icons/my/desc.gif);
}
table.tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
	background-color: hsl(<?= THEME_HUE ?>,25%,80%);
	color: <?= $theme ?>;
}

tbody tr.odd, li.odd {
	background-color: hsl(<?= THEME_HUE ?>,25%,80%);
	/* border: 1px solid red; */
}

tbody tr.even, li.even {	
	background-color: hsl(<?= THEME_HUE ?>,25%,90%);
}

table.nostripe tbody tr.odd {
	background-color: transparent;
}

table.nostripe tbody tr.even {	
	background-color: transparent;
}

tbody tr.odd.emptyAlert {	
	background-color: hsl(60,90%,85%) !important;
}

tbody tr.even.emptyAlert {	
	background-color: hsl(60,90%,90%) !important;
}

.expTable tbody tr>td:first-child {
	padding-left: 28px; 
	background: 5px center no-repeat url(/images/icons/glyphish/mini/mini-theme/02-star);
}

.expTable tbody tr.done>td:first-child {
	background-image: url(/images/icons/glyphish/mini/mini-white/02-star);
}

tr.done, tr.done a:link, tr.done a:visited { color: hsl(<?= THEME_HUE ?>, 20%, 35%); }
tr.done a:hover { color: <?= $theme ?>; }
tr.done a:active { color: white; }

/***** FORMS *****/
form {
	width: 80%;
	max-width: 800px;
	margin: 1em auto;
}

table.questionnaire, table.query, table.fb_chart, table.tablesorter {
	border: <?= $border ?>;
	<?= shadow() ?>
	margin: 1em auto 5px auto;
	max-width: 795px;
	clear: both;
}

table.questionnaire td.input select {
	max-width: 400px;
}

.radiorow_options {
	background-color: <?= $theme ?> !important;
	color: <?= $text_on_theme ?>;
	font-size: smaller;
}

.radiorow_options th {
	padding: 5px;
	color: <?= $text_on_theme ?>;
}

tr + tr.radiorow_options th {
	border-top: <?= $border ?>;
	background-color: <?= $theme ?>;
	color: <?= $text_on_theme ?>;
}

.radiopage td + td {
	text-align: center;
	vertical-align: middle;
}

.radiopage td.question {
	width: 50%;
}

.instructions {
	font-size: 100%;
	max-width: 795px;
}

table.questionnaire td.question {
	padding-right: 1em;
	text-align: left;
}

input, select, textarea {
    border:1px dotted <?= $theme ?>;
}

tr.ranking {
	cursor: url(/images/icons/my/ns-move), move;
	-moz-user-select: none; -webkit-user-select: none; -ms-user-select: none;
}

td.handle {
	padding: .25em 1em;
	text-align: right;
}

input:focus, select:focus, textarea:focus {
	<?= shadow('2px', '2px', '4px') ?>
	border-color: hsl(0,100%,20%);
}

input[type=number] { text-align: right; }
input[type=search] { <?= roundCorners('1em') ?> }

ul.radio, ul.vertical_radio { margin:0;}

ul.radio li { display:inline; }

ul.radio li, ul.vertical_radio li { padding:0; }

img.radio {
	width: 25px; height: 25px;
	background: top left no-repeat url("/images/icons/my/radio_unselected");
}

.shade { background-color:<?= $shade ?> !important; }
.highlight { background: url(/images/icons/my/red_star.png) no-repeat 6px 3px; }
.highlight td.question { padding-left: 27px; }

.delete_icon {
	width:17px; height:17px;
	background: url(/images/icons/my/delete) no-repeat center center;
}

label {
	line-height:1.2;
}

.note, small { 
	font-size:10px; 
	display: block;
}

#footer small { display: inline; }

.note a {
	border-width: 1px;
}

.formlist {
    clear:both;
}
.formlist li { 
    clear:both;
    padding:3px;
}
.formlist li>label:first-child {
    float:left;
    text-align:right;
    width:35%;
}
.formlist li input, .formlist li select, .formlist li ul {
    float:right;
    text-align:left;
    width:60%;
    margin:0;
    padding:0;
}

.radiopage input[type=radio], .radioanchor input[type=radio] { display: none; }
.radiopage input[type=radio] + label:before,
.radioanchor input[type=radio] + label:before {
	content: "";
	display: inline-block; 
	width: 14px; height:14px; padding: 0;
	<?= roundCorners('7px') ?>
	background-color: transparent;
	box-shadow: inset 1px 1px 2px rgba(0,0,0,.8);
}
.radiopage input[type=radio] + label,
.radioanchor input[type=radio] + label { 
	display: inline-block; 
	width: 14px; height:14px; padding: 0;
	background-color: transparent; 
	border: 4px solid white; 
	<?= roundCorners('10px') ?>
	color: transparent; font-size: 14px; overflow: hidden;
	box-shadow: 1px 1px 2px rgba(0,0,0,.8);
}
.radiopage input[type=radio]:checked + label,
.radioanchor input[type=radio]:checked + label { background-color: hsl(200,100%, 30%); }

.radiopage input[type=radio] + label:hover,
.radioanchor input[type=radio] + label:hover { background-color: hsl(200,100%, 40%); }
.radiopage input[type=radio] + label:active,
.radioanchor input[type=radio] + label:active	{ box-shadow: 0 0 1px rgba(0,0,0,.5); }
	

.radiogroup, .checkboxgroup { list-style:none; }
.formlist li .checkboxgroup *, .formlist li .radiogroup * {
    float:none;
}

.radioanchor {
	width: 100%;
	margin: 0;
	padding: 0;
}

#low_anchor, #high_anchor {
	max-width: 10em;
	display: inline-block;
}

.buttonrow #low_anchor { text-align: right; }
.buttonrow #high_anchor { text-align: left; }

.radioanchor tr { background-color: transparent !important; }

img.radio {
	width: 25px; height:25px;
}

.radioanchor td, .radioanchor td+td { text-align: center; }

td.anchor { 
	width: 10em !important; 
	font-size: 80%;
}

.labnotes { font-size: 80%; }

.unsaved {
	background: hsl(55, 80%, 90%);
	border-color: yellow;
}

#help { display: none; }

.helpbutton {
	float: right;
	position: relative;
	left: -60px;
	width: 44px;
	height: 44px;
	text-align: center;
	font-size: 70%;
	line-height: 44px;
	border: 3px solid white;
	background-color: <?= $highlight ?>;
	<?= roundCorners('25px') ?>
	<?= shadow('2px','2px','4px') ?>
}

.helpbutton:hover, .helpbutton:active {
	background-color: hsl(<?= THEME_HUE ?>,100%,40%);
}

.helpbutton:active {
	<?= shadow('1px','1px','1px') ?>
}

#help ul { list-style: circle url("/images/icons/glyphish/mini/mini-theme/02-star") outside; }
#help ul li { padding-left: 0; margin-left: 1em;}

#graph_container {
	width: 90%; 
	max-width: 1000px;
	height: 500px; 
	margin: 1em auto;
}

/* old graph styles */

table.fb_chart { 
}

.fb_chart td { 
	vertical-align: bottom;
	text-align: center !important;
}

.fb_chart td img { 
	width: 50px; 
	vertical-align: bottom; 
}

.graph0 { background-color: #FFF; }
.graph1 { background-color: #C00; }
.graph2 { background-color: #CC7800; }
.graph3 { background-color: #FC0; }
.graph4 { background-color: #060; }
.graph5 { background-color: #06C; }
.graph6 { background-color: #00C; }
.graph7 { background-color: #609; }

/*-------------------------------------------------
PSYCHOMORPH STYLES
-------------------------------------------------*/

#taglist a { 
	padding: 0 3px; 
	cursor:pointer;
}

.tagcloud {
	font-size: 20px;
	overflow:auto;
	float:none;
	margin:20px auto 10px auto;
}

.tagcloud li {
	display: inline; 
	padding: 0; 
	height: 20px;
}

.tagcloud a {
	border: none; 
	padding: 0 2px;
}


/*-------------------------------------------------
EXPERIMENT STYLES
-------------------------------------------------*/

#experiment {
	margin-top: 1em;
	text-align: center;
}

#experiment table {
	margin: 0 auto;
	table-layout: fixed;
	width: 100%;
	max-width: 1200px;
}

#experiment table.xafc {
	max-width: none;
}

#experiment table tr {
	background: none;
}

#experiment #question {
	text-align: center;
	padding-bottom: .5em;
	font-size: 125%;
}

#continue_button input {
	display: block;
	margin: 1em auto;
}

#image_loader { text-align: center; }
#image_loader img { visibility: hidden; width: 1px; height: 1px;}
#image_loader img#loader { visibility: visible; width:300px; height:300px;}

.audio {
	width: 5em; 
	font-size: 300%;
	margin: 1em auto;
	-moz-user-select: none; -webkit-user-select: none; -ms-user-select: none;
	text-align: center; 
}
	
.audio span.play {
	height: 1.4em; 
	line-height: 1.4em;
	<?= roundCorners('3em') ?>
	border: 5px solid white; 
	color: white; 
	background-color: #004466; 
	box-shadow: 4px 4px 6px rgba(0,0,0,.5); 
	display: block; 
}
.audio span.play:active, .audio span.choose:active { box-shadow: 2px 2px 4px rgba(0,0,0,.5); }
.audio.played span.play { background-color: hsl(200,10%,50%); }
.audio.playing span.play { background-color: hsl(200,100%,30%); }
.audio span.choose { 
	display: block;
	font-size: 70%;
	width: 4em;
	margin: 0 auto;
	color: hsl(200,100%,30%);
	background-color: white;
	padding: 0 .5em;
	border: 3px solid hsl(200, 100%, 30%);
	border-bottom-left-radius: 1em; 
	border-bottom-right-radius: 1em; 
	box-shadow: 4px 4px 6px rgba(0,0,0,.5); 
}

table.jnd {
	/* border: 2px solid <?= $theme ?>; */
	<?= roundCorners('0') ?>
}

.jnd .input_interface td { 
	font-size: 90%; 
	border: 2px solid <?= $theme ?>; 
	width: 12.5% !important;
	min-height: 4em;
	vertical-align: middle;
	padding: .5em .25em;
	background-color: <?= $shade ?>;
	-moz-user-select: none; -webkit-user-select: none; -ms-user-select: none;
}

.jnd tr.exp_images td {
	border: 2px solid <?= $theme ?>; 
}

img#left_image { margin: 0 0 0 auto; }
img#right_image { margin: 0 auto 0 0; }
img#center_image, .jnd img#left_image, .jnd img#right_image { margin: 0 auto; }

.jnd .input_interface td:hover { 
	color: <?= $text_on_theme ?>;
	background-color: <?= $theme ?>; 
}

.buttons .input_interface input {
	font-size: 150%;
}

input.rating {
	font-size: 200%;
	width: 2.5em;
	text-align: center;
}

.exp_images td {
	/* border: 2px solid <?= $border_color ?>; */
	background-color: transparent;
	text-align: center;
}

.jnd .exp_images td {
	background-color: rgba(255,255,255,1);	
}

.exp_images td img {
	display: block;
	margin: 0 auto;
	max-width: 100%;
}

table.xafc tr.exp_images td img { 
	display: inline;
}

table.tafc tr.exp_images td img, table.xafc tr.exp_images td img {
	border: 2px solid white;
}

table.xafc tr.exp_images td img {
	min-width: 150px;
}

table.tafc tr.exp_images td img:hover, table.xafc tr.exp_images td img:hover {
	border: 2px solid <?= $theme ?>;
}

table.tafc tr.exp_images td#center_image img:hover {
	border: 2px solid white;
}

img.motivation {
	height: 400px; 
	display: inline-block;
}

#motivation-container {
	width: 450px; 
	margin: 0 auto;
}

table.motivation #spacebar {
	text-align: center; 
	line-height: 400px; 
}

table.motivation #countdown {
	height: 400px; 
	margin-right: 20px; 
	float: left;
}

table.motivation #countdownlabels {
	float: left;
	line-height: 200px;
}

table.motivation .ui-slider { width: 25px; }
table.motivation .ui-slider-handle { display: none; }

.motivation + .trialcounter { display: none; }

#feedback_averages {
	margin: 1em auto;
	width: 650px;
}
#feedback_averages img {
	width: 300px;
	height: 400px;
}

/*-------------------------------------------------
STUDENT SECTION
-------------------------------------------------*/

.navBar { 
	clear: both;
	width: 100%;
	text-align: center; 
	margin: 1em auto; 
}

.navBar li { display: inline; }

.navBar li.back { 
	background-repeat: no-repeat;
	background-position: left;
	background-image: url(/images/icons/my/arrow_left.png);
	padding: .25em .25em .25em 25px;
}

.navBar li.next { 
	background-repeat: no-repeat;
	background-position: right;
	background-image: url(/images/icons/my/arrow_right.png);
	padding: .25em 25px .25em .25em;
}

.navBar li.home a { 
	padding: .25em;
}

.lecture_menu {
	font-size: smaller;
	max-width: 60em;
	margin: 1em auto;
}

.lecture_menu h2 { 
	padding: 0 0 .25em 0; 
	margin: 0 0 .5em 0;
	font-size: 1em; 
	width: 100%;
}

.lecture_menu ul { margin: 0 1em; }

.lecture_menu .demos, .lecture_menu .downloads {
	width: 45%;
	float: left;
	margin-bottom: 1em;
	text-align: left;
	padding: 1em .5em;
}

.lecture_menu .downloads {
	float: none;
	margin: 0 auto 1em auto;
	/* float: right; */
}

.imgTable { 
	margin: 1em auto;
}

.right { float: right; margin: 1em 0 1em 1em; display: block; }

.left { float: left; margin: 1em 1em 1em 0; display: block; }

.imgTable th {
	border: none;
}

/*-------------------------------------------------
jQuery UI
-------------------------------------------------*/


/*!
 * jQuery UI CSS Framework 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Theming/API
 */

/* Layout helpers
----------------------------------*/
.ui-helper-hidden { display: none; }
.ui-helper-hidden-accessible { position: absolute !important; clip: rect(1px 1px 1px 1px); clip: rect(1px,1px,1px,1px); }
.ui-helper-reset { margin: 0; padding: 0; border: 0; outline: 0; line-height: 1.3; text-decoration: none; font-size: 100%; list-style: none; }
.ui-helper-clearfix:before, .ui-helper-clearfix:after { content: ""; display: table; }
.ui-helper-clearfix:after { clear: both; }
.ui-helper-clearfix { zoom: 1; }
.ui-helper-zfix { width: 100%; height: 100%; top: 0; left: 0; position: absolute; opacity: 0; filter:Alpha(Opacity=0); }


/* Interaction Cues
----------------------------------*/
.ui-state-disabled { cursor: default !important; }


/* Icons
----------------------------------*/

/* states and images */
.ui-icon { display: block; text-indent: -99999px; overflow: hidden; background-repeat: no-repeat; }


/* Misc visuals
----------------------------------*/

/* Overlays */
.ui-widget-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }


/*!
 * jQuery UI CSS Framework 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Theming/API
 *
 * To view and modify this theme, visit http://jqueryui.com/themeroller/?ffDefault="Lucida%20Grande",%20"Lucida%20Sans%20Unicode",%20"Lucida%20Sans",%20"Lucida",%20"Trebuchet%20MS",%20verdana,%20helvetica,%20arial,%20sans-serif&fwDefault=normal&fsDefault=1em&cornerRadius=4px&bgColorHeader=004466&bgTextureHeader=03_highlight_soft.png&bgImgOpacityHeader=0&borderColorHeader=ffffff&fcHeader=ffffff&iconColorHeader=ffffff&bgColorContent=e3e6e8&bgTextureContent=01_flat.png&bgImgOpacityContent=0&borderColorContent=004466&fcContent=222222&iconColorContent=004466&bgColorDefault=e3e6e8&bgTextureDefault=02_glass.png&bgImgOpacityDefault=75&borderColorDefault=004466&fcDefault=222222&iconColorDefault=004466&bgColorHover=004466&bgTextureHover=02_glass.png&bgImgOpacityHover=75&borderColorHover=004466&fcHover=ffffff&iconColorHover=ffffff&bgColorActive=004466&bgTextureActive=02_glass.png&bgImgOpacityActive=75&borderColorActive=004466&fcActive=ffffff&iconColorActive=ffffff&bgColorHighlight=fbf9ee&bgTextureHighlight=02_glass.png&bgImgOpacityHighlight=55&borderColorHighlight=fcefa1&fcHighlight=363636&iconColorHighlight=006699&bgColorError=fef1ec&bgTextureError=05_inset_soft.png&bgImgOpacityError=95&borderColorError=cd0a0a&fcError=cd0a0a&iconColorError=cd0a0a&bgColorOverlay=aaaaaa&bgTextureOverlay=08_diagonals_thick.png&bgImgOpacityOverlay=50&opacityOverlay=30&bgColorShadow=aaaaaa&bgTextureShadow=01_flat.png&bgImgOpacityShadow=0&opacityShadow=30&thicknessShadow=8px&offsetTopShadow=-8px&offsetLeftShadow=-8px&cornerRadiusShadow=8px
 */


/* Component containers
----------------------------------*/
.ui-widget,
.ui-widget .ui-widget,
.ui-widget input, 
.ui-widget select, 
.ui-widget textarea, 
.ui-widget button { font-size: 1em; }
/* .ui-widget-content { border: 1px solid #004466; background: #e3e6e8 url(images/ui-bg_flat_0_e3e6e8_40x100.png) 50% 50% repeat-x; color: #222222; } */
.ui-widget-content { border: 3px solid #004466; background: #e3e6e8 50% 50% repeat-x; color: #222222; }
.ui-widget-content a { color: #222222; }
.ui-widget-header { border: none; background: #004466 url(images/ui-bg_highlight-soft_0_004466_1x100.png) 50% 50% repeat-x; color: #ffffff; font-weight: bold; }
.ui-widget-header a { color: #ffffff; }

/* Interaction states
----------------------------------*/
.ui-state-default, 
.ui-widget-content .ui-state-default, 
.ui-widget-header .ui-state-default { 
	border: 3px solid #004466; 
	/* background: #e3e6e8 url(images/ui-bg_glass_75_e3e6e8_1x400.png) 50% 50% repeat-x; */
	background: #e3e6e8;
	font-weight: normal; 
	color: #222222; 
}
.ui-state-default a, 
.ui-state-default a:link, 
.ui-state-default a:visited { color: #222222; text-decoration: none; }

.ui-state-hover, 
.ui-widget-content .ui-state-hover, 
.ui-widget-header .ui-state-hover, 
.ui-state-focus, 
.ui-widget-content .ui-state-focus, 
.ui-widget-header .ui-state-focus { 
	border: 3px solid #ffffff; 
	/* background: #004466 url(images/ui-bg_glass_75_004466_1x400.png) 50% 50% repeat-x; */
	background: hsl(200,100%,30%);
	font-weight: normal; 
	color: #ffffff; 
}
.ui-state-hover a, .ui-state-hover a:hover { color: #ffffff; text-decoration: none; }

.ui-state-active, 
.ui-widget-content .ui-state-active, 
.ui-widget-header .ui-state-active { 
	border: 3px solid #004466; 
	/* background: #004466 url(images/ui-bg_glass_75_004466_1x400.png) 50% 50% repeat-x; */
	background: #004466;
	font-weight: normal; 
	color: #ffffff; 
}
.ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited { color: #ffffff; text-decoration: none; }

.ui-widget :active { outline: none; }

/* Interaction Cues
----------------------------------*/
.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight  {
	border: 3px solid #fcefa1; 
	/* background: #fbf9ee url(images/ui-bg_glass_55_fbf9ee_1x400.png) 50% 50% repeat-x; */
	background: #fbf9ee;
	color: #363636; 
}
.ui-state-highlight a, .ui-widget-content .ui-state-highlight a,.ui-widget-header .ui-state-highlight a { color: #363636; }
.ui-state-error, .ui-widget-content .ui-state-error, .ui-widget-header .ui-state-error {
	border: 1px solid #cd0a0a; 
	/* background: #fef1ec url(images/ui-bg_inset-soft_95_fef1ec_1x100.png) 50% bottom repeat-x; */
	background: #fef1ec;
	color: #cd0a0a; 
}
.ui-state-error a, .ui-widget-content .ui-state-error a, .ui-widget-header .ui-state-error a { color: #cd0a0a; }
.ui-state-error-text, .ui-widget-content .ui-state-error-text, .ui-widget-header .ui-state-error-text { color: #cd0a0a; }
.ui-priority-primary, .ui-widget-content .ui-priority-primary, .ui-widget-header .ui-priority-primary { font-weight: bold; }
.ui-priority-secondary, .ui-widget-content .ui-priority-secondary,  .ui-widget-header .ui-priority-secondary { opacity: .7; filter:Alpha(Opacity=70); font-weight: normal; }
.ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled { opacity: .35; filter:Alpha(Opacity=35); background-image: none; }

/* Icons
----------------------------------*/

/* states and images */
.ui-icon { width: 16px; height: 16px; background-image: url(images/ui-icons_004466_256x240.png); }
.ui-widget-content .ui-icon {background-image: url(images/ui-icons_004466_256x240.png); }
.ui-widget-header .ui-icon {background-image: url(images/ui-icons_ffffff_256x240.png); }
.ui-state-default .ui-icon { background-image: url(images/ui-icons_004466_256x240.png); }
.ui-state-hover .ui-icon, .ui-state-focus .ui-icon {background-image: url(images/ui-icons_ffffff_256x240.png); }
.ui-state-active .ui-icon {background-image: url(images/ui-icons_ffffff_256x240.png); }
.ui-state-highlight .ui-icon {background-image: url(images/ui-icons_006699_256x240.png); }
.ui-state-error .ui-icon, .ui-state-error-text .ui-icon {background-image: url(images/ui-icons_cd0a0a_256x240.png); }

/* positioning */
.ui-icon-carat-1-n { background-position: 0 0; }
.ui-icon-carat-1-ne { background-position: -16px 0; }
.ui-icon-carat-1-e { background-position: -32px 0; }
.ui-icon-carat-1-se { background-position: -48px 0; }
.ui-icon-carat-1-s { background-position: -64px 0; }
.ui-icon-carat-1-sw { background-position: -80px 0; }
.ui-icon-carat-1-w { background-position: -96px 0; }
.ui-icon-carat-1-nw { background-position: -112px 0; }
.ui-icon-carat-2-n-s { background-position: -128px 0; }
.ui-icon-carat-2-e-w { background-position: -144px 0; }
.ui-icon-triangle-1-n { background-position: 0 -16px; }
.ui-icon-triangle-1-ne { background-position: -16px -16px; }
.ui-icon-triangle-1-e { background-position: -32px -16px; }
.ui-icon-triangle-1-se { background-position: -48px -16px; }
.ui-icon-triangle-1-s { background-position: -64px -16px; }
.ui-icon-triangle-1-sw { background-position: -80px -16px; }
.ui-icon-triangle-1-w { background-position: -96px -16px; }
.ui-icon-triangle-1-nw { background-position: -112px -16px; }
.ui-icon-triangle-2-n-s { background-position: -128px -16px; }
.ui-icon-triangle-2-e-w { background-position: -144px -16px; }
.ui-icon-arrow-1-n { background-position: 0 -32px; }
.ui-icon-arrow-1-ne { background-position: -16px -32px; }
.ui-icon-arrow-1-e { background-position: -32px -32px; }
.ui-icon-arrow-1-se { background-position: -48px -32px; }
.ui-icon-arrow-1-s { background-position: -64px -32px; }
.ui-icon-arrow-1-sw { background-position: -80px -32px; }
.ui-icon-arrow-1-w { background-position: -96px -32px; }
.ui-icon-arrow-1-nw { background-position: -112px -32px; }
.ui-icon-arrow-2-n-s { background-position: -128px -32px; }
.ui-icon-arrow-2-ne-sw { background-position: -144px -32px; }
.ui-icon-arrow-2-e-w { background-position: -160px -32px; }
.ui-icon-arrow-2-se-nw { background-position: -176px -32px; }
.ui-icon-arrowstop-1-n { background-position: -192px -32px; }
.ui-icon-arrowstop-1-e { background-position: -208px -32px; }
.ui-icon-arrowstop-1-s { background-position: -224px -32px; }
.ui-icon-arrowstop-1-w { background-position: -240px -32px; }
.ui-icon-arrowthick-1-n { background-position: 0 -48px; }
.ui-icon-arrowthick-1-ne { background-position: -16px -48px; }
.ui-icon-arrowthick-1-e { background-position: -32px -48px; }
.ui-icon-arrowthick-1-se { background-position: -48px -48px; }
.ui-icon-arrowthick-1-s { background-position: -64px -48px; }
.ui-icon-arrowthick-1-sw { background-position: -80px -48px; }
.ui-icon-arrowthick-1-w { background-position: -96px -48px; }
.ui-icon-arrowthick-1-nw { background-position: -112px -48px; }
.ui-icon-arrowthick-2-n-s { background-position: -128px -48px; }
.ui-icon-arrowthick-2-ne-sw { background-position: -144px -48px; }
.ui-icon-arrowthick-2-e-w { background-position: -160px -48px; }
.ui-icon-arrowthick-2-se-nw { background-position: -176px -48px; }
.ui-icon-arrowthickstop-1-n { background-position: -192px -48px; }
.ui-icon-arrowthickstop-1-e { background-position: -208px -48px; }
.ui-icon-arrowthickstop-1-s { background-position: -224px -48px; }
.ui-icon-arrowthickstop-1-w { background-position: -240px -48px; }
.ui-icon-arrowreturnthick-1-w { background-position: 0 -64px; }
.ui-icon-arrowreturnthick-1-n { background-position: -16px -64px; }
.ui-icon-arrowreturnthick-1-e { background-position: -32px -64px; }
.ui-icon-arrowreturnthick-1-s { background-position: -48px -64px; }
.ui-icon-arrowreturn-1-w { background-position: -64px -64px; }
.ui-icon-arrowreturn-1-n { background-position: -80px -64px; }
.ui-icon-arrowreturn-1-e { background-position: -96px -64px; }
.ui-icon-arrowreturn-1-s { background-position: -112px -64px; }
.ui-icon-arrowrefresh-1-w { background-position: -128px -64px; }
.ui-icon-arrowrefresh-1-n { background-position: -144px -64px; }
.ui-icon-arrowrefresh-1-e { background-position: -160px -64px; }
.ui-icon-arrowrefresh-1-s { background-position: -176px -64px; }
.ui-icon-arrow-4 { background-position: 0 -80px; }
.ui-icon-arrow-4-diag { background-position: -16px -80px; }
.ui-icon-extlink { background-position: -32px -80px; }
.ui-icon-newwin { background-position: -48px -80px; }
.ui-icon-refresh { background-position: -64px -80px; }
.ui-icon-shuffle { background-position: -80px -80px; }
.ui-icon-transfer-e-w { background-position: -96px -80px; }
.ui-icon-transferthick-e-w { background-position: -112px -80px; }
.ui-icon-folder-collapsed { background-position: 0 -96px; }
.ui-icon-folder-open { background-position: -16px -96px; }
.ui-icon-document { background-position: -32px -96px; }
.ui-icon-document-b { background-position: -48px -96px; }
.ui-icon-note { background-position: -64px -96px; }
.ui-icon-mail-closed { background-position: -80px -96px; }
.ui-icon-mail-open { background-position: -96px -96px; }
.ui-icon-suitcase { background-position: -112px -96px; }
.ui-icon-comment { background-position: -128px -96px; }
.ui-icon-person { background-position: -144px -96px; }
.ui-icon-print { background-position: -160px -96px; }
.ui-icon-trash { background-position: -176px -96px; }
.ui-icon-locked { background-position: -192px -96px; }
.ui-icon-unlocked { background-position: -208px -96px; }
.ui-icon-bookmark { background-position: -224px -96px; }
.ui-icon-tag { background-position: -240px -96px; }
.ui-icon-home { background-position: 0 -112px; }
.ui-icon-flag { background-position: -16px -112px; }
.ui-icon-calendar { background-position: -32px -112px; }
.ui-icon-cart { background-position: -48px -112px; }
.ui-icon-pencil { background-position: -64px -112px; }
.ui-icon-clock { background-position: -80px -112px; }
.ui-icon-disk { background-position: -96px -112px; }
.ui-icon-calculator { background-position: -112px -112px; }
.ui-icon-zoomin { background-position: -128px -112px; }
.ui-icon-zoomout { background-position: -144px -112px; }
.ui-icon-search { background-position: -160px -112px; }
.ui-icon-wrench { background-position: -176px -112px; }
.ui-icon-gear { background-position: -192px -112px; }
.ui-icon-heart { background-position: -208px -112px; }
.ui-icon-star { background-position: -224px -112px; }
.ui-icon-link { background-position: -240px -112px; }
.ui-icon-cancel { background-position: 0 -128px; }
.ui-icon-plus { background-position: -16px -128px; }
.ui-icon-plusthick { background-position: -32px -128px; }
.ui-icon-minus { background-position: -48px -128px; }
.ui-icon-minusthick { background-position: -64px -128px; }
.ui-icon-close { background-position: -80px -128px; }
.ui-icon-closethick { background-position: -96px -128px; }
.ui-icon-key { background-position: -112px -128px; }
.ui-icon-lightbulb { background-position: -128px -128px; }
.ui-icon-scissors { background-position: -144px -128px; }
.ui-icon-clipboard { background-position: -160px -128px; }
.ui-icon-copy { background-position: -176px -128px; }
.ui-icon-contact { background-position: -192px -128px; }
.ui-icon-image { background-position: -208px -128px; }
.ui-icon-video { background-position: -224px -128px; }
.ui-icon-script { background-position: -240px -128px; }
.ui-icon-alert { background-position: 0 -144px; }
.ui-icon-info { background-position: -16px -144px; }
.ui-icon-notice { background-position: -32px -144px; }
.ui-icon-help { background-position: -48px -144px; }
.ui-icon-check { background-position: -64px -144px; }
.ui-icon-bullet { background-position: -80px -144px; }
.ui-icon-radio-off { background-position: -96px -144px; }
.ui-icon-radio-on { background-position: -112px -144px; }
.ui-icon-pin-w { background-position: -128px -144px; }
.ui-icon-pin-s { background-position: -144px -144px; }
.ui-icon-play { background-position: 0 -160px; }
.ui-icon-pause { background-position: -16px -160px; }
.ui-icon-seek-next { background-position: -32px -160px; }
.ui-icon-seek-prev { background-position: -48px -160px; }
.ui-icon-seek-end { background-position: -64px -160px; }
.ui-icon-seek-start { background-position: -80px -160px; }
/* ui-icon-seek-first is deprecated, use ui-icon-seek-start instead */
.ui-icon-seek-first { background-position: -80px -160px; }
.ui-icon-stop { background-position: -96px -160px; }
.ui-icon-eject { background-position: -112px -160px; }
.ui-icon-volume-off { background-position: -128px -160px; }
.ui-icon-volume-on { background-position: -144px -160px; }
.ui-icon-power { background-position: 0 -176px; }
.ui-icon-signal-diag { background-position: -16px -176px; }
.ui-icon-signal { background-position: -32px -176px; }
.ui-icon-battery-0 { background-position: -48px -176px; }
.ui-icon-battery-1 { background-position: -64px -176px; }
.ui-icon-battery-2 { background-position: -80px -176px; }
.ui-icon-battery-3 { background-position: -96px -176px; }
.ui-icon-circle-plus { background-position: 0 -192px; }
.ui-icon-circle-minus { background-position: -16px -192px; }
.ui-icon-circle-close { background-position: -32px -192px; }
.ui-icon-circle-triangle-e { background-position: -48px -192px; }
.ui-icon-circle-triangle-s { background-position: -64px -192px; }
.ui-icon-circle-triangle-w { background-position: -80px -192px; }
.ui-icon-circle-triangle-n { background-position: -96px -192px; }
.ui-icon-circle-arrow-e { background-position: -112px -192px; }
.ui-icon-circle-arrow-s { background-position: -128px -192px; }
.ui-icon-circle-arrow-w { background-position: -144px -192px; }
.ui-icon-circle-arrow-n { background-position: -160px -192px; }
.ui-icon-circle-zoomin { background-position: -176px -192px; }
.ui-icon-circle-zoomout { background-position: -192px -192px; }
.ui-icon-circle-check { background-position: -208px -192px; }
.ui-icon-circlesmall-plus { background-position: 0 -208px; }
.ui-icon-circlesmall-minus { background-position: -16px -208px; }
.ui-icon-circlesmall-close { background-position: -32px -208px; }
.ui-icon-squaresmall-plus { background-position: -48px -208px; }
.ui-icon-squaresmall-minus { background-position: -64px -208px; }
.ui-icon-squaresmall-close { background-position: -80px -208px; }
.ui-icon-grip-dotted-vertical { background-position: 0 -224px; }
.ui-icon-grip-dotted-horizontal { background-position: -16px -224px; }
.ui-icon-grip-solid-vertical { background-position: -32px -224px; }
.ui-icon-grip-solid-horizontal { background-position: -48px -224px; }
.ui-icon-gripsmall-diagonal-se { background-position: -64px -224px; }
.ui-icon-grip-diagonal-se { background-position: -80px -224px; }


/* Misc visuals
----------------------------------*/

/* Corner radius */
.ui-corner-all, .ui-corner-top, .ui-corner-left, .ui-corner-tl { -moz-border-radius-topleft: 1em; -webkit-border-top-left-radius: 1em; -khtml-border-top-left-radius: 1em; border-top-left-radius: 1em; }
.ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr { -moz-border-radius-topright: 1em; -webkit-border-top-right-radius: 1em; -khtml-border-top-right-radius: 1em; border-top-right-radius: 1em; }
.ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl { -moz-border-radius-bottomleft: 1em; -webkit-border-bottom-left-radius: 1em; -khtml-border-bottom-left-radius: 1em; border-bottom-left-radius: 1em; }
.ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br { -moz-border-radius-bottomright: 1em; -webkit-border-bottom-right-radius: 1em; -khtml-border-bottom-right-radius: 1em; border-bottom-right-radius: 1em; }

/* Overlays */
.ui-widget-overlay { background: #aaaaaa url(images/ui-bg_diagonals-thick_50_aaaaaa_40x40.png) 50% 50% repeat; opacity: .30;filter:Alpha(Opacity=30); }
.ui-widget-shadow { 
	margin: -8px 0 0 -8px; 
	padding: 8px; 
	background: #aaaaaa url(images/ui-bg_flat_0_aaaaaa_40x100.png) 50% 50% repeat-x; 
	opacity: .30;filter:Alpha(Opacity=30); 
	<?= roundCorners('8px') ?>
}

/*!
 * jQuery UI Resizable 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Resizable#theming
 */
.ui-resizable { position: relative;}
.ui-resizable-handle { position: absolute;font-size: 0.1px; display: block; }
.ui-resizable-disabled .ui-resizable-handle, .ui-resizable-autohide .ui-resizable-handle { display: none; }
.ui-resizable-n { cursor: n-resize; height: 7px; width: 100%; top: -5px; left: 0; }
.ui-resizable-s { cursor: s-resize; height: 7px; width: 100%; bottom: -5px; left: 0; }
.ui-resizable-e { cursor: e-resize; width: 7px; right: -5px; top: 0; height: 100%; }
.ui-resizable-w { cursor: w-resize; width: 7px; left: -5px; top: 0; height: 100%; }
.ui-resizable-se { cursor: se-resize; width: 12px; height: 12px; right: 1px; bottom: 1px; }
.ui-resizable-sw { cursor: sw-resize; width: 9px; height: 9px; left: -5px; bottom: -5px; }
.ui-resizable-nw { cursor: nw-resize; width: 9px; height: 9px; left: -5px; top: -5px; }
.ui-resizable-ne { cursor: ne-resize; width: 9px; height: 9px; right: -5px; top: -5px;}

/*!
 * jQuery UI Selectable 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Selectable#theming
 */
.ui-selectable-helper { position: absolute; z-index: 100; border:1px dotted black; }

/*!
 * jQuery UI Accordion 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Accordion#theming
 */
/* IE/Win - Fix animation bug - #4615 */
.ui-accordion { width: 100%; }
.ui-accordion .ui-accordion-header { cursor: pointer; position: relative; margin-top: 1px; zoom: 1; }
.ui-accordion .ui-accordion-li-fix { display: inline; }
.ui-accordion .ui-accordion-header-active { border-bottom: 0 !important; }
.ui-accordion .ui-accordion-header a { display: block; font-size: 1em; padding: .5em .5em .5em .7em; }
.ui-accordion-icons .ui-accordion-header a { padding-left: 2.2em; }
.ui-accordion .ui-accordion-header .ui-icon { position: absolute; left: .5em; top: 50%; margin-top: -8px; }
.ui-accordion .ui-accordion-content { padding: 1em 2.2em; border-top: 0; margin-top: -2px; position: relative; top: 1px; margin-bottom: 2px; overflow: auto; display: none; zoom: 1; }
.ui-accordion .ui-accordion-content-active { display: block; }

/*!
 * jQuery UI Autocomplete 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Autocomplete#theming
 */
.ui-autocomplete { position: absolute; cursor: default; }	

/* workarounds */
* html .ui-autocomplete { width:1px; } /* without this, the menu expands to 100% in IE6 */

/*
 * jQuery UI Menu 1.8.22
 *
 * Copyright 2010, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Menu#theming
 */
.ui-menu {
	list-style:none;
	padding: 2px;
	margin: 0;
	display:block;
	float: left;
}
.ui-menu .ui-menu {
	margin-top: -3px;
}
.ui-menu .ui-menu-item {
	margin:0;
	padding: 0;
	zoom: 1;
	float: left;
	clear: left;
	width: 100%;
}
.ui-menu .ui-menu-item a {
	text-decoration:none;
	display:block;
	padding:.2em .4em;
	line-height:1.5;
	zoom:1;
}
.ui-menu .ui-menu-item a.ui-state-hover,
.ui-menu .ui-menu-item a.ui-state-active {
	font-weight: normal;
	margin: -1px;
}
/*!
 * jQuery UI Button 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Button#theming
 * [LDB added box-shadows ]
 */
.ui-button { 
	display: inline-block; 
	position: relative; 
	padding: 0; 
	margin-right: .1em; 
	text-decoration: none !important; 
	cursor: pointer; 
	text-align: center; 
	zoom: 1; 
	overflow: visible; /* the overflow property removes extra width in IE */
	box-shadow: 2px 2px 4px rgba(0,0,0,.5); 
} 
.ui-button:active { box-shadow: 1px 1px 2px rgba(0,0,0,.5); }
.ui-button-icon-only { width: 1.6em; } /* to make room for the icon, a width needs to be set here */
button.ui-button-icon-only { width: 1.8em; } /* button elements seem to need a little more width */
.ui-button-icons-only { width: 3.4em; } 
button.ui-button-icons-only { width: 3.7em; } 

/*button text element [LDB halved padding] */ 
.ui-button .ui-button-text { display: block; line-height: 1.4; }
.ui-button-text-only .ui-button-text { padding: .2em .5em; }
.ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text { padding: .15em; text-indent: -9999999px; }
.ui-button-text-icon-primary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .2em .5em .2em 1.05em; }
.ui-button-text-icon-secondary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .2em 1.05em .2em .5em; }
.ui-button-text-icons .ui-button-text { padding-left: 1.05em; padding-right: 1.05em; }
/* no icon support for input elements, provide padding by default */
input.ui-button { padding: .2em .5em; }

/*button icon element(s) */
.ui-button-icon-only .ui-icon, .ui-button-text-icon-primary .ui-icon, .ui-button-text-icon-secondary .ui-icon, .ui-button-text-icons .ui-icon, .ui-button-icons-only .ui-icon { position: absolute; top: 50%; margin-top: -8px; }
.ui-button-icon-only .ui-icon { left: 50%; margin-left: -8px; }
.ui-button-text-icon-primary .ui-button-icon-primary, .ui-button-text-icons .ui-button-icon-primary, .ui-button-icons-only .ui-button-icon-primary { left: .5em; }
.ui-button-text-icon-secondary .ui-button-icon-secondary, .ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }
.ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }

/*button sets*/
.ui-buttonset { margin-right: 7px; }
.ui-buttonset .ui-button { margin-left: 0; margin-right: -.3em; }

/* workarounds */
button.ui-button::-moz-focus-inner { border: 0; padding: 0; } /* reset extra padding in Firefox */
/*!
 * jQuery UI Dialog 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Dialog#theming
 */
.ui-dialog { position: absolute; padding: .2em; width: 300px; overflow: hidden; }
.ui-dialog .ui-dialog-titlebar { padding: .4em 1em; position: relative; -moz-border-radius: 4px !important; -khtml-border-radius: 4px !important; -webkit-border-radius: 4px !important; border-radius: 4px !important; }
.ui-dialog .ui-dialog-title { float: left; margin: .1em 16px .1em 0; } 
.ui-dialog .ui-dialog-titlebar-close { position: absolute; right: .3em; top: 50%; width: 19px; margin: -10px 0 0 0; padding: 3px; height: 18px; }
.ui-dialog .ui-dialog-titlebar-close span { display: block; margin: 1px; }
.ui-dialog .ui-dialog-titlebar-close:hover, .ui-dialog .ui-dialog-titlebar-close:focus { padding: 0; }
.ui-dialog .ui-dialog-content { position: relative; border: 0; padding: .5em 1em; background: none; overflow: auto; zoom: 1; }
.ui-dialog .ui-dialog-buttonpane { text-align: left; border-width: 1px 0 0 0; background-image: none; margin: .5em 0 0 0; padding: .3em 1em .5em .4em; }
.ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset { float: right; }
.ui-dialog .ui-dialog-buttonpane button { margin: .5em .4em .5em 0; cursor: pointer; }
.ui-dialog .ui-resizable-se { width: 14px; height: 14px; right: 3px; bottom: 3px; }
.ui-draggable .ui-dialog-titlebar { cursor: move; }

.growl .ui-dialog-titlebar, .growl .ui-dialog-buttonpane, .growl .ui-resizable-se { display:none; }
.growl .ui-dialog { top: 20px; right: 20px; }
.ui-dialog { 
	border: 5px solid #004466; 
	<?= roundCorners('10px') ?>
	<?= shadow() ?>
}
.growl.ui-dialog:after {
	content: "[Click to close]"; 
	text-align: right;
	font-size: 80%;
}

/*!
 * jQuery UI Slider 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Slider#theming
 */
.ui-slider { position: relative; text-align: left; <?= roundCorners('10px') ?> } /* large border radii cause ui-slider-range to stick out top and bottom corners */
.ui-slider .ui-slider-handle { position: absolute; z-index: 2; width: 1.2em; height: 1.2em; cursor: default; }
.ui-slider .ui-slider-range { position: absolute; z-index: 1; font-size: .7em; display: block; border: 0; background-position: 0 0; }

.ui-slider-horizontal { height: .8em; }
.ui-slider-horizontal .ui-slider-handle { top: -.3em; margin-left: -.6em; }
.ui-slider-horizontal .ui-slider-range { top: 0; height: 100%; }
.ui-slider-horizontal .ui-slider-range-min { left: 0; }
.ui-slider-horizontal .ui-slider-range-max { right: 0; }

.ui-slider-vertical { width: .8em; height: 100px; }
.ui-slider-vertical .ui-slider-handle { left: -.3em; margin-left: 0; margin-bottom: -.6em; }
.ui-slider-vertical .ui-slider-range { left: 0; width: 100%; }
.ui-slider-vertical .ui-slider-range-min { bottom: 0; }
.ui-slider-vertical .ui-slider-range-max { top: 0; }/*!
 * jQuery UI Tabs 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Tabs#theming
 */
.ui-tabs { position: relative; padding: .2em; zoom: 1; } /* position: relative prevents IE scroll bug (element with position: relative inside container with overflow: auto appear as "fixed") */
.ui-tabs .ui-tabs-nav { margin: 0; padding: .2em .2em 0; }
.ui-tabs .ui-tabs-nav li { list-style: none; float: left; position: relative; top: 1px; margin: 0 .2em 1px 0; border-bottom: 0 !important; padding: 0; white-space: nowrap; }
.ui-tabs .ui-tabs-nav li a { float: left; padding: .5em 1em; text-decoration: none; }
.ui-tabs .ui-tabs-nav li.ui-tabs-selected { margin-bottom: 0; padding-bottom: 1px; }
.ui-tabs .ui-tabs-nav li.ui-tabs-selected a, .ui-tabs .ui-tabs-nav li.ui-state-disabled a, .ui-tabs .ui-tabs-nav li.ui-state-processing a { cursor: text; }
.ui-tabs .ui-tabs-nav li a, .ui-tabs.ui-tabs-collapsible .ui-tabs-nav li.ui-tabs-selected a { cursor: pointer; } /* first selector in group seems obsolete, but required to overcome bug in Opera applying cursor: text overall if defined elsewhere... */
.ui-tabs .ui-tabs-panel { display: block; border-width: 0; padding: 1em 1.4em; background: none; }
.ui-tabs .ui-tabs-hide { display: none !important; }
/*!
 * jQuery UI Datepicker 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Datepicker#theming
 */
.ui-datepicker { width: 17em; padding: .2em .2em 0; display: none; }
.ui-datepicker .ui-datepicker-header { position:relative; padding:.2em 0; }
.ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next { position:absolute; top: 2px; width: 1.8em; height: 1.8em; }
.ui-datepicker .ui-datepicker-prev-hover, .ui-datepicker .ui-datepicker-next-hover { top: 1px; }
.ui-datepicker .ui-datepicker-prev { left:2px; }
.ui-datepicker .ui-datepicker-next { right:2px; }
.ui-datepicker .ui-datepicker-prev-hover { left:1px; }
.ui-datepicker .ui-datepicker-next-hover { right:1px; }
.ui-datepicker .ui-datepicker-prev span, .ui-datepicker .ui-datepicker-next span { display: block; position: absolute; left: 50%; margin-left: -8px; top: 50%; margin-top: -8px;  }
.ui-datepicker .ui-datepicker-title { margin: 0 2.3em; line-height: 1.8em; text-align: center; }
.ui-datepicker .ui-datepicker-title select { font-size:1em; margin:1px 0; }
.ui-datepicker select.ui-datepicker-month-year {width: 100%;}
.ui-datepicker select.ui-datepicker-month, 
.ui-datepicker select.ui-datepicker-year { width: 49%;}
.ui-datepicker table {width: 100%; font-size: .9em; border-collapse: collapse; margin:0 0 .4em; }
.ui-datepicker th { padding: .7em .3em; text-align: center; font-weight: bold; border: 0;  }
.ui-datepicker td { border: 0; padding: 1px; }
.ui-datepicker td span, .ui-datepicker td a { display: block; padding: .2em; text-align: right; text-decoration: none; }
.ui-datepicker .ui-datepicker-buttonpane { background-image: none; margin: .7em 0 0 0; padding:0 .2em; border-left: 0; border-right: 0; border-bottom: 0; }
.ui-datepicker .ui-datepicker-buttonpane button { float: right; margin: .5em .2em .4em; cursor: pointer; padding: .2em .6em .3em .6em; width:auto; overflow:visible; }
.ui-datepicker .ui-datepicker-buttonpane button.ui-datepicker-current { float:left; }

/* with multiple calendars */
.ui-datepicker.ui-datepicker-multi { width:auto; }
.ui-datepicker-multi .ui-datepicker-group { float:left; }
.ui-datepicker-multi .ui-datepicker-group table { width:95%; margin:0 auto .4em; }
.ui-datepicker-multi-2 .ui-datepicker-group { width:50%; }
.ui-datepicker-multi-3 .ui-datepicker-group { width:33.3%; }
.ui-datepicker-multi-4 .ui-datepicker-group { width:25%; }
.ui-datepicker-multi .ui-datepicker-group-last .ui-datepicker-header { border-left-width:0; }
.ui-datepicker-multi .ui-datepicker-group-middle .ui-datepicker-header { border-left-width:0; }
.ui-datepicker-multi .ui-datepicker-buttonpane { clear:left; }
.ui-datepicker-row-break { clear:both; width:100%; font-size:0em; }

/* RTL support */
.ui-datepicker-rtl { direction: rtl; }
.ui-datepicker-rtl .ui-datepicker-prev { right: 2px; left: auto; }
.ui-datepicker-rtl .ui-datepicker-next { left: 2px; right: auto; }
.ui-datepicker-rtl .ui-datepicker-prev:hover { right: 1px; left: auto; }
.ui-datepicker-rtl .ui-datepicker-next:hover { left: 1px; right: auto; }
.ui-datepicker-rtl .ui-datepicker-buttonpane { clear:right; }
.ui-datepicker-rtl .ui-datepicker-buttonpane button { float: left; }
.ui-datepicker-rtl .ui-datepicker-buttonpane button.ui-datepicker-current { float:right; }
.ui-datepicker-rtl .ui-datepicker-group { float:right; }
.ui-datepicker-rtl .ui-datepicker-group-last .ui-datepicker-header { border-right-width:0; border-left-width:1px; }
.ui-datepicker-rtl .ui-datepicker-group-middle .ui-datepicker-header { border-right-width:0; border-left-width:1px; }

/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
.ui-datepicker-cover {
    position: absolute; /*must have*/
    z-index: -1; /*must have*/
    filter: mask(); /*must have*/
    top: -4px; /*must have*/
    left: -4px; /*must have*/
    width: 200px; /*must have*/
    height: 200px; /*must have*/
}/*!
 * jQuery UI Progressbar 1.8.22
 *
 * Copyright 2012, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Progressbar#theming
 */
.ui-progressbar { height:2em; text-align: left; overflow: hidden; }
.ui-progressbar .ui-progressbar-value {margin: -1px; height:100%; }

/*-------------------------------------------------
THEMEROLLER TWEAKS
-------------------------------------------------*/

.ui-state-error, .ui-state-highlight { padding: .25em .5em; }


/*-------------------------------------------------
MOBILE STYLES
-------------------------------------------------*/

/* @media screen and (max-device-width: 480px) { */
<?php if (MOBILE) { ?>
	body {
		font-family: Helvetica;
		-webkit-text-size-adjust:none;
	    font-size:14px; 
	    background-image: none;
    }
    
    body.logo {
    	background-image: none;
    }
    
    img { max-width: 100%; height: auto; }
    
	.nomenu #maincontent, #maincontent { 
		padding: 5px;
		min-height: 10px;
	}
	
	#content { right:0; }
	
	#contentmask { padding-bottom: 0; }
	
	#breadcrumb { padding-left: 5px; }
	
	#breadcrumb li { display: block; }
	
	.fb_profile_pic { 
		display: none; 
		left: 0;
	}
	
	.helpbutton {
		left: 0;
		top: 4em;
		margin-right: 5px;
		float: right;
	}
	
	ul#login_info {
		font-size: 100%;
		position: absolute;
		top: 10px;
		right: 0;
		padding-right: 0px;
	}
	
	#header {
		margin-bottom: 0;
	}
	
	#menu {
		display: block;
		position: relative;
		width: 100%;
		min-height: 10px;
		right: 0;
		min-width: none;
		padding: 0px;	
	}
	
	#menu ul {
		background-color: transparent;
		border: none;
		<?= roundCorners('0') ?>
		box-shadow: none;
		width: auto;
		margin: 0;
		padding: 0;
		font-size: 17px;
		margin: 5px;
	}
	
	#menu li {
		display: block;
		width: auto;
		color: black;
		background-image: none !important;
		padding: 0;
		text-align: center;
	}
	
	#menu ul li a, #menu ul li a:link, #menu ul li a:visited {
		display: block;
		width: auto;
		margin-bottom: -1px;
		padding: 12px 10px;
		color: black;
		background-color: white;
		border: 1px solid #999;
	}
	
	#menu ul li a:hover, #menu ul li a:active {
		border-bottom: 1px solid #999;
		background-color: <?= THEME ?>;
		color: white;
	}
	
	#menu ul li:first-child a {
		border-top-left-radius: 8px;
		border-top-right-radius: 8px;
	}
	#menu ul li:last-child a {
		border-bottom-left-radius: 8px;
		border-bottom-right-radius: 8px;
	}
	
	#footer {
		margin-top: 0;
		height: auto;
	}
	
	p {
		margin:.5em 0;
		line-height:1.3;
		max-width:100%;
	}
	
	.bigbuttons {
		margin: 1em 0;
	}
	
	.bigbuttons li {
		font-size: 70%;
		margin: 0 5px 5px 0;
	}
	
	.bigbuttons li a {
		<?= roundCorners('1.5em') ?>
	}
	
	form {
		margin: 0;
		width: 100%;
		min-width: 100%;
		max-width: 100%;
	}
	
	table.questionnaire, table.query, table.fb_chart, table.tablesorter {
		margin: 0;
		width: 100%;
		max-width: 100%;
		clear: both;
		border: none;
		<?= roundCorners('0') ?>
		box-shadow: none;
	}
	
	table.questionnaire td.input select, select {
		max-width: 100%;
	}
	
	tr.mobile_radiorow_div, tr.mobile_radiorow_div td { height: 0; padding: 0; margin: 0; }
	
	input { font-size: 120%; }
	
	dt { 
		display: block;
		float: none; 
		width: auto; 
		margin-left: 0;
		text-align: left; 
	}
	dd {
		display: block;
		margin: 0 0 0 1em;
	}
	
	.ui-button { font-size: 120%; }
	.ui-dialog { max-width: 100%; width: 95%; }
<?php } ?>