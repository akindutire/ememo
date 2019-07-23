<?php
	
	include_once 'vendor/autoload.php';
	include_once 'zil/main.php';
	
		
	use zil\App;
	use src\Config;

	
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


