<?php

/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */
include_once ("lib/helpers.php");

class config { //compatibility

	static function isDebug($resource="") {
		return MosaikConfig::isDebug($resource);
	}

}

class MosaikConfig {

	static public $var = array();
	static public $debug = array();

	static function isDebug($resource = "") {
		if (array_key_exists($resource, MosaikConfig::$debug)) {
			return MosaikConfig::$debug[$resource];
		}
		return false;
	}

	static function setDebug($key) {
		MosaikConfig::$debug[$key] = true;
	}

	static function unsetDebug($key) {
		MosaikConfig::$debug[$key] = false;
	}

	static function setVar($name, $value) {
		MosaikConfig::$var[$name] = $value;
	}

	static function getVar($name) {
		if (array_key_exists($name, MosaikConfig::$var)) {
			return MosaikConfig::$var[$name];
		} return false;
	}

	static function getEnv($name, $default = null) {


		if (isset($_POST[$name])) {
            if ( $_POST[$name] === "false") return false;
			return $_POST[$name];
		} else if (isset($_GET[$name])) {

            if ( $_GET[$name] === "false") return false;

            return $_GET[$name];
		} else {
			return $default;
		}
	}

	static function logEnv() {
		qlog("GET:");
		qdir($_GET);
		qlog("POST:");
		qdir($_POST);
	}

	static function setPersistent($namespace, $name, $value) {
		qlog (__CLASS__."::" .__FUNCTION__ . ": $namespace/$name");
		//qdir($value);

		$r = Doctrine::getTable("XSettings")
			->findBySql("namespace = ? AND name = ?", array($namespace, $name))
			->getFirst();

		if (!is_object($r)) {
			qlog (__CLASS__."::" .__FUNCTION__ . ": $namespace/$name not found creating");

			$r = new XSettings();
		} else {
			qlog (__CLASS__."::" .__FUNCTION__ . ": $namespace/$name fetched");

		}
		$r->name = $name;
		$r->namespace = $namespace;
		$r->value = $value;
		$r->user_id = -1;
		$r->changed = currentMysqlDatetime();
		qlog (__CLASS__."::" .__FUNCTION__ . ": $namespace/$name saving");

		$r->save();
	}

	static function getPersistent($namespace, $name, $default=false) {
		$r = Doctrine::getTable("XSettings")->findBySql("namespace = ? AND name = ?", array($namespace, $name))->getFirst();
		if (!is_object($r)) {
			$r = new XSettings();
			$r->name = $name;
			$r->namespace = $namespace;
			$r->changed = currentMysqlDatetime();
			$r->value = $default;
			$r->save();
		}
		return $r->value;
	}

}

