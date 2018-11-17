<?php

// konstruct is twisting include data (autoloader)
// so include that file here!
//
ob_start();
if (!defined("E_DEPRECATED"))
	define("E_DEPRECATED", 8192);

include_once("Zend/Loader/Autoloader.php");
ini_set('display_errors','Off');
ini_set("html_errors", "Off");
// path for cookies
$cookie_path = "/";

// timeout value for the cookie
$cookie_timeout = 60 * 60 * 1000; // in seconds
$garbage_timeout = $cookie_timeout + 600;
session_name("SAGAkademieWebsession");
session_set_cookie_params($cookie_timeout, $cookie_path);

ini_set("session.gc_maxlifetime", $garbage_timeout); // in seconds // 2 Stunden
//ini_set("session.save_path", dirname(__FILE__) . "/resources/sessions/");

/* set session lifetime */
session_cache_expire($cookie_timeout * 40);

/* keepalive bugfix */
$uri = $_SERVER['REQUEST_URI'];
if (substr_count($uri, "_keepalive")) {
	include("./_keepalive.php");
	exit(0);
}

/* clear the apc cache on request */
if (isset($_GET['clearApc'])) {
	apc_clear_cache();
	apc_clear_cache("user");
}
if (isset($_GET['clearMemcache'])) {
	$m = new Memcache();
	$m->addServer("127.0.0.1", "11211");
	$m->flush();
}

$starttime = microtime(true);
// set error reporting
// Report simple running errors
#error_reporting(E_ERROR | E_WARNING | E_PARSE & ~E_NOTICE & ~E_DEPRECATED);
// konstruct is twisting include data (autoloader)
// so include that file here!
//
ini_set('include_path',
 ini_get('include_path')
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/controller"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/PEAR"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/templates"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/konstrukt"
 . PATH_SEPARATOR . dirname(__FILE__) . "/lib/db/models"
);

function setHttpFilename($name) {
	$GLOBALS['attachment_filename'] = $name;
}

$GLOBALS['http_attachment'] = true;

function setHttpAttachment($name) {
	$GLOBALS['http_attachment'] = $name;
}

$GLOBALS["attachment_type"] = "application/download";

function setHttpContentType($name) {
	$GLOBALS['attachment_type'] = $name;
}

function getRequestType() {
	$uri = $_SERVER['REQUEST_URI'];
	if (substr_count($uri, ";json") > 0) {
		return "json";
	} else if (substr_count($uri, ";pdf") > 0) {
		return "pdf";
	} else if (substr_count($uri, ";csv") > 0) {
		return "csv";
	} else if (substr_count($uri, ";download") > 0) {
		return "download";
	} else if (substr_count($uri, ";doc") > 0) {
		return "doc";
	}
	return "html";
}



include_once('lib/std.inc.php');
include_once ("services/SystemService.php");

include("_lib/Identity.php");



/**
 * Application starts here
 * */
/* * * UPDATE DER SEMINAR NR * */
SystemService::onStart();
// NEW IDENTITY MANAGEMENT
global $identity;
$identity = Identity::create();
@$session = (object) $_SESSION;
$identity->authenticate (new MSUtil_Mixin ( $session, (object)$_POST, (object)$_GET ));


Doctrine::getTable("Seminar")->updateKursNr();

MosaikDebug::infoGroup("Root");
MosaikDebug::infoDebug("Creating Component Creator");
$htmlCc = new SAG_ComponentCreator();
MosaikDebug::infoDebug("Initializing Contruct");
$k = k();

$k->setLog(dirname(__FILE__) . '/resources/log/debug-' . date("Ymd") . '.log');

if (MosaikConfig::isDebug("konstrukt")) {
	$k->setLog(dirname(__FILE__) . '/resources/log/debug.log');
	$k->setDebug();
}

if (count($_FILES) != 0) {
	MosaikDebug::msg($_FILES, "_FILES");
}


