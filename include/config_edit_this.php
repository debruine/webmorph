<?php

/****************************************************
 * Configuration file
 ***************************************************/
    define('DEBUG', false);
    
    /*** Localisation ***/
    
    date_default_timezone_set('Europe/London');
    
    /*** set error reporting ***/
    
    error_reporting(E_ALL & ~E_NOTICE);
    
    /*** settings ***/
    
    ini_set('max_execution_time', 30);  // maximum time in seconds a script is allowed to run before it is terminated by the parser
    ini_set('max_input_time', 60);      //maximum time in seconds a script is allowed to parse input data
    ini_set('max_input_variables', 1000); // maximum number of variables sent to a script
    ini_set('memory_limit', '256M');    // must be larger than post_max_size, maximum amount of memory in bytes that a script is allowed to allocate
    ini_set('post_max_size', '128M');   // must be larger than upload_max_filesize
    ini_set('upload_max_filesize', '64M');
    
    /*** Constants ***/
    
    define('DS', DIRECTORY_SEPARATOR);  
    define('DIRPERMS', 0755);
    define('IMGPERMS', 0644);
    
    define('JQUERY', "/include/js/jquery/jquery-2.1.4.min.js");
    define('JQUERYUI', '/include/js/jquery/jquery-ui-1.11.4.min.js');
    define('JQUERYUI_THEME' , '/include/css/jquery-ui-1.10.4.appley.php');
    
    /*** Server-specific settings ***/
    
    define('MYSQL_DB', 'psychomorph');
    define('IMAGEBASEDIR', '/Users/YOURUSERNAME/webmorph_images');
    define('MYSQL_HOST', '127.0.0.1');
    define('MYSQL_USER', 'root');
    define('MYSQL_PSWD', 'YOURPASSWORD');
    define('TOMCAT', "/usr/local/tomcat/webapps/");
    
    /*** user agent ***/
    define('MOBILE', false);

    /*** Validation Keys ***/
    define('GOOGLE_CLIENT_ID', 'my-client-id.apps.googleusercontent.com');
    define('GOOGLE_CLIENT_SECRET', 'my-secret-code');

?>