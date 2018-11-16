<?php
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__) )));

include_once("Zend/Loader/Autoloader.php");

$autoloader = Zend_Loader_Autoloader::getInstance();

$config = new Zend_Config_Json(APPLICATION_PATH . '/config.json',APPLICATION_ENV);

foreach ( $config->includePath as $path ) {
	set_include_path(implode(PATH_SEPARATOR, array(
			realpath(APPLICATION_PATH . "/" . $path),
			get_include_path(),
		)));
}


$runtimeConfig = new stdClass();
$runtimeConfig->libraryPath = APPLICATION_PATH . "/" . $config->libraryPath;
$runtimeConfig->modelsPath = APPLICATION_PATH . "/" . $config->modelsPath;

// DBPool - Database bootstraping and utils
include_once ( "DBPool.php" );
DBPool::init(new MSUtil_Mixin ( $runtimeConfig, $config ) );
