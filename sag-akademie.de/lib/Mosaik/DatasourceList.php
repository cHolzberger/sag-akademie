<?php
/**
 * a list of datasources
 * 
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */
class MosaikDatasourceList { ## a list of datasources
	var $source;

	function __construct ( $source = false) {
	    if ( $source ) {
		$this->source = $source;
	    } else {
		$this->source = array ();
	    }
	}

	function dump() {
		$str = "";
		foreach ( $this->source as $l) {
			$str .= $l->dump();
		}
		return $str;
	}

	function log() {
		$GLOBALS['firephp']->group("MosaikDatasourceList");
		foreach ( $this->source as $l) {
			$l->log();
		}
		$GLOBALS['firephp']->groupEnd();
	}

	function add(&$ds) {
		$this->source[$ds->name] = &$ds;
	}

	function copyFrom ( $dsl ) {
		foreach ( $dsl->source as $key=>$item ) {
			$this->source[$key] = $item;
		}
	}

	function get($name, $key = false, $default = "") {
			if ($key and $name != null ) {
				return $this->source[$name]->get($key, $default);
			} else {
				//$GLOBALS['firephp']->log($name, "MosaikDatasourceList::get");
				return $this->source[$name];
			}
	}
}


?>
