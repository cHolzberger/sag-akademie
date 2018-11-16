<?php
// If using the zip archive
// TODO: Put the 'lib' folder from the archive on your include path
include_once('lib/FirePHP/fb.php'); // (procedural API) or
include_once('lib/FirePHP/Init.php'); // (object oriented API)
include_once("lib/Mosaik/Config.php");

// Configure FirePHP
define('INSIGHT_IPS', '*');
define('INSIGHT_AUTHKEYS', MosaikConfig::getVar("firephp/authkey"));
define('INSIGHT_PATHS', __DIR__);
define('INSIGHT_SERVER_PATH', '/index.php'); // assumes /index.php exists on your hostname
// NOTE: Based on this configuration /index.php MUST include FirePHP

class MosaikDebug extends FirePHP {
	static $classStack=array();
	
	static function infoGroup($grn) {
		if ( MosaikConfig::isDebug ("firephp-info") ) {
			$calledFrom = debug_backtrace(); 
			return $GLOBALS['firephp']->group($grn . "From File: " . $calledFrom[0]['file'] . ":".$calledFrom[0]['line'] );
		}
	}

	static function infoGroupEnd() {
		if ( MosaikConfig::isDebug ("firephp-info") ) {
			return $GLOBALS['firephp']->groupEnd();
		}
	}

	static function infoDebug($info,$lbl="Info") {
		if ( MosaikConfig::isDebug ("firephp-info") ) {
			return $GLOBALS['firephp']->info($info, $lbl);
		}
	} 
	
	static function errorDebug($e) {
		if ( MosaikConfig::isDebug ("firephp-error") ) {
			return $GLOBALS['firephp']->error($e);
		}
	}
	
	static function msg ( $info, $lbl, $class="c") {
		if ( $class!="c" && count(MosaikDebug::$classStack ) >0 ) {
			$class = MosaikDebug::$classStack[count(MosaikDebug::$classStack -1)];
		}
		if ( MosaikConfig::isDebug ("firephp-".$class) ) {
			return $GLOBALS['firephp']->info($info, $lbl);
		}
	}
	
	static function groupStop() {
		if ( count ( MosaikDebug::$classStack ) > 0 ) {
			$class = array_pop(MosaikDebug::$classStack);
		}
		if ( MosaikConfig::isDebug ("firephp-" . $class) ) {
			return $GLOBALS['firephp']->groupEnd();
		}
	}
	
	static function groupStart ( $lbl, $class="c" ) {
		array_push(MosaikDebug::$classStack, $class);
		
		if ( MosaikConfig::isDebug ("firephp-".$class) ) {
			$calledFrom = debug_backtrace(); 
			return $GLOBALS['firephp']->group($grn . "From File: " . $calledFrom[0]['file'] . ":".$calledFrom[0]['line'] );
		}
	}
}

$firephp = MosaikDebug::getInstance(true);
$GLOBALS['firephp'] = &$firephp;
$GLOBALS['debug']['firephp'] = &$firephp;

if ( Config::isDebug("firephp") ) {
	$GLOBALS['firephp']->log(true, "Config.debug");
//	$firephp->registerErrorHandler();
//	$firephp->registerExceptionHandler();
	$options = array('maxObjectDepth' => 1,
                 'maxArrayDepth' => 3,
                 'useNativeJsonEncode' => true,
                 'includeLineNumbers' => true);

	$firephp->setOptions($options);
} else {
	$firephp->setEnabled(config::isDebug("firephp"));
}





MosaikDebug::infoGroup(__FILE__);
MosaikDebug::infoDebug(getenv("PHP_MCM_DEBUG_CONFIG"), "Debug-Config");
MosaikDebug::infoGroupEnd();
