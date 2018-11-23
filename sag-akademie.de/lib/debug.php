<?php
ini_set('display_errors','On');
ini_set("html_errors", "On");
ini_set('log_errors', 1);

include_once("Mosaik/Config.php");

if ( function_exists("xdebug_enable") && function_exists("xdebug_call_class") ) {
	xdebug_start_error_collection();
}

function log_trace() {
	if ( function_exists("xdebug_enable") && function_exists("xdebug_call_class") ) {
		ob_start();
		xdebug_print_function_stack();
		qlog(ob_get_contents());
		ob_end_clean();
	}
}


function errorHandler($errno, $errstr, $errfile, $errline)
{   $err = "";
	if ( function_exists("xdebug_call_class")) {	
		$err = "\nSource: ".xdebug_call_class()."::".xdebug_call_function() ." in $errfile:$errline";
	}
            
	global $serverCtrl;
	$err = $err . "\n$errstr";
    switch ($errno) {
    case E_USER_ERROR:
		//$serverCtrl->fault($err);
		qlog($err);
		log_trace();
		qlog(xdebug_get_collected_errors());
        exit(1);
        break;

    case E_USER_WARNING:
			//$serverCtrl->fault("WARNING:" . $err);
			qlog("WARNING: ". $err);
        break;
	case E_NOTICE:	
    case E_USER_NOTICE:
        	//$serverCtrl->fault("NOTICE:" . $err);
			qlog("NOTICE $errfile:$errline => ");
			qdir($err);
			//qlog(xdebug_get_collected_errors());

        break;
	case E_DEPRECATED:
		break;
    default:
		if ( isset($serverCtrl)) {
   //  		$serverCtrl->fault("UNKNOWN ERROR:" . $err);
		}
		qlog("UNKNOWN: $errno");
		qdir($err);
		log_trace();
		//qlog(xdebug_get_collected_errors());

        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}
$old_error_handler = set_error_handler("errorHandler");




function begin() {
	if (! function_exists("xdebug_enable") ) return; 
	
	qlog("======== NEW RPC REQUEST =========\nDate: ".date("Y.m.d H:m:s") ."\nTime Index: " . xdebug_time_index()
	. "\nFormat: ". $_SERVER['HTTP_ACCEPT']);
	
}


function memreport($msg=null) {
	if (! function_exists("xdebug_enable") ) return;
	if ( $msg ) qlog ( $msg);
	
	qlog("Time Index: " . xdebug_time_index());
	qlog("\nMemory Usage: " . xdebug_memory_usage() /(1024*1024). " MBytes");
	qlog("\nPeak Memory Usage: " . xdebug_peak_memory_usage() / (1024*1024) . " MBytes");
}
function report() {
	if (! function_exists("xdebug_enable") ) return; 
	
	qlog("-- Done --");
	memreport();
	
}

// quick logging
// usefull when doing rpc debug
function qlog($msg) {
	$logfile = MosaikConfig::getVar("qlog/file");
	if (!empty($logfile)) {
		$msg = date("m.d.y-H:i:s") .":". $msg;
		error_log($msg);

		file_put_contents($logfile, $msg . "\n", FILE_APPEND);

	} else {
		error_log($msg);
	}
}

function qerror ($msg) {
	$logfile = MosaikConfig::getVar("qlog/errorfile");
	if (!empty($logfile)) {
        $msg = date("m.d.y-H:i:s") .":". $msg;
		error_log($msg);

		file_put_contents($logfile, $msg . "\n", FILE_APPEND);

	} else {
		error_log($msg);
	}
}

function qerror_dir ( $obj) {
	qdir($obj, "qerror");
}
// quick logging
// usefull when doing rpc debug
function qdir($obj,$fn = "qlog") {
	$logfile = MosaikConfig::getVar("qlog/file");

	ob_start();
	var_dump($obj);
	$cnt = ob_get_contents();
	ob_end_clean();

    $fn($cnt);
}


function exceptionHandler($e) {
	qlog("Exception: " .get_class($e) . " ==> ". $e->getMessage());
	qlog("Trace: " . $e->getTraceAsString());
	qlog("Quelle: " . $e->getFile() . ":" . $e->getLine());
	//qdir($e);

}
$old_exception_handler = set_exception_handler("exceptionHandler");

class FirePHP {
	static $classStack=array();
	static function group () {

	}
	static function groupEnd () {
		
	}

	static function info () {
		
	}
	static function infoGroup($grn) {
		if ( MosaikConfig::isDebug ("firephp-info") ) {
			qdir($grn);
		}
	}

	static function infoGroupEnd() {
		if ( MosaikConfig::isDebug ("firephp-info") ) {
			qlog("end");
		}
	}

	static function infoDebug($info,$lbl="Info") {
		if ( MosaikConfig::isDebug ("firephp-info") ) {
			qlog($lbl.": ");
			qdir($info);
		}
	} 
	
	static function errorDebug($e) {
		if ( MosaikConfig::isDebug ("firephp-error") ) {
			qlog("error: ");
			qdir($e);
		}
	}
	
	static function log($m) {
		qdir($m);
	}

	static function msg ( $info, $lbl, $class="c") {
		if ( MosaikConfig::isDebug ("firephp-".$class) ) {
			qlog("$class $lbl: ");
			qdir($info);
		}
	}
	
	static function groupStop() {
		qlog("Group stop");
	}
	
	static function groupStart ( $lbl, $class="c" ) {
		qlog("Group start");
	}

	static function getInstance() {
		return $GLOBALS['firephp'];
	}
}

class MosaikDebug extends FirePHP {
	
}



$firephp = new MosaikDebug();
$GLOBALS['firephp'] = &$firephp;
$GLOBALS['debug']['firephp'] = &$firephp;


error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED );
ini_set('display_errors','On');
ini_set("html_errors", "Off");
