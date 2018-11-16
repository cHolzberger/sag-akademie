<?php
class SAG_KundeIdentityLoader implements k_IdentityLoader {
	function __construct(&$session) {
		$this->session = $session;
	}
	
	function load(k_Context $context) {
		$cookie = new k_adapter_DefaultCookieAccess(MosaikConfig::getVar("hostname"),""); // FIXME: provide values!!!
		$session = new k_adapter_DefaultSessionAccess($cookie);
		$ident = null;
	 			
		if ( isset($_POST['username']) ){
			$ident = new SAG_KundeIdentity($session);
		} else if ( $session->get("kunde_user") != null){
			$ident = new SAG_KundeIdentity($session);
 		} else { 
			  $ident = new SAG_AnonymousIdentity("/kunde/fehler");		
		}
		
		$cookies = $context->cookie();
		 
		if ( $context->query("vdrk", false) ) {
			$ident->setFromVdrk(true);
			$cookies->set("vdrk", true, time() + 3600);
		} else if ( $context->cookie("vdrk",false) ) {
			$ident->setFromVdrk(true);
		} else {
			$ident->setFromVdrk(false);
		}
		Mosaik_ObjectStore::init()->get("/current")->add ( "identity", $ident, "identity information" );
		return $ident;
  	}
}
?>