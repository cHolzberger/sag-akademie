<?
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
		$serverCtrl->fault($err);
		qlog($err);
		log_trace();
        exit(1);
        break;

    case E_USER_WARNING:
			//$serverCtrl->fault("WARNING:" . $err);
			qlog("WARNING: ". $err);
        break;

    case E_USER_NOTICE:
        	//$serverCtrl->fault("NOTICE:" . $err);
			qlog("NOTICE: ". $err);
        break;

    default:
		if ( isset($serverCtrl)) {
   //  		$serverCtrl->fault("UNKNOWN ERROR:" . $err);
		}
		qlog("UNKNOWN: ". $err);
		log_trace();
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

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

error_reporting(E_ALL);
ini_set('display_errors','Off');
ini_set("html_errors", "Off");
$old_error_handler = set_error_handler("errorHandler");

session_name("SAGAkademieWebsession");
// path for cookies
$cookie_path = "/";

// timeout value for the cookie
$cookie_timeout = 60 * 60 * 1000; // in seconds
session_set_cookie_params($cookie_timeout, $cookie_path);
session_cache_expire($cookie_timeout * 2);
//ini_set("session.save_path", dirname(dirname(__FILE__)) . "/resources/sessions/");

session_start();

// this is the bootstrap file for the rpc server
// set the default mode to deliver the html content
//************* BOOTSTRAP *******************/
error_reporting(E_ERROR | E_WARNING | E_PARSE & ~E_NOTICE & ~E_DEPRECATED );

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__) )));

// Define application environment
//defined('APPLICATION_ENV')
//    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

ini_set('include_path',
 	APPLICATION_PATH
 . PATH_SEPARATOR . APPLICATION_PATH. "/lib"
);
if ( !class_exists("Zend_Loader_Autoloader")) {
	require_once("Zend/Loader.php");

	require_once("Zend/Loader/Autoloader.php");
	
}
include_once ("lib/config.php");

begin();
// Load config
$autoloader = Zend_Loader_Autoloader::getInstance();
$config = new Zend_Config_Json(APPLICATION_PATH . '/config.json',APPLICATION_ENV);

foreach ( $config->includePath as $path ) {
	set_include_path(implode(PATH_SEPARATOR, array(
			realpath(APPLICATION_PATH . "/" . $path),
			realpath ( APPLICATION_PATH ),
			realpath ( APPLICATION_PATH . "/lib"),
			get_include_path(),
		)));
}


$runtimeConfig = new stdClass();
$runtimeConfig->libraryPath = APPLICATION_PATH . "/" . $config->libraryPath;
$runtimeConfig->modelsPath = APPLICATION_PATH . "/" . $config->db->modelsPath;
$runtimeConfig->templatePath = APPLICATION_PATH . "/" . $config->db->templatesPath;

$runtimeConfig->rpcPath = dirname (__FILE__);
// MSUtil - all sorts of utils
include_once ("MSUtil.php");

 $env = MSUtil::getEnv();
 $env->setCacheDir(dirname(dirname(__FILE__)) . "/resources/cache/envcache_");
//memreport("Before Doctrine");
// DBPool - Database bootstraping and utils
include ( "DBPool.php" );
DBPool::init(new MSUtil_Mixin ( $runtimeConfig, $config ) );


