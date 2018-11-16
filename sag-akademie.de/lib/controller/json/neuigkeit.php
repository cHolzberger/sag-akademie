<?php
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
class JSON_Neuigkeit extends JsonComponent {
	function getTable() {
	    return Doctrine::getTable("Neuigkeit");
	}
	function renderJson() {
		$jr = new MosaikJsonResult();
		$jr->q = Doctrine::getTable( "Neuigkeit" )->getNews();
		$jr->headers = $this->getHeaders();
		$jr->headline = "Neuigkeiten";

		return $jr->render();
	}
	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['Neuigkeit'];
	}
}
?>
