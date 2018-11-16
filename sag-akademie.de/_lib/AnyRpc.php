<?php
 if ( !class_exists("Zend_Loader_Autoloader")) {
	require_once("Zend/Loader/Autoloader.php");
 }
define("ANYRPC_PATH", dirname(__FILE__));
include ( ANYRPC_PATH . "/AnyRpc/__compat.php");

class AnyRpc {
	static public $_config = null;
	static public $_env = array();
	static public $_factory = array();

	static function init($config) {
		AnyRpc::$_config = $config;
	}



	static function loadModules ( $dir ) {
		return MSUtil::_getSingleton("AnyRpc_ModuleFactory")->loadModules($dir);
	}

	static function setupModule ( &$module ) {
		return MSUtil::_getSingleton("AnyRpc_ModuleFactory")->setupModule($module);
	}

	static function get($name, $default = null) {
		return @isset(AnyRpc::$_env[$name]) ? AnyRpc::$_env[$name] : $default;
	}

	static function createServer ( $name, $mode, $target ) {
		AnyRpc::$_env['serverName'] = $name;
		AnyRpc::$_env['mode'] = $mode;
		AnyRpc::$_env['targetService'] = $target;
		return MSUtil::_getSingleton("AnyRpc_ServerFactory")->createServer($name, $mode);
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
}
spl_autoload_register(array("AnyRpc", "autoload"));