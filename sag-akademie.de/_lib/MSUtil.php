<?php

define("MSUTIL_PATH", dirname(dirname(__FILE__)) . "/");

if (!class_exists("Zend_Loader_Autoloader")) {
	require_once("Zend/Loader/Autoloader.php");
}

class MSUtil {

	static public $_factory = array();

	static function &_getSingleton($name) {
		if (!array_key_exists($name, self::$_factory)) {
			self::$_factory[$name] = new $name( );
		}
		return self::$_factory[$name];
	}

		/**
	 * Loads the Class $class autodetects subfolders for pre php5.3 "namespaces"
	 * 
	 * @param type $class 
	 */
	static function autoload($class) {
		$fn = dirname(__FILE__) . "/" . str_replace("_", "/", $class) . ".php";

		if ( class_exists ($class)) {
			return true;
		} else if (file_exists($fn)) {
			include ( $fn );
			return class_exists($class);
		} else {
			return false;
		}
	}

	static function &getEnv() {
		return self::_getSingleton("MSUtil_Env");
	}

}

spl_autoload_register(array("MSUtil", "autoload"));