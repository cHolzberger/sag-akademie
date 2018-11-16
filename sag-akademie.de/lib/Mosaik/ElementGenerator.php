<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class MosaikElementGenerator { ## generate elements from templates dir
	var $basepath;
	var $datasourceList;
	var $config;
	var $namespace = "";
	var $elements = null;
	static $elementList = array();

	function __construct($config, &$datasourceList, $namespace = "", &$el=null) {
		$this->config = $config;
		$this->namespace = $namespace;
		$this->datasourceList = &$datasourceList;

		if ( $el == null) {
			$this->elements = new MosaikElementList( $this->datasourceList );
		} else {
			$this->elements = &$el;
		}
	}
	/**
	 * returns the element list using caching
	 * @return MosaikElementList
	 */
	function getElementList() {
	    $path = $this->config->elementBasepath;

	    // check if apc is enabled and the element list ist not cached by now
	    if ( MosaikConfig::getVar("enableApc") && !isset( MosaikElementGenerator::$elementList[$path]) ) {
		$loaded = false;
		
		 $list = apc_fetch("el_".$path, $loaded);
		 if ( $loaded ) {
     		    MosaikDebug::msg($path, "Loading cached elements list");
		    MosaikElementGenerator::$elementList[$path] = $list;
		 }
	    } else if (MosaikConfig::getVar("enableMemcache")) {
		$m = new Memcache();
		   $m->addServer("localhost", 11211);
		   $list = $m->get("el_".$path);
		    if ( $list!==FALSE ) {
     		    MosaikDebug::msg($path, "Loading cached elements list");
		    MosaikElementGenerator::$elementList[$path] = $list;
		 }
	    }

	    // parse directory info and create tag elements
	    if (!isset( MosaikElementGenerator::$elementList[$path]) ) {
		MosaikDebug::msg("generating ElementList","MosaikPageReader");
		MosaikElementGenerator::$elementList[$path] = $this->loadElements();
		if ( MosaikConfig::getVar("enableApc") ) {
		    apc_add("el_" . $path, MosaikElementGenerator::$elementList[$path]);
		} else if (MosaikConfig::getVar("enableMemcache")) {
		    $m = new Memcache();
		    $m->addServer("localhost", 11211);
		    $m->set("el_" . $path, MosaikElementGenerator::$elementList[$path]);
		}
	    }

	    // update the data list and return the elementList
	    //MosaikElementGenerator::$elementList[$path]->setDatasourceList($this->datasourceList);
	    return MosaikElementGenerator::$elementList[$path];
	}

	/**
	 * creates an index of possible tags found in the taglib dir
	 * @return MosaikElementList
	 */
	function loadElements() {
		$path = $this->config->elementBasepath;

		if ( $this->namespace != "") {
			$ns = str_replace (":","/",$this->namespace);
			$path = $path . "/".$ns;
		}

		$path = realpath ( $path);
		//MosaikDebug::infoDebug("Scanning for elements in:" . $path . " namespace: " . $this->namespace);
		$queue = new MosaikObjectQueue("loadElements");

		$dir = dir($path);

		while ( $elem = $dir->read() ) {

			if ( $elem == "." || $elem == ".." || $elem[0] == ".") continue;
			$elemPath = realpath ($this->config->elementBasepath ."/". $elem);

			if (strstr ( $elem, ".php") ) {
				$name = str_replace (".php","", $elem);
				$elem = $this->basepath . $elem;
				//MosaikDebug::infoDebug("Adding " . $name . $elem);
				$this->elements->add( new MosaikElement($this->config, $name, $elem, $this->namespace ));
			} else if ( is_dir( $elemPath )) {
				$namespace = $elem;
				if ( $this->namespace != "") {
					$namespace = $this->namespace . ":" . $elem;
				}
				$queue->add( new MosaikElementGenerator ( $this->config, $this->datasourceList, $namespace , $this->elements));
			}
		}

		$dir->close();

		$queue->run();

		return $this->elements;
	}
}

?>
