<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class MosaikRuntime {
	static public $path = array(); // to be used in the future

	static function getPath() {
		return $GLOBALS['path'];
		//return runtime::$path;
	}

	function addPath($name, $url) {
		$GLOBALS['path'][] =  array("name" => $name, "url" => $url);
		runtime::$path[] = array("name" => $name, "url" => $url);
	}
}

