<?php
include_once("Zend/Loader/Autoloader.php");
include_once("lib/config.php");
ini_set('include_path',
 ini_get('include_path')
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/controller"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/PEAR"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/templates"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/konstrukt"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/db/models"
);
include_once('lib/std.inc.php');
include_once ("services/SystemService.php");

Doctrine::generateModelsFromDb(WEBROOT . '/lib/db/models/', array('doctrine'), array('generateTableClasses' => false));
