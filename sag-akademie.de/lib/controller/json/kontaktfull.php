<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");

class JSON_KontaktFull extends JsonComponent {

	function getTable() {
		$name = "";
		MosaikDebug::msg($this->saveData, "SaveData");
		if (array_key_exists("tableName", $this->saveData)) {
			$name = $this->saveData['tableName'];
		} else if (substr($this->saveData["id"], 0, 2) == "ak") {
			$name = "AkquiseKontakt";
		} else if (substr($this->saveData["id"], 0, 2) == "kk") {
			$name = "Kontakt";
		} else {
			return null;
		}
		$this->saveData['tableName'] = $name;
		MosaikDebug::msg($name, "TableName");

		$this->saveData["id"] = substr($this->saveData["id"], 2);

		return Doctrine::getTable($name);
	}

	function search($q) {
		$table = "ViewAkquiseKontaktR";
		$q = Doctrine::getTable($table)->detailSearch($q);
		return $q;
	}

	function searchStart($a) {
		$table = "ViewAkquiseKontaktR";
		$a = utf8_encode(urldecode($a));
		$q = Doctrine::getTable($table)->detailSearch($a . "%");
		return $q;
	}

	function advancedSearch($rules) {
		if (is_array($rules)) {
			ksort($rules);
		} else if (is_string($rules)) {
			$rules = explode("#:#", $rules);
		}

		$table = "ViewAkquiseKontaktR";

		$q = Doctrine::getTable($table)->advancedSearch($rules);

		return $q;
	}

	function renderJson() {
		$jr = new MosaikJsonResult();
		$jr->perPage = 100;
		if (isset($_POST['advancedSearch'])) {
			MosaikDebug::msg($_POST, "POST");
			$jr->q = $this->advancedSearch($_POST['rules']);
			$jr->headline = "Firmen - Erweiterte Suche";
		} else if (isset($_POST['q'])) {
			$jr->q = $this->search($_POST['q']);
			$jr->headline = "Firmen - Suche nach: " . MosaikConfig::getEnv("q");
		} else if (isset($_POST['a'])) {
			$jr->q = $this->searchStart($_POST['a']);
			$jr->headline = "Firmen - Suche nach: " . MosaikConfig::getEnv("a");
		} else { // kein query argument speizifiziert -- zu grosse ergebnismenge
			//$tbl = Doctrine::getTable("ViewAkquiseKontaktR");
			//$data = $tbl->findAll(Doctrine::HYDRATE_ARRAY);
			$data = array();
		}

		//foreach ( $data as $key=>$d) {
		//	$data[$key]['kontaktQuelleStand'] = mysqlDateToLocal($d['kontaktQuelleStand']);
		//}
		$jr->headers = $this->getHeaders();
		$jr->headline .= " (Akquise Kontakte & Kunden)";

		if (MosaikConfig::getEnv('mode', "paged") == "all") {
			return $jr->renderAll();
		} else {
			return $jr->render();
		}
	}

	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['KontakteFull'];
	}

	function filter() {
		
	}

}

?>