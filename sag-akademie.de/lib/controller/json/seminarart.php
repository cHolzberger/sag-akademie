<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");

class JSON_SeminarArt extends JsonComponent {

	function map() {
		return "JSON_SeminarArt";
	}

	function getTable() {
		return Doctrine::getTable('SeminarArt');
	}

	function forward($class_name, $namespace = "") {
		$jr = new MosaikJsonResult();
		$standort_id = urldecode($this->next());
		$q = Doctrine_Query::create();
		$q->from("SeminarArt");
		$q->where("id=?", $standort_id);
		$jr->headline = "Standort " . $q;
		$jr->q = $q;
		$jr->headers = $this->getHeaders();

		return $jr->renderSingle();
	}

	function extend(&$data) {
		if (UTF8_HACK)
			$strTo = "UTF-8//IGNORE";
		else
			$strTo = "UTF-8";

		$data['zielgruppe'] = iconv("UTF-8", $strTo, $data['zielgruppe']);
		$data['bezeichnung'] = iconv("UTF-8", $strTo, $data['bezeichnung']);
		$data['langbeschreibung'] = iconv("UTF-8", $strTo, $data['langbeschreibung']);
		$data['kurzbeschreibung'] = iconv("UTF-8", $strTo, $data['kurzbeschreibung']);
		$data['nachweise'] = iconv("UTF-8", $strTo, $data['nachweise']);

		$data['voraussetzungen'] = iconv("UTF-8", $strTo, $data['voraussetzungen']);
		if (isset($_GET['convert'])) {
			$data['datum_begin'] = mysqlDateToLocal($data['datum_begin']);
			$data['datum_ende'] = mysqlDateToLocal($data['datum_ende']);
		}
	}

	function renderJson() {
		qlog(__CLASS__ ."(".__FILE__. ":".__LINE__."): ". __FUNCTION__);

		$jr = new MosaikJsonResult();
		$tbl = Doctrine::getTable("SeminarArt");

		$jr->filter = $this;
		$jr->q = $tbl->detailedList();
		$jr->headline = "Seminare";
		$jr->headers = $this->getHeaders();

		$query = " status=1 ";
		if (MosaikConfig::getEnv('entwurfAnzeigen', false) == 1) {
			$query.=" OR status=3 ";
		}

		if (MosaikConfig::getEnv('inaktivAnzeigen', false) == 1) {
			$query.=" OR status=2 ";
		}


		if (MosaikConfig::getEnv('internAnzeigen', false) == 1) {
			$query.=" OR status=4 ";
		}

		if (MosaikConfig::getEnv('orgaAnzeigen', false) == 1) {
			$query.=" OR status=7 ";
		}

		$jr->q = $jr->q->where($query);
		return $jr->render();
	}

	function getHeaders() {
		global $_tableHeaders;


		return $_tableHeaders['SeminarArt'];
	}

	function filter() {

	}

}

?>
