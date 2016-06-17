<?php
    session_start();
    header("Content-Type: text/css");

/*-------------------------------------------------
PAGE COLORS FOR DYNAMIC STYLES
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
        $text = 'hsl(200,0%,80%)';          // very light grey
        $text_on_theme = 'hsl(200,0%,0%)';  // black
        $border_color = 'hsl(200,0%,10%)';  // very dark grey
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


body {
    color:<?= $text ?>;
    background-image: url(<?= $bgpattern ?>);
    background-color:<?= $theme ?>;
}

body.loading {
    background-color:<?= $theme ?>;
}

.ui-effects-transfer {
    border-color: <?= $theme ?>;
}

pre, code, .file, #imgname {
    color: <?= $theme ?>;
}

p {
    background-color:<?= $bgcolor?>;
    border-color:<?= $bgcolor?>;
    color:<?= $text ?>;
}


/***** HEADERS AND TEXT *****/

h1, h2, h3, h4, h5, h6 {
    color:<?= $theme ?>;
}
.default_button {
    border: <?= $border ?>;
    background-color: <?= $highlight ?>;
}

.feature {
    background-color:<?= $bgcolor?>;
    color:<?= $text ?>;
    border-color: <?= $border_color ?>;
}

strong {
    color:<?= $theme ?>;
}


/***** TEXT LINKS *****/
a:link, a:visited, a:hover, a:active, #menu a:focus {
    border-color:<?= $theme ?>;
}
a:link, a:visited { 
    color:<?= $theme ?>; 
}
a:hover, a:focus {
    color:<?= $highlight ?>;
}
a:active {
    background-color:<?= $theme ?>;
    color:<?= $text_on_theme ?>;
}
span.tinybutton, .tinybutton li, a.tinybutton, a.tinybutton:link, a.tinybutton:visited {
    color: <?= $text_on_theme ?> !important;
    background-color: <?= $theme ?>;
}
span.tinybutton:hover, .tinybutton li:hover, a.tinybutton:hover {
    background-color: <?= $highlight ?>;
}
span.tinybutton:active, .tinybutton li:active, a.tinybutton:active {
    background-color: <?= $highlight ?>;
}

/***** BASIC LISTS *****/
dd {
    color: <?= $text ?>;
}
#whatsnewDialog dt {
    color: <?= $theme ?>;
}
input, select, textarea {
    border-color: <?= $theme ?>;
}

input:focus, select:focus, textarea:focus {
    border-color: <?= $highlight ?>;
}

input[type=text]:focus, input[type=number]:focus, textarea:focus {
    background-color: <?= $bgcolor ?>;
    color: <?= $text ?>;
}

/***** TABLES *****/

thead, tfoot {
    background-color: <?= $theme ?>;
    color: <?= $text_on_theme ?>;
}

table.sortable thead th:hover {
    background-color: <?= $highlight ?>;
    color: <?= $text_on_theme ?>;
}

.odd {
    background-color: <?= $shade ?>;
    color: <?= $text ?>;
}
.even {
    background-color: <?= $bgcolor ?>;
    color: <?= $text ?>;
}

/***** PSYCHOMORPH *****/

.growl {
    border-color: <?= $theme ?>;
    background-color: <?= $bgcolor ?>;
    color: <?= $text ?>;
}

p.msg {
    border-color: <?= $theme ?>;
}

.progressBox {
    border-color: <?= $theme ?>;
}
.progressBar {
    /* background-color: <?= $theme ?>; */
}

#queue_n {
    color: <?= $text_on_theme ?>;
    background-color: <?= $theme ?>;
}

#loadingInterface.interface {
    color: <?= $theme ?>;
}

.hue_chooser, .rgb_chooser  {
    background-color: <?= $highlight ?>;
}
.hue_chooser .ui-slider-handle, .rgb_chooser .ui-slider-handle {
    background-color: <?= $border_color ?>;
    color: <?= $text_on_theme ?>;
}
#fm_delete {
    background-image: url("/include/images/finder/trash.php?h=<?= $theme_hue ?>");
}
#fm_delete.hover {
    background-image: url("/include/images/finder/trash_open.php?h=<?= $theme_hue ?>");
}
#webcam, #webcamvas {
    border-color: <?= $theme ?>;
}
#continuum {
    border-bottom: <?= $border ?>;
    border-top: <?= $border ?>;
}

#continuum:hover {
    background-color: <?= $text_on_theme ?>;
    color: <?= $theme ?>;
}

.batch_name span { 
    border-color: <?= $theme ?>; 
}


/* MENUBAR STYLES */

.menucategory:hover #currentTem_name,
.menubar .menucategory:hover > span,
.menubar .menucategory ul li:hover,
.menubar .menucategory:hover > span.shortcut,
.menubar .menucategory li:hover a,
.menubar .menucategory a:hover,
.menubar .menucategory a:active,
.context_menu li:hover {
    background-color: <?= $theme ?>;
    color: <?= $text_on_theme ?>;
}

.menubar .menucategory ul li.disabled:hover {
	background-color:<?= $theme ?>!important;
}

.menubar .menucategory ul li:hover .submenu {
    color: <?= $text ?>;
}

#delin {
    background-image: url(<?= $bgpattern ?>);
}

.toolbar {
    border-color: <?= $theme ?>;
}

/* FINDER STYLES */

#finder {
    background-color: <?= $border_color ?>;
    border-color: <?= $text ?>;
}

