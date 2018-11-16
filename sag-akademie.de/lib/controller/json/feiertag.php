<?php
/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
class JSON_Feiertag extends k_Component {

	function map() {
		return "JSON_Feiertag";
	}

	function POST() {
		return $this->renderJson();
	}

	function forward($class_name, $namespace = "") {
		$jr = new MosaikJsonResult();
		$feiertag_id = $this->next();
		$q = Doctrine_Query::create();
		$q->from("Feiertag");
		$q->where("id=?", $feiertag_id);
		$jr->headline = "Feiertag " . $q;
		$jr->q = $q;
		$jr->headers = $this->getHeaders();

		return $jr->renderSingle();
	}

	function renderJson() {
		 $year= MosaikConfig::getEnv("year", false);
		
		
		$jr = new MosaikJsonResult();
		
		if ( $year ) {
			$jr->q = Doctrine::getTable("Feiertag")->yearSelect($year);
		} else {
			$jr->q = Doctrine::getTable("Feiertag")->getAll();
		}
		
		$jr->headers = $this->getHeaders();
		$jr->headline = "Feiertag";

		return $jr->render();
	}

	function getHeaders() {
		global $_tableHeaders;
	
		return $_tableHeaders['Feiertag'];
	}

	function filter() {
		
	}

}
?>
