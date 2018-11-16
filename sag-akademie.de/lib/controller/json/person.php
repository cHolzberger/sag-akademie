<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php")
;
require_once("Mosaik/JsonResult.php");

class JSON_Person extends JsonComponent {

	function search($q) {
		$table = "Person";
		return Doctrine::getTable($table)->detailedSearch($q);
	}

	function getTable() {
		return Doctrine::getTable("Person");
	}

	function searchStart($a) {
		$table = "Person";
		$a = utf8_encode(urldecode($a));
		$hits = Doctrine::getTable($table)->search($a . "%");
		$ids = array();

		if (count($hits) < 1) {
			return array();
		}

		foreach ($hits as $hit) {
			$ids[] = $hit['id'];
		}

		$q = Doctrine::getTable($table)->detailedIn($ids);
		return $q;
	}

	function advancedSearch($rules) {
		
		
		if (is_array($rules)) {
			ksort($rules);
		} else if (is_string($rules)) {
			#$rules = explode("#:#", $rules);
			$rules = explode(",", $rules);
		}
		$table = "ViewPerson";
		return Doctrine::getTable($table)->advancedSearch($rules);
	}

	function renderJson() {
		qlog(__CLASS__ . ":: " . __FUNCTION__);
		qdir($_POST);
		$jr = new MosaikJsonResult();
// FIXME: personen werden nicht aufgeloest
		$jr->headers = $this->getHeaders();
		$jr->headline = "Personen suche: ";

		if (isset($_POST['advancedSearch'])) {
			$jr->setNocache(true);
			$jr->q = $this->advancedSearch($_POST['rules']);
			$jr->headline = ""; //.= advancedSearchAsString();
		} else if (($q=MosaikConfig::getEnv('q', false)) != false) {
			qlog ( "cache id: person_suche_" . $q);
			$jr->setCacheId("person_suche_". $q);
			$jr->q = $this->search($q);
			$jr->headline .= $q;
		} else if (isset($_POST['a'])) {
			$jr->setCacheId("person_start_" + $_POST['a']);
			
			$jr->q = $this->searchStart($_POST['a']);
			$jr->headline .= $_POST['a'];
		} else if (MosaikConfig::getEnv('kontaktId', false)) {
			$jr->setCacheId("person_advanced_search_{$_POST['kontaktId']}");
			$kontaktId = MosaikConfig::getEnv('kontaktId');
			$jr->setCacheId("person_kontakt_" + $kontaktId);
			qlog("KontaktID: $kontaktId");
			$jr->q = Doctrine_Query::create();
			$jr->q->from("Person")->where("kontakt_id = ?", $kontaktId);
		}

		return $jr->render();
	}

	function getHeaders() {
		global $_tableHeaders;

		return $_tableHeaders['Person'];
	}

	function filter() {
		
	}

}

?>