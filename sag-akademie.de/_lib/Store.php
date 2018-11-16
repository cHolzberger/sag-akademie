<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

//include_once("Zend/Loader/Autoloader.php");
define("STORECOLLECTION_PATH", dirname(__FILE__));

class Store {
	static public $_config = null;
	static public $_env = array();
	static public $_factory = array();

	static function init($config) {
		self::$_config = $config;
	}

	static function loadModules ( $dir ) {
		return MSUtil::_getSingleton("Store_ModuleFactory")->loadModules($dir);
	}

	static function setupModule ( &$module ) {
		return MSUtil::_getSingleton("Store_ModuleFactory")->setupModule($module);
	}

	static function autoload ( $class ) {
		$basename = dirname ( __FILE__ );
		include ( $basename . "/".str_replace ("_", "/", $class) . ".php" );
	}
}
