<?php
#include_once ("lib/pear/Log/Log.php");

global $firephp;
 $firephp = FirePHP::getInstance(true);

function dlog($msg, $indent=0) {
	global $firephp;
	$indent = "";

	for ($i=0; $i< $indent; $i++) {
		$indent .= " ";
	}

	$firephp->log ($indent . $msg);
}

function ddump($msg, $var) {
	global $firephp;
	$firephp->dump ($msg, $var);
}

?>