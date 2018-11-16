<?php
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
class JSON_Kooperationspartner extends JsonComponent {
	function getTable() {
	    return Doctrine::getTable("Kooperationspartner");
	}
	function renderJson() {
		$jr = new MosaikJsonResult();
		$jr->q = Doctrine_Query::create()->from("Kooperationspartner");
		if ( ( $kategorie = MosaikConfig::getEnv("kategorie", false )) ) {
			$jr->q->where("kategorie = ?", $kategorie);
		}
		$jr->headers = $this->getHeaders();
		$jr->headline = "Kooperationspartner";

		return $jr->render();
	}
	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['Kooperationspartner'];
	}
}
?>
