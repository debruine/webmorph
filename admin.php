<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

/****************************************************/
/* !Display Page */
/***************************************************/

if ($_SESSION['user_id'] != 1) { 
    header('Location: /');
    exit;
}

?><!DOCTYPE html>

<html xmlns:fb='http://www.facebook.com/2008/fbml' lang='en'>
<head>
    <title>WebMorph Admin</title>
    <meta charset='utf-8'>
    <meta name='author' content='Lisa DeBruine and Bernard Tiddeman' />
    <meta name='description' content='Online tools for manipulating faces' />
    <meta name='keywords' content='face research,faces,psychology,research,computer graphics,psychomorph' />
    <!--<meta name='verify-v1' content='oCEvWF1olBQ+/+nyyAZfRnSeVVGeEVlD0Qw8aHTRvAU=' />-->
    <meta property='og:site_name' content='Psychomorph'/>
    <meta property='og:image' content='/include/images/logo.png'/>
    <meta name="viewport" id="vp" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />
    <meta name='apple-mobile-web-app-capable' content='yes' />
    <link rel='apple-touch-startup-image' href='/include/images/logo.png' />
    <link rel='apple-touch-startup-image' sizes='640x920' href='/include/images/logo@2x.png' />
    <meta name='apple-mobile-web-app-status-bar-style' content='black' />
    <link rel='shortcut icon' href='/include/images/favicon.ico' />
    <link rel='apple-touch-icon-precomposed' href='/include/images/apple-touch-icon-precomposed.png' />
    <link rel='stylesheet' type='text/css' href='/include/css/style.css'>
    <link rel='stylesheet' type='text/css' href='<?= JQUERYUI_THEME ?>'>
    <link rel='stylesheet' type='text/css' href='/include/css/theme.php' id='themecss'>
</head>

<style>
</style>

<!-- START BODY -->

<body>
    
<h3>Users</h3>

    <div id="usertable">
<?php
    $q = new myQuery('SELECT user.id as ID, 
                             CONCAT(lastname, ", ", firstname) as Name, 
                             email as Email,
                             status,
                             DATE(regdate) as "Date Registered",
                             IF(status+0<3, "<span class=\"tinybutton\">AUTH</span>", "<span class=\"tinybutton\">DE-AUTH</span>") as Authorise
                      FROM user
                      ORDER BY status+0, regdate');
                echo $q->get_result_as_table();
?>
    </div>

<!-- !Javascripts for this page -->

<script src='<?= JQUERY ?>'></script> 
<script src='<?= JQUERYUI ?>'></script>

<script>
    $('span.tinybutton').click( function() {
        var authType = this.innerHTML == "AUTH" ? 'user' : 'disabled';
        var userID = $(this).closest('tr').find('td:eq(0)').text();
        $.ajax({
                url: 'scripts/userAuth',
                dataType: 'json',
                type: 'POST',
                data: { 
                    id: userID,
                    auth: authType 
                },
                success: function(data) {
                    location.reload();
                }
            });
    });
    $('#usertable tbody tr td:first-child').css('text-align', 'right');
    $('table tbody tr:odd').addClass('odd');
    $('table tbody tr:even').addClass('even');
</script>

</body>
</html>