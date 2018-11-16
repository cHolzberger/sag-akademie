<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("Mosaik/JsonResult.php");
require_once("templates/sag/JsonComponent.php");

class JSON_User extends JsonComponent {
    
	function getTable() {
	    return Doctrine::getTable("XUser");
	}

	function renderJson() {
		$jr = new MosaikJsonResult();
		$jr->q = Doctrine_Query::create()->from("XUser u");
		$jr->headers = $this->getHeaders();
		$jr->headline = "Standorte";
		
		return $jr->render();
	}
	 
	function getHeaders() {
		global $_tableHeaders;
		
		return $_tableHeaders['XUser'];
	}
	
	function filter() {
		
	}
}

?>
