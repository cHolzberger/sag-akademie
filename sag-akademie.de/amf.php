<?php
$starttime = microtime(true);
// set error reporting
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

/* set session lifetime */

ini_set("session.gc_maxlifetime", 7200); // in seconds // 2 Stunden
ini_set("session.cookie_lifetime", 7200 );
ini_set("session.save_path", dirname(__FILE__) . "/resources/sessions/");

// konstruct is twisting include data (autoloader)
// so include that file here!
//
ini_set('include_path',
  ini_get('include_path')
  .PATH_SEPARATOR.dirname(__FILE__)."/lib/controller"
  .PATH_SEPARATOR.dirname(__FILE__)."/lib"
   .PATH_SEPARATOR.dirname(__FILE__)."/lib/templates"
  .PATH_SEPARATOR.dirname(__FILE__)."/lib/konstrukt"
  .PATH_SEPARATOR.dirname(__FILE__)."/lib/Doctrine/models"
);

include_once('lib/std.inc.php');
/**
 * Init. Zend_AMF - Server
 **/
  include("Zend/Amf/Server.php");

/**
 * Application starts here
 **/
$server = new Zend_Amf_Server;
?>

