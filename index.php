<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

?><!DOCTYPE html>

<html lang="en">
<head>
    <title>WebMorph</title>
    <meta charset='utf-8'>
    <meta name='author' content='Lisa DeBruine and Bernard Tiddeman' />
    <meta name='Description' content='Online tools for manipulating faces by morphing and transforming.' />
    <meta name='keywords' content='webmorph,morphing,psychomorph,face research,faces,psychology,research,computer graphics' />
    <!--<meta name='verify-v1' content='oCEvWF1olBQ+/+nyyAZfRnSeVVGeEVlD0Qw8aHTRvAU=' />-->
    <meta property='og:site_name' content='WebMorph'/>
    <meta property='og:image' content='/include/images/logo.png'/>
    <meta name="viewport" id="vp" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />
    <meta name='apple-mobile-web-app-capable' content='yes' />
    <link rel='apple-touch-startup-image' href='/include/images/logo.png' />
    <link rel='apple-touch-startup-image' sizes='640x920' href='/include/images/logo@2x.png' />
    <meta name='apple-mobile-web-app-status-bar-style' content='black' />
    <link rel='shortcut icon' href='/include/images/favicon.ico' />
    <link rel='apple-touch-icon-precomposed' href='/include/images/apple-touch-icon-precomposed.png' />
    <link rel='stylesheet' type='text/css' href='<?= JQUERYUI_THEME ?>'>
    <link rel='stylesheet' type='text/css' href='/include/css/style.php' id='css'>
</head>

<!-- START BODY -->

<body class="loading">

<div data-role="page" id="page">

<!-- !menubar -->
<div data-role="header">
    <?php include 'include/sections/menubar.php'; ?>
</div><!-- end menu header -->

<!-- START CONTENT -->

<div data-role="content" id="content">

<!-- !- loginInterface -->
<?php include 'include/sections/login.php'; ?>

<!-- !- projectInterface -->
<?php include 'include/sections/projects.php'; ?>

<!-- !- finderInterface -->
<?php include 'include/sections/finder.php'; ?>

<!-- !- delineationInterface -->
<?php include 'include/sections/delineate.php'; ?>

<!-- !- averageInterface -->
<?php include 'include/sections/average.php'; ?>

<!-- !- transformInterface -->
<?php include 'include/sections/transform.php'; ?>
 
<!-- !- hidden dialog boxes -->
<?php include 'include/sections/dialogs.php'; ?>

<!-- !- help dialog -->
<?php include 'include/sections/help.php'; ?>

</div><!-- /content -->

<div data-role='footer'>
    <footer id='footer' data-persistent=''></footer>
</div>

</div><!-- /page -->

<!-- !Javascripts for this page -->

<script src='<?= JQUERY ?>'></script> 
<script src='<?= JQUERYUI ?>'></script>
<script src='/include/js/psychomorph/vars.js'></script>
<?php if (LOGGEDIN) echo "<script>PM.user.id = {$_SESSION['user_id']};</script>\n"; ?>

<!--<script src='/include/js/psychomorph/webmorph.min.js'></script>-->

<script src='/include/js/psychomorph/functions.js'></script>
<script src='/include/js/psychomorph/batch.func.js'></script>
<script src='/include/js/psychomorph/finder.func.js'></script>
<script src='/include/js/psychomorph/delin.func.js'></script>
<script src='/include/js/psychomorph/trans.func.js'></script>
<script src='/include/js/psychomorph/user.func.js'></script>
<script src='/include/js/psychomorph/page.js'></script>


<?php if (!DEBUG) {
/*
echo "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1815702-3', 'auto');
  ga('send', 'pageview');
</script>";
*/

echo "<script>
window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
ga('create', 'UA-1815702-3', 'auto');
ga('send', 'pageview');
</script>
<script async src='https://www.google-analytics.com/analytics.js'></script>";

} ?>
</body>
</html>