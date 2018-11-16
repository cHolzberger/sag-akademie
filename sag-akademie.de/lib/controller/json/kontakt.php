<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("Mosaik/JsonResult.php");

require_once("templates/sag/JsonComponent.php");

class JSON_Kontakt extends JsonComponent {

	function getTable() {
		return Doctrine::getTable("Kontakt");
	}

	function search($searchText,$kontext) {
		$table = "Kontakt";
		$q = Doctrine::getTable($table)->detailSearch($searchText,"",$kontext);

		return $q;
	}

	function searchAll($searchText) {
		$table = "Kontakt";
		$q = Doctrine::getTable($table)->detailSearchAll($searchText,"");

		return $q;
	}


	function searchStart($a,$kontext) {
		$table = "Kontakt";
		$a = utf8_encode(urldecode($a));
		$q = Doctrine::getTable($table)->search($a . "%",$kontext);

		return $q;
	}
	
	function searchStartAll($a) {
		$table = "Kontakt";
		$a = utf8_encode(urldecode($a));
		$q = Doctrine::getTable($table)->searchAll($a . "%");

		return $q;
	}

	function advancedSearch($rules,$kontext) {
		if (is_array($rules)) {
			ksort($rules);
		} else if (is_string($rules)) {
			$rules = explode(",", $rules);

			#$rules = explode("#:#", $rules);
		} else {
			throw new Exception("Keine Such-Parameter angegeben.");
		}

		$table = "ViewKontakt";
		$q = Doctrine::getTable($table)->advancedSearch($rules,$kontext);
		return $q;
	}

	function renderJson() {
		qlog(__CLASS__ . ":: " . __FUNCTION__);
		qdir($_POST);
		$jr = new MosaikJsonResult();
		
		$kontext = MosaikConfig::getEnv("kontext","all");
		

		if (isset($_POST['advancedSearch'])) {
			$jr->q = $this->advancedSearch($_POST['rules'],$kontext);
			//$jr->headline = "Firmen - Erweiterte Suche";
		} else if (MosaikConfig::getEnv('q', false)) {
			qlog("Kontatksuche im Kontext: $kontext");
			$searchQuery = MosaikConfig::getEnv('q');
			if ( $kontext == "all" ) {
				$jr->q = $this->searchAll($searchQuery);
			} else {
				$jr->q = $this->search($searchQuery,$kontext);
			}
			$jr->headline = "Firmen - Suche nach: " . MosaikConfig::getEnv("q");
		} else if (MosaikConfig::getEnv('a', false)) {
			if ( $kontext == "all" ) {
			$jr->q = $this->searchStartAll(MosaikConfig::getEnv('a'));
			} else {
			$jr->q = $this->searchStart(MosaikConfig::getEnv('a'), $kontext);
			}
			$jr->headline = "Firmen - Suche nach: " . MosaikConfig::getEnv("a");
		} else {
			$tbl = Doctrine_Query::create()->from("Kontakt")->where("1=1");
			$jr->headline = "Alle Firmen:";
			$jr->q = $tbl;
		}

		$jr->headline .= " (Kunden)";
		$jr->headers = $this->getHeaders();
		if (MosaikConfig::getEnv('mode', "paged") == "all") {
			return $jr->renderAll();
		} else {
			return $jr->render();
		}
	}

	function getHeaders() {

		global $_tableHeaders;
		return $_tableHeaders['Kontakt'];
	}

	function filter() {
		
	}

}

?>