// NEW IDENTITY MANAGEMENT ENDE

$k->setIdentityLoader(getIdentityLoader());
$k->setComponentCreator($htmlCc);
MosaikDebug::infoDebug("RunningKonstruct");
MosaikDebug::infoGroupEnd();

if (false && count($_POST) > 0) {
	if ( strstr ($_SERVER['REQUEST_URI'], "admin") ===false ) { // admin post info nicht loggen!
		$post = new PostInfo();
		$post->data = serialize($_POST);
		
		$post->url = $_SERVER['REQUEST_URI'];
		$post->save();
	}
}

$resp = $k->run('SAG_Home'); // k_httpResponse from now on

$resp->setHeader("Expires", "now");
$resp->setHeader("Last-Modified", gmdate('D, d M Y H:i:s') . ' GMT');

switch (getRequestType ()) {
	case "json":
		$resp->setContentType("application/json");
		MosaikDebug::msg("json", "Content-Type");
		break;
	case "pdf":
		//if ($GLOBALS['http_attachment']) {
		//$resp->setHeader("Content-Disposition", "attachment; filename=" . $GLOBALS['attachment_filename']);
		//}
		$resp->setHeader("Content-Transfer-Encoding", "binary");
		$resp->setContentType("application/pdf");
	
		qlog("Content-Type: pdf");
		break;
	
	case "csv":
		$resp->setHeader("Content-Disposition", "attachment; filename=" . $GLOBALS['attachment_filename']);
		$resp->setHeader("Content-Transfer-Encoding", "binary");
		$resp->setContentType("application/csv");
		
		MosaikDebug::msg("pdf", "Content-Type");
		break;
	case "download":

		if ($GLOBALS['http_attachment']) {
			$resp->setHeader("Content-Disposition", "attachment; filename=" . $GLOBALS['attachment_filename']);
		}
		$resp->setHeader("Content-Transfer-Encoding", "binary");
		$resp->setContentType($GLOBALS['attachment_type']);
		break;
	default:
		MosaikDebug::msg("html", "Content-Type");
}

$resp->setHeader("Access-Control-Allow-Origin","*");
$resp->setHeader("Access-Control-Allow-Method","POST, GET, OPTIONS");
$resp->setHeader("Access-Control-Allow-Headers","X-Requested-With");

$mtime = microtime(true);
$totaltime = $mtime - $starttime;
//$GLOBALS['firephp']->log("PHP took " . $totaltime . " seconds");

if (MosaikConfig::isDebug("doctrine-profile")) {
	MosaikDebug::groupStart("Doctrine Profile", "doctrine-profile");
	/** profiling * */
	$time = 0;
	foreach ($profiler as $event) {
		$time += $event->getElapsedSecs();
		MosaikDebug::msg($event->getName() . " " . sprintf("%f", $event->getElapsedSecs()), "Query");
		MosaikDebug::msg($event->getQuery(), "Query");
		$params = $event->getParams();
		if (!empty($params)) {
			MosaikDebug::msg($params, "Parameters");
		}
	}
	MosaikDebug::msg($time, "Total time");

	/** profiling * */
	MosaikDebug::groupStop();
}
ob_end_clean();

$resp->out();
/*
  o b*_start();
  if ( MosaikConfig::isDebug("html")) {

  } else {
  ob_start();
  $resp->out();
  $contents = ob_get_contents();
  ob_end_clean();
  switch ( getRequestType() ) {
  case "json":
  case "pdf":
  case "csv":
  echo $contents;
  break;
  default:
  $hstarttime = microtime(true);
  // echo Minify_HTML::minify($contents);
  // fixme: lets see if pages are cacheabe store them here and minify them
  echo $contents;
  $htime = microtime(true) - $hstarttime;
  $GLOBALS['firephp']->log("Minify " . $htime . " seconds");
  break;
  }
  }
  ob_end_flush();
 */
session_commit();

SystemService::onFinish();
?>
