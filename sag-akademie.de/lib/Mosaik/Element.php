<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class MosaikElement { ## a single element
    var $name;
    var $namespace;
    var $filename;
    var $datasourceList;
    var $config;
    var $ignoreChildren;

    function __construct($config, $name, $filename, $namespace="") {
	$this->config = $config;
	$this->name=$name;
	$this->filename = $filename;
	$this->ignoreChildren = false;
	$this->namespace = $namespace;
	$this->init();

    }

    function setNamespace($ns) {
	$this->namespace = $ns;
    }

    function setDatasourceList($dsl) {
	$this->datasourceList = $dsl;
    }

    function getName() {
	if ( !empty ($this->namespace) ) return $this->namespace . ":" . $this->name;
	return $this->name;
    }

    function getFn () {
	$path = $this->config->elementBasepath . "/";

	if ( $this->namespace != "") {
	    $ns = str_replace (":","/",$this->namespace);
	    $path = $path . $ns . "/";
	}

	$path = $path . $this->filename;
	return $path;
    }

    function init() {
    }
    // TODO map $this[""] to attribute values and defaults
    function source($attributes, $value) {
	setTagname($this->name);

	global $firephp;
	if ( ! is_array($attributes) && !is_object($attributes) ) {
	    $attributes = array();
	}
	$attribute = $attributes;
	#FIXME: parse attributes for auto php stuff
	#this is run inside PHP!
	#$firephp->dump("datasource", $this->datasourceList);
	$dsl = &$this->datasourceList;
	$datasourceList = &$this->datasourceList;
	//$dsl->log();

	if (isset ( $attributes['datasource'] ) ) {
	    $ds = $datasource = $dsl->get( $attributes['datasource'] );
	}

	/* code caching functions */
	$isCached = false;
	$code = "";
	$argumentlist = '&$dsl, &$datasourceList, &$ds, &$attribute, &$attributes, &$value';


	if ( MosaikConfig::getVar("enableApc") ) {
	    $code = apc_fetch ( $this->getFn() , $isCached);
	} else if ( MosaikConfig::getVar("enableMemcached")) {
	    $m = new Memcache();
	    $m->addServer("localhost", 11211);
	    $code = $m->get($this->getFn());
	    if ( $code === FALSE) $isCached=false;
	    else $isCached=true;
	}

	if (  MosaikConfig::getVar("enableApc") && $isCached) {
	//    MosaikDebug::msg($this->getFn(), "Using APC");
	    $func = create_function($argumentlist, $code);

	    ob_start();
	    $func ($dsl, $datasourceList, $ds, $attributes, $attribute, $value);
	    $content = ob_get_contents();
	    ob_end_clean();

	} else if ( file_exists ( $this->getFn() )) {
		if ( MosaikConfig::getVar("enableApc")) MosaikDebug::msg($this->getFn(), "Generating APC");
		$code = '?> ';
		$code .= file_get_contents($this->getFn());
		$code .='<?';

		$func = create_function($argumentlist, $code);
		if ( MosaikConfig::getVar("enableApc")) apc_add( $this->getFn(), $code);
		else if (MosaikConfig::getVar("enableMemcache")) {
		    $m = new Memcache();
		    $m->addServer("localhost", 11211);
		    $m->set($this->getFn(), $code);
		}

		ob_start();
		$func($dsl, $datasourceList, $ds, $attributes, $attribute, $value);
		$content = ob_get_contents();
		ob_end_clean();
	    } else {
		$content = "missing file: " . $this->getFn();
	    }

	return $content;
    }
}

?>