#finder li, #finder li:hover, .filehelper li {
    background-color: <?= $highlight ?>;
    background-image: url("/include/images/finder/folder.php?h=<?= $theme_hue ?>"), url("/include/images/finder/folder_arrow_white.svg");
    color: <?= $text_on_theme ?>;
}
#finder li.folder.closed {
    background-image: url("/include/images/finder/folder.php?h=<?= $theme_hue ?>"), url("/include/images/finder/folder_arrow_grey.svg");
    color: <?= $text ?>;
}
#finder li.folder.selected, #finder li.folder.closed.selected {
    background-color: <?= $highlight ?>;
    background-image: url("/include/images/finder/folder.php?h=<?= $theme_hue ?>"), url("/include/images/finder/folder_arrow_white.svg");
    color: <?= $text_on_theme ?>;
}
#finder li.file:hover {
    background-color: <?= $shade ?>;
    color: <?= $text ?>;
}
#finder li.folder.closed ul {display: none;}
#finder li.file, .filehelper li {
    background-image: url("/include/images/finder/imgicon.php?h=<?= $theme_hue ?>");
    color: <?= $text ?>;
}
#finder li.folder#trash {
    background-image: url("/include/images/finder/trash.php?h=<?= $theme_hue ?>"), url("/include/images/finder/folder_arrow_grey.svg");
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
#finder li.file.selected, #average-list li.selected {
    background-color: <?= $highlight ?>;
    color: <?= $text_on_theme ?>;
}

#finder li.file.ui-selecting, #finder li.file.selected.ui-selecting  {
    background-color: <?= $highlight ?>;
    color: <?= $text ?>;
}

#finder li.folder.folderDrop > span, .filehelper li span {
    background-color: <?= $theme ?>;
    color: <?= $text_on_theme ?>;
}

#finder li.folder ul.folderDrop {
    box-shadow: 0 0 5px <?= $theme ?> inset;
}

#finder.image-view li.file.selected{
    background-color: <?= $highlight ?>;
}
#finder.image-view li.folder {
    background-image: url("/include/images/finder/folder.php?h=<?= $theme_hue ?>");
}

#finder.image-view li.selected > span, #finder.image-view li.folder:not(.closed) > span {
    color: <?= $text ?>;
}

#help h1 {
    color: <?= $text_on_theme ?>;
    background-color: <?= $theme ?>;
}

#help h2, #help h3 {
    border-color: <?= $theme ?>;
}

.tinyhelp {
    color: <?= $theme ?>;
    background-color: <?= $border_color ?>;
}

.tinyhelp:hover {
    color: <?= $text_on_theme ?>;
    background-color: <?= $highlight ?>;
}

/* IMAGEBOX */

#selectedImage {
    background-image: url(<?= $bgpattern ?>);
}
.chosen, #cropBox, #project_list {
    background-color: <?= $bgcolor ?>;
}
#average {
    background-image: url("/include/images/blankface.php?h=<?= $theme_hue ?>");
}

#destimages img, #grid img, .hoverdrag, #destimages li.hoverdrag, #custom_mask, #project_list {
    border-color: <?= $theme ?>;
}


/**************************** START OS X BUTTON STYLE **********************************/
/******************** http://jsfiddle.net/reitermarkus/xLQ2F/ **************************/

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
    background: -webkit-linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: -moz-linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: -o-linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: -ms-linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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

    border-top-color:       hsl(<?= ($hue + 20)%360 ?>,35.7%,49.4%);
    border-bottom-color:    hsl(<?= ($hue + 22)%360 ?>,23.9%,36.1%);
    border-left-color:      hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%);
    border-right-color:     hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%);
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
    background: -webkit-linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: -moz-linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: -o-linear-gradient(top
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: -ms-linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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
    background: linear-gradient(top,
        hsl(<?= ($hue - 7.0)%360 ?>,61.4%,83.7%)  0%,
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

    border-top-color:       hsl(<?= ($hue + 20)%360 ?>,35.7%,49.4%);
    border-bottom-color:    hsl(<?= ($hue + 22)%360 ?>,23.9%,36.1%);
    border-left-color:      hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%);
    border-right-color:     hsl(<?= ($hue + 23)%360 ?>,30.2%,42.2%);
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
    color: <?= $text_on_theme ?>;
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
        box-shadow:         0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
    }
    5%,
    95% {
        box-shadow:         0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>,
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
        box-shadow:        0 -0.830em 0 <?= $c1 ?>em <?= $theme ?>,
                    -0.377em -0.740em 0 <?= $c2 ?>em <?= $theme ?>,
                    -0.645em -0.522em 0 <?= $c3 ?>em <?= $theme ?>,
                    -0.775em -0.297em 0 <?= $c4 ?>em <?= $theme ?>,
                    -0.820em -0.090em 0 <?= $c5 ?>em <?= $theme ?>;
    }
    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
        box-shadow:         0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>,
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
        box-shadow:         0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
    }
    5%,
    95% {
        box-shadow:         0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>,
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
        box-shadow:        0 -0.830em 0 <?= $c1 ?>em <?= $theme ?>,
                    -0.377em -0.740em 0 <?= $c2 ?>em <?= $theme ?>,
                    -0.645em -0.522em 0 <?= $c3 ?>em <?= $theme ?>,
                    -0.775em -0.297em 0 <?= $c4 ?>em <?= $theme ?>,
                    -0.820em -0.090em 0 <?= $c5 ?>em <?= $theme ?>;
    }
    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
        box-shadow:         0 -0.83em 0 <?= $c1 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c2 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c3 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c4 ?>em <?= $theme ?>,
                            0 -0.83em 0 <?= $c5 ?>em <?= $theme ?>;
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
    background-color: <?= $border_color ?>;
}
