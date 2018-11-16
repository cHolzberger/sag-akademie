<?php
class SAG_AnonymousIdentity extends SAG_Identity  {
	function __construct ( $authRedirect = "") {
		$this->redirect = $authRedirect;
	}
	
	function anonymous() {
		return true;
	}
	
	function authenticate ($class= NULL) {
		MosaikDebug::msg("SAG_AnonymousIdentity", "Login");
		if ( getRequestType() != "json" ) {
			instantRedirect($this->redirect);
		} else {
			die ('{"success": false}');
		}
	}
}
?>