// use either Get vars (if $_GET['s'] is present
// or the url
//memreport("After Doctrine");
//find out which server to use
$serverName = array_key_exists("s", $_GET) ?  $_GET['s']:"html";
$mode = "";
$targetService = "";


	
if ( $serverName != "html") {
	//find out which mode to use
	$mode = array_key_exists("m", $_GET) ?  $_GET['m']:"html";
	//and for performance reasons and because sope cant work the other way round, one can specify an target object here
	// targetObject notation is namespace.functionName
	$targetService = array_key_exists("t", $_GET) ?  $_GET['t']: null;
} else  if ( dirname( $_SERVER['PHP_SELF']) . "/" ==  $_SERVER['REQUEST_URI'] || basename($_SERVER['REQUEST_URI']) == "index.php") { 
	$serverName = "html";
} else  {
	// url may either be 
	// <Servertype>
	// or
	//<Servertype>/<Namespace>/<class> for RPC Interface
	// or 
	//<Servertype/<Namespace>/<class>/smd for Smd info
	// or 
	// <Servertype>/<Namespace>
	// or
	//<Servertype>/<Namespace>/smd
	$base = dirname($_SERVER['PHP_SELF']);
	$url = explode("?", $_SERVER['REQUEST_URI']);
	$subdir = substr($url[0], strlen($base)+1);
	$request = explode("/", $subdir);
	
	switch ( count ($request)) {
		case 1:  
			// <Servertype>
			$targetService=null;
			$serverName = $request[0];

			break;
		case 2: 
			// <Servertype>/<Namespace>
			// or 
			// <Servertype>/smd
			$serverName = $request[0];
			
			if ( $request[1] == "smd" ) {
				$mode = "smd";
				$targetService=null;
			} else {
				$mode = "html";//ugly
				$targetService = $request[1];
			}
			break;
		case 3:
			//<Servertype>/<Namespace>/<class> for RPC Interface
			// or 
			//<Servertype>/<Namespace>/smd
			$serverName = $request[0];
			
			if ( $request[2] == "smd" ) {
				$targetService = $request[1];
				$mode = "smd";
			} else {
				$mode = "html"; // ugly
				$targetService = $request[1] . "." . $request[2];
			}
			break;
		case 4:
			// <Servertype/<Namespace>/<class>/smd
			
			$serverName = $request[0];
			$targetService = $request[1] . "." . $request[2];
			$mode = "smd";
			
			break;
	}
}





include ("AnyRpc.php");
AnyRpc::init(new MSUtil_Mixin ( $runtimeConfig, $config) );
//memreport("After AnyRpc");
//************ END BOOTSTRAP *********/
//

//******** DO LOGIN ******


include("_lib/Identity.php");
qdir($_SESSION);
global $identity;
$identity = Identity::create();
$session = (object) $_SESSION;
if ( array_key_exists("logout", $_GET) ) {
	session_destroy();
}

if ( $identity->authenticate ( new MSUtil_Mixin ( $session, (object)$_POST, (object)$_GET ) )) {

	$_SESSION['token'] = $identity->getToken();

	session_commit();
} else {
	qerror("unauthorized access to _rpc server");
	qerror_dir($_SESSION);
	echo "You are not allowed to login";
	return;
		// fixme throw 403
}

// lookup ale modules and register them to the autoloader
$modules = AnyRpc::loadModules (APPLICATION_PATH . "/" . $config->moduleRoot);

if ( $serverName == "html") {
	echo "<pre>";
	echo "RPC - Service\n\n";
	echo "Logged in as: :" . $identity->getDn();
	echo "'\n\n";
	foreach ( $modules as $module ) {
		echo "\t{$module['namespace']}: \n";
		foreach ( $module['config']->rpcClasses as $class ) {
			echo "\t\t$class\n";
		}
	}
	echo "</pre>";
	return;
}

// create the server
$serverCtrl = AnyRpc::createServer($serverName, $mode, $targetService);

if ( $targetService === null ) {
	qlog("No Service specified providing all Classes");
	
	// setup all modules
	Zend_Registry::set ("modules", $modules);

	// Add classes exportet through config

	foreach ( $modules as $module ) {
		AnyRpc::setupModule ($module);

		foreach ( $module['config']->rpcClasses as $class ) {
			$serverCtrl->setClass($class, lcfirst($module['namespace']), $module['config']);
		}
	}
} else {
	$classname = str_replace ( ".", "_", $targetService);
	$namespace = explode (".", $targetService);
	$namespace = $namespace[0];
	
	qdir(array( "namespace"=> $namespace, "classname" => $classname ));

	//fixme add / remove namepsace here
	$module = $modules[$namespace];

	AnyRpc::setupModule($module);
	$serverCtrl->setClass ($classname, lcfirst($module['namespace']), $module['config']);
}


try {
	$serverCtrl->handle ();
	
} catch ( Exception $e ) {
	qerror("RPC-EXCEPTION:");
	qerror ( $e->getMessage() );
}


report();

