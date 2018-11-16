<?php
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");


class JSON_Download extends JsonComponent {
	function getTable() {
	    return Doctrine::getTable("Download");
	}
	function renderJson() {
		$jr = new MosaikJsonResult();
		$jr->q = Doctrine_Query::create()->from("Download download");

		if ( ( $kategorie = MosaikConfig::getEnv("kategorie", false )) ) {
			$jr->q->where("kategorie = ?", $kategorie);
		}
		
		$jr->headers = $this->getHeaders();
		$jr->headline = "Download";

		return $jr->render();
	}
	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['Download'];
	}
}
?>
