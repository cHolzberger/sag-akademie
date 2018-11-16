<?php

include_once("DatabaseService.php");

ini_set('include_path',
    ini_get('include_path')
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/controller"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/templates"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/konstrukt"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/Doctrine/models"
);


//include_once("lib/debug.php");
//include_once("lib/Mosaik.php");

//include ("lib/Doctrine.compiled.php");
/**
 * konfig
 */
class ConfigModel {
    public $name;
    public $nspace;
    public $value;

}

/**
 * FLEX Interface zu MosaikConfig::setPersisten und MosaikConfig::getPersistent
 */
class ConfigService extends DatabaseService {
/**
 * Liefert die persistente variable $name aus dem namespace $namespace zurueck
 * 
 * @param $nspace String
 * @param $name String
 * @return String
 */
    function getString($nspace, $name) {
	$o = new ConfigModel();
	$o->value = MosaikConfig::getPersistent(strval($nspace),strval($name),"");
	$o->name = $name;
	$o->nspace = $nspace;
	return $o;
    }
/**
 * setzt die persistente variable $name aus dem namespace $namespace auf wert $value
 *
 * @param $nspace String
 * @param $name String
 * @param $value String
 */
    function setString($nspace, $name, $value) {
	MosaikConfig::setPersistent($nspace,$name, $value);
    }
}
