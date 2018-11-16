<?php
$starttime = microtime(true);
// konstruct is twisting include data (autoloader)
// so include that file here!
ini_set('include_path',
  ini_get('include_path')
  .PATH_SEPARATOR.dirname(__FILE__)."/lib/controller"
  .PATH_SEPARATOR.dirname(__FILE__)."/lib"
  .PATH_SEPARATOR.dirname(__FILE__)."/lib/konstrukt"
  .PATH_SEPARATOR.dirname(__FILE__)."/lib/Doctrine/models" 
);


include_once('lib/std.inc.php');
//include ("lib/controller/sag_home.php");
/**
 * Application starts here
 **/

MosaikDebug::infoGroup("Root");
MosaikDebug::infoDebug("Creating Comonent Creator");
$htmlCc = new SAG_ComponentCreator();
MosaikDebug::infoDebug("Initializing Contruct");
$k = k();

if (MosaikConfig::isDebug("konstrukt")) {
	$k->setLog(dirname(__FILE__) . '/log/debug.log');
	$k->setDebug();
}
$k->setIdentityLoader(new SAG_IdentityLoader());
$k->setComponentCreator($htmlCc);
MosaikDebug::infoDebug("RunningKonstruct"); 
MosaikDebug::infoGroupEnd();


$resp = $k->run('SAG_Home'); // k_httpResponse from now on

$resp->setHeader("Expires", "now");
$resp->setHeader("Last-Modified", gmdate('D, d M Y H:i:s') . ' GMT');

$mtime = microtime(true);
$totaltime = $mtime - $starttime;
$GLOBALS['firephp']->log("PHP took " . $totaltime . " seconds");

if ( MosaikConfig::isDebug("html")) {
	$resp->out();
} else { 
	ob_start();
	$resp->out();
	$contents = ob_get_contents();
	ob_end_clean();
	echo Minify_HTML::minify($contents);
}
?>