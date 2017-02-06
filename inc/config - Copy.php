<?php    

	// These two constants are used to create root-relative web addresses
    // and absolute server paths throughout all the code
	define("BASE_URL","/");
	define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"] . "/");

	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
	define("DB_OPTIONS", serialize($options));



    define("DB_HOST","localhost");
    define("DB_NAME","corrcheck");
    define("DB_USER","root");
    define("DB_PASS","");

	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    error_reporting(0);