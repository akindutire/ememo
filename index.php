<?php
	
	include_once $_SERVER['DOCUMENT_ROOT']."/ememo/System/vendor/autoload.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/ememo/vendor/autoload.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/ememo/System/zil/main.php";
		
	use zil\App;
	use src\memoboard\config\Config;
	
	/**
	 * @params
	 * Event Log- true by default
	 */

	$AppSpace = new App(new Config(), true);

	/**
	 * @params
	 *  true - allow all | false - deny all
	 */
    
    	$AppSpace->start();



?>


