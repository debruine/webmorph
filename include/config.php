<?php

/****************************************************
 * Configuration file
 ***************************************************/
	/*** Localisation ***/
	
	date_default_timezone_set('Europe/London');
	
	/*** set error reporting ***/
	
	error_reporting(E_ALL & ~E_NOTICE);
	
	ini_set('max_execution_time', 30); 	// maximum time in seconds a script is allowed to run before it is terminated by the parser
	ini_set('max_input_time', 60); 		//maximum time in seconds a script is allowed to parse input data (doesn't change)
	ini_set('max_input_variables', 1000); // doesn't change
	ini_set('memory_limit', '256M'); 	// must be larger than post_max_size, maximum amount of memory in bytes that a script is allowed to allocate
	ini_set('post_max_size', '128M'); 	// must be larger than upload_max_filesize
	ini_set('upload_max_filesize', '64M');
	
	/*** Constants ***/
	
	define('ENDLINE', "\n");
	define('ENDTAG', "\n\n");
	define('DS', DIRECTORY_SEPARATOR);	
	define('DIRPERMS', 0755);
	define('IMGPERMS', 0644);
	define('MYSQL_DB', 'psychomorph');
	
	define('JQUERY', "/include/js/jquery/jquery-2.1.4.min.js");
	define('JQUERYUI', '/include/js/jquery/jquery-ui-1.11.4.min.js');
	define('JQUERYUI_THEME' , '/include/css/jquery-ui-1.10.4.appley.php');
	
	/*** Server-specific settings ***/
	
	if ($_SERVER['SERVER_NAME'] == 'test.psychomorph' || $_SERVER['SERVER_NAME'] == 'webmorph.test') {
		define('DEBUG', true);
		define('IMAGEBASEDIR', '/Users/lisad/images/'); // . $id);
		define('MYSQL_HOST', '127.0.0.1');
		define('MYSQL_USER', 'root');
		define('MYSQL_PSWD', 'm0nKEY');
		define('TOMCAT', "/usr/local/tomcat/webapps/");
	} else {
		define('DEBUG', false);
		define('IMAGEBASEDIR', '/var/www/html/images/'); // . $id);
		define('MYSQL_HOST', 'db.psy.gla.ac.uk');
		define('MYSQL_USER', 'researcher');
		define('MYSQL_PSWD', 'scad00ba');
		define('TOMCAT', "/usr/share/apache-tomcat-7.0.27/webapps/");
	} 
	
	/*** user agent ***/
	define('MOBILE', false);

	/*** Validation Keys ***/
	define('GOOGLE_CLIENT_ID', '629587382604-9g1n2dprl7hfh0h24ama77kqc09m0qf8.apps.googleusercontent.com');
	define('GOOGLE_CLIENT_SECRET', 'x8gLb9GYx6ZxJgLiT-WtL-kb');

?>