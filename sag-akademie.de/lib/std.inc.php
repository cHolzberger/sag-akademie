<?php
// standard include for admin and public views
/* PDF DEFAULTS: */
$GLOBALS['pdf-margin-top'] =5;
$GLOBALS['pdf-margin-left'] =5;
$GLOBALS['pdf-margin-bottom'] =5;
$GLOBALS['pdf-margin-right'] =5;

include_once('lib/config.php');
include_once('lib/debug.php');

/* clearing apc cache */

if (MosaikConfig::getVar("apcEnabled")) {
    $loaded = false;
	$version = trim(apc_fetch ("runtime_version", $loaded));
    if ( $loaded) {
	$currentVersion = trim(MosaikConfig::getVar("version"));
	if ( $currentVersion != $version ) {
	    MosaikDebug::msg($version, "Version Change Detected clearing apc cache...");
	    apc_clear_cache();
	    apc_clear_cache("user");
	    apc_add("runtime_version", trim($currentVersion));
	}
    }
} else if (MosaikConfig::getVar("memcacheEnabled") ) {
    $loaded = false;
    $m = new Memcache();
    $m->addServer("localhost", 11211);

    $version = trim($m->get ("runtime_version", $loaded));
    if ( $loaded) {
	$currentVersion = trim(MosaikConfig::getVar("version"));
	if ( $currentVersion != $version ) {
	    MosaikDebug::msg($version, "Version Change Detected clearing apc cache...");
	    $m->clear();
	    $m->set("runtime_version", trim($currentVersion));
	}
    }
}

MosaikDebug::groupStart("Loading Framwork...","framwork");

include_once('compat.php');
include_once("helpers.php");
include_once ('konstrukt.inc.php');
include_once("sag/components.php");
include_once("generic/components.php");
include_once("Mosaik.php");
include_once("identity/identity.php");
include_once("lib/breadcrumbtranslate.php");
include_once("jsmin/jsmin.php");
include_once("jsmin/htmlmin.php");
include_once("jsmin/CSSCompressor.php");
include_once("jsmin/CSSUriRewriter.php");
include_once("jsmin/CSSmin.php");
MosaikDebug::msg("done","Loading");
MosaikDebug::groupStop();
/** 
 * date init 
 */

date_default_timezone_set("Europe/Berlin");
mb_internal_encoding("UTF-8");
/**
 * Firephp init
 */




include('lib/dbconnection.php');
