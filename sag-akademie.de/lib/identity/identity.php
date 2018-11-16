<?php
include_once("httpdigest.php");

class SAG_Identity implements k_Identity {
	private $fromVdrk;
	static $users = array();
	
	function user() {
		return "";
	}
	
	function anonymous() {
		return false;
	}
	
	function group() {
		return "";
	}
	
	function uid() {
		return -2;
	}
	
	function active() {
		return true;
	}
	
	function isFromVdrk() {
		return $this->fromVdrk;
	}
	
	function logout() {
		
	}
	
	function setFromVdrk($value) {
		extract($GLOBALS['debug']);
		$firephp->log($value,"Setting vdrk mode");
		$this->fromVdrk = $value;
	}
	
	function getUsers ($class = "_") {
		
		if ( !isset(SAG_Identity::$users[$class]))  {
		    if ( $class=="_") {
			SAG_Identity::$users[$class] = Doctrine::getTable("XUser")->findAll();
		    } else {
			SAG_Identity::$users[$class] = Doctrine::getTable("XUser")->findByClass($class);
		    }
		}
		return SAG_Identity::$users[$class];
	}
	
	function authenticate ($class=NULL) {
 		//throw new k_SeeOther("/resources/logon/");
		MosaikDebug::msg("Not Authenticated -- SAG_Identity", "Login");
		//die();
	}
}

include_once('kundeidentity.php');
include_once('adminidentity.php');
include_once('anonymousidentity.php');

include_once('kundeidentityloader.php');
include_once('anonymousidentityloader.php');

include_once('adminidentityloader.php');

function getIdentityLoader() {
	$uri = $_SERVER['REQUEST_URI'];
	if ( 0 ==  substr_count($uri, "admin")) {
		$cookie = new k_adapter_DefaultCookieAccess(MosaikConfig::getVar("hostname"), "/"); // FIXME: provide values!!!
		$session = new k_adapter_DefaultSessionAccess($cookie);
	} else {
		$cookie = new k_adapter_DefaultCookieAccess(MosaikConfig::getVar("hostname"), "/admin"); // FIXME: provide values!!!
		$session = new k_adapter_DefaultSessionAccess($cookie);
	}
	
	if ( 0!= substr_count($uri, "kunde/fehler") || 0!= substr_count($uri, "kunde/neu") || 0!= substr_count($uri, "kunde/gesperrt") ) {
		return new SAG_AnonymousIdentityLoader($session);
	} else if ( 0 !=  substr_count($uri, "admin") ) {
		return new SAG_AdminIdentityLoader($session);
	} else if (  $session->get("kunde_user") != null || 0 != substr_count($uri, "kunde/")) {	
		return new SAG_KundeIdentityLoader($session);
	} else {
		return new SAG_AnonymousIdentityLoader($session);
	}
}
  
?>