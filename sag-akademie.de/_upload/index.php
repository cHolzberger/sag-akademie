<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

// this is the bootstrap file for the post-upload server
//************* BOOTSTRAP *******************/
ob_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE & ~E_NOTICE & ~E_DEPRECATED);

// Define path to application directory
defined('APPLICATION_PATH')
 || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__))));

ini_set('include_path',
 	APPLICATION_PATH
 . PATH_SEPARATOR . APPLICATION_PATH. "/lib"
);
require_once ("Zend/Config/Json.php");
require_once ("lib/config.php");



// Load config


$config = new Zend_Config_Json(APPLICATION_PATH . '/config.json', APPLICATION_ENV);

foreach ($config->includePath as $path) {
	set_include_path(implode(PATH_SEPARATOR, array(
	  realpath(APPLICATION_PATH . "/" . $path),
	  realpath(APPLICATION_PATH),
	  realpath(APPLICATION_PATH . "/lib"),
	  get_include_path(),
	 )));
}
require_once("Zend/Loader/Autoloader.php");
$autoloader = Zend_Loader_Autoloader::getInstance();

$runtimeConfig = new stdClass();
$runtimeConfig->libraryPath = APPLICATION_PATH . "/" . $config->libraryPath;
$runtimeConfig->modelsPath = APPLICATION_PATH . "/" . $config->db->modelsPath;
$runtimeConfig->templatePath = APPLICATION_PATH . "/" . $config->db->templatesPath;

$runtimeConfig->rpcPath = dirname(__FILE__);
// MSUtil - all sorts of utils
include ("MSUtil.php");


// DBPool - Database bootstraping and utils
include ( "DBPool.php" );
DBPool::init(new MSUtil_Mixin($runtimeConfig, $config));

//StoreCollection - 
include ( "Store.php" );
Store::init(new MSUtil_Mixin($runtimeConfig, $config));

$bootlog = ob_get_contents();
ob_end_clean();
//************ END BOOTSTRAP *********/
qlog("Upload Module Ready");

$env = MSUtil::getEnv();

$store = $env->service;
$stores = Store::loadModules(APPLICATION_PATH . "/" . $config->storeRoot);

if ($store == null) {
	echo "<pre>";
	foreach ($stores as $store) {
		print_r($store);
	}

	echo "</pre>";
} else {
	$module = $stores[$store];
	$instance = Store::setupModule($module);
	if ( $env->download != null) {
		$instance->handleDownload($env);
	} else if ( $env->delete != null ) {
		$instance->handleDelete($env);
	} else if ( $env->upload != null ) {
		$instance->handleUpload($env);
	} else {
		$instance->showInfo($env);
	}
}


// MISC - FIXES - WRONG PLACE!
if ( array_key_exists("fix", $_GET ) ) {
	echo "Running FIX!";
	echo "<b>Kontakte:</b><br/>";
	$q = Doctrine_Query::create();
	
	$q->from ("PostInfo")->where("url LIKE ?", "/admin/kontakte/new%");
	
	$results = $q->execute();
	
	foreach ( $results as $i) {
		//echo "running:";
		$data = unserialize($i['data']);
		//print_r($data);
		echo $data['kontakt']['firma'] . "<br/>";
		
		$kq = Doctrine_Query::create();
		$kq->from("Kontakt")->where("firma = ?", $data['kontakt']['firma']);
		$kontakt = $kq->execute();
		
		foreach ( $kontakt as $k ) {
			if ( $k->angelegt_datum == "0000-00-00 00:00:00" && trim($k->firma) != "") {
				echo "fixing ...<br/>";
				$k->angelegt_datum = $i->datum;
				$k->save();
			}
		}
	}
	echo "<b>AkquiseKontakte:</b><br/>";
	$q = Doctrine_Query::create();
	
	$q->from ("PostInfo")->where("url LIKE ?", "/admin/akquise/new%");
	
	$results = $q->execute();
	
	foreach ( $results as $i) {
		//echo "running:";
		$data = unserialize($i['data']);
		//print_r($data);
		echo $data['AkquiseKontakt']['firma'] . "<br/>";
		
		$kq = Doctrine_Query::create();
		$kq->from("AkquiseKontakt")->where("firma = ?", $data['AkquiseKontakt']['firma']);
		$kontakt = $kq->execute();
		
		foreach ( $kontakt as $k ) {
			if ( $k->angelegt_datum == "0000-00-00 00:00:00" && trim($k->firma) != "") {
				echo "fixing ...<br/>";
				$k->angelegt_datum = $i->datum;
				$k->save();
			}
		}
		
	}
}

