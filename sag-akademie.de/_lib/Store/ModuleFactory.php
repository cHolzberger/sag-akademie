<?php

class Store_ModuleFactory {
	/** this functions purpose is to make module loading / seeking cacheable
	 * it scans a dir for modules and returns an array of information about modules
	 *
	 * @param string $dirName
	 */
	function &loadModules($dirName) {
		// Ensure modules/ is on include_path
		set_include_path(implode(PATH_SEPARATOR, array(
			realpath($dirName),
			get_include_path(),
		)));

		$modules = array();
		$modulesDir = dir($dirName);

		while (false !== ( $directory = $modulesDir->read() )) {
			if ($directory == "." || $directory == ".." || $directory == ".svn" || !is_dir($modulesDir->path . "/" . $directory) || !is_file ($modulesDir->path . "/" . $directory . "/config.json") )
				continue;

			$directory = realpath($modulesDir->path . "/" . $directory);
			/** basename of this module * */
			$bn = basename($directory);
			/** namespace of this module * */
			$ns = $bn;

			$modules[$ns] = array(
				"namespace" => $ns,
				"basePath" => $directory,
				"config" => new Zend_Config_Json($directory . '/config.json', 'production', true)
			);
		}

		return $modules;
	}

	function setupModule(&$module) {
		// setup autoloading
		$autoloader = Zend_Loader_Autoloader::getInstance();

		$autoloader->registerNamespace($module['namespace'] . "_");
		// Add module to the autoloader
		$loader = new Zend_Application_Module_Autoloader($module);
		
		$classname = $module['config']->class;
		$instance = new $classname();
		$instance->setup($module['config']);
		
		return $instance;
	}
}
