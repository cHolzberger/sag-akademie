<?php
/** 
 * Copyright 2011 by Christian Holzberger <ch@mosaik-software.de> - MOSAIK Software 
 * 
 */


class MSUtil_Env {
	/*
	 * @var MSUtil_Mixin
	 */
	private $_vars;
	private $_ns;
	private $_cachedir;

	function  __construct() {
		$null = null;
		$this->_vars = new MSUtil_Mixin( $null, $_POST, $_GET, $_ENV, $_SERVER );
		$this->_cachedir = dirname(dirname(dirname(__FILE__))) . "/resources/cache/envcache_";
	}

	function getArgument($name, $default=null) {
		$this->_vars->defaultValue = $default;
		
		return $this->_vars->$name;
	}
	
	function __get($name) {
		return $this->_vars->$name;
	}
	
	function get($name, $default) {
		return $this->_vars->$name != null ? $this->_vars->$name:$default;
	}
	
	function setCacheDir($dir) {
		$this->_cachedir = $dir;
	}
	
	function getCacheKey($name) {
		if ( $this->hasCachefile($name)) {
			return file_get_contents( $this->_cachedir . $name);
		} else {
			return "";
		}
	}
	
	function setCacheKey($name, $data) {
		file_put_contents($this->_cachedir . $name, $data);
	}
	
	function hasCacheKey ($name ) {
		file_exists( $this->_cachedir.$name);
	}
}