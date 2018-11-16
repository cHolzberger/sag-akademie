<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class MosaikElementList { ## list of possible tags for html
	var $elements;
	var $datasourceList;
	var $hitcache;

	function __construct (&$datasourceList, $elements=false) {
	    if ( $elements ) {
		$this->elements = $elements;
	    } else {
		$this->elements=array();
	    }
		$this->datasourceList = &$datasourceList;
		$this->hitcache=array();
	}

	function has($name) {
		if ( array_key_exists($name, $this->hitcache)) return $this->hitcache[$name];

		foreach ( $this->elements as $element) {
			$ename = $element->getName();
			if ($ename == $name) {
				$this->hitcache[$name] = $element;
				return $element;
			}
		}
		return false;
	}

	function items() {
		return $this->elements;
	}

	function add($elem) {
		$elem->setDatasourceList($this->datasourceList);
		$this->elements[]=$elem;
	}

	function setDatasourceList(&$dsl) {
	    foreach ( $this->elements as $element) {
		$element->setDatasourceList($dsl);
	    }
	   $this->datasourceList = &$dsl;
	}
}
?>
