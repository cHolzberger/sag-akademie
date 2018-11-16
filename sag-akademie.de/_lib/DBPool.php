<?php

define("DBPOOL_PATH", dirname(dirname(__FILE__)) . "/");
if (!class_exists("Zend_Loader_Autoloader")) {
	require_once("Zend/Loader/Autoloader.php");
}
include_once("MSUtil.php");

class DBPool {

	static public $_config;
	static public $_bootstrap = null;
	static public $_includeCompiled = 'Doctrine.compiled.php';
	static public $_includeStandard = 'Doctrine.php';
	static public $cacheDriver;

	static function init(&$config) {
		self::$_config = $config;
		self::_import();

		if (self::$_bootstrap === null) {
			self::$_bootstrap = new DBPool_Bootstrap($config);
			self::$_bootstrap->init();
			self::$_bootstrap->connect();
		}
	}

	/** laed entwerde die compilierte oder die std version von doktrine
	 *
	 */
	static function _import() {

		if (file_exists(self::$_config->libraryPath . self::$_includeCompiled)) {
			include_once(self::$_config->libraryPath . self::$_includeCompiled);
		} else {
			include_once(self::$_config->libraryPath . self::$_includeStandard);
			$autoloader = Zend_Loader_Autoloader::getInstance();
			$autoloader->pushAutoloader(array('Doctrine', 'autoload'), 'Doctrine');
		}

		include_once(self::$_config->db->templatesPath . "/init.php");
		Doctrine::loadModels(DBPOOL_PATH . self::$_config->db->modelsPath . 'generated');
		Doctrine::loadModels(DBPOOL_PATH . self::$_config->db->modelsPath);
	}

	/**
	 * Loads the Class $class autodetects subfolders for pre php5.3 "namespaces"
	 * 
	 * @param type $class 
	 */
	static function autoload($class) {
		$fn = dirname(__FILE__) . "/" . str_replace("_", "/", $class) . ".php";

		if (class_exists($class)) {
			return true;
		} else if (file_exists($fn)) {
			include ( $fn );
			return class_exists($class);
		} else {
			return false;
		}
	}

}

spl_autoload_register(array("DBPool", "autoload"));
