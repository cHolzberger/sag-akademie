<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class MosaikDatasource { ## a single datasource
	var $name;
	var $store;

	function __construct($name = "dbtable") {
		$this->name = $name;
		$this->store = array();
	}

	function set($list) {
		$this->store = $list;
	}

	function add($key, $value) {
		$this->store[$key] = $value;
	}

	function merge($srcds) {
		$this->store = array_merge ( $srcds->store, $this->store );
	}

	function dump() {
	    ob_start();
	    print_r($this->store);
	    $cnt = ob_get_contents();
	    ob_end_clean();

	    return "\n{$this->name}: \n".$cnt;
	}

	function log() {
		$GLOBALS['firephp']->info($this->store, $this->name);
	}

	function get($key,$default=null) {
		if ( -1 != strpos($key, ":") ) {
			$return = $this->store;
			$idx = explode(":",$key);
			foreach ($idx as $id) {
				if (is_array($return) && array_key_exists ($id, $return)) {
					$return = $return[$id];

				} else if ( $default != null){
					return $default;
				} else {

					//$GLOBALS['firephp']->warn("Key: " . $id . " not found");
					return "";
				}
			}
			//$GLOBALS['firephp']->log($key, "MosaikDatasource::get");

			return $return;
		} else {
				if (array_key_exists ($key, $this->store)) {
					//$GLOBALS['firephp']->log($key);
					return $this->store[$key];
				} else {
					//$GLOBALS['firephp']->warn("Key: " . $key . " not found");
					return "";
				}
		}
	}
}
