<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");

class JSON_InhouseSeminar extends JsonComponent {

	function map() {
		return "JSON_InhouseSeminar";
	}

	function getTable() {
		return Doctrine::getTable("ViewInhouseSeminar"); // fixme standort daten muessen auch gespeichert werden
	}

	function beforeSave(&$data, $row) {

		if ($data['freigabe_status'] != $row['freigabe_status']) {
			$data['freigabe_datum'] = currentMysqlDate();
		}
	}

	function search($q) {
		$table = "ViewInhouseSeminar";
		$data = array();
		$q = Doctrine_Query::create();
		return $q->from($table)->where("kontakt_firma = ?", "%" . $q . "%");
	}

	function getBySeminarArt($seminarArtId) {
		//$seminarArt = Doctrine::getTable("SeminarArt")->find($seminarArtId);
		$q = Doctrine_Query::create() 
		 ->from("ViewInhouseSeminar")
		 ->where("seminar_art_id = ?", $seminarArtId);
		
		return $q;
	}

	function getByDate($from, $to) {
		$mFrom = mysqlDateFromLocal($from);
		$mTo = mysqlDateFromLocal($to);
		qlog("From " . $mFrom);
		qlog("To " . $mTo);
		$q = Doctrine_Query::create()
		 ->from("ViewInhouseSeminar")
		 ->where("seminar.datum_begin >= ? AND seminar.datum_begin <= ?", array($mFrom, $mTo));
		 
		return $q;
	}

	function advancedSearch($rules) {
		ksort($rules);
		$table = "ViewInhouseSeminar";
		return Doctrine::getTable($table)->advancedSearch($rules);
	}

	function extend(&$data) {
		//fixme: ?!
		$data['anzahlBestaetigt'] = Doctrine::getTable("ViewBuchungPreis")->countbystatus($data['id'], 1);

		if (isset($_GET['convert'])) {
			$data['datum_begin'] = mysqlDateToLocal($data['datum_begin']);
			$data['datum_ende'] = mysqlDateToLocal($data['datum_ende']);
		}
	}

	function forward($class_name, $namespace = "") {
		qlog(__CLASS__ . "(" . __FILE__ . ":" . __LINE__ . "): " . __FUNCTION__);

		$jr = new MosaikJsonResult();
		$jr->filter = $this;
		$seminar_id = $this->next();
		$standort_id = $_GET['standort_id'];
		$q = Doctrine_Query::create();
		$q->from("ViewInhouseSeminar");
		$q->where("id=?", $seminar_id);
		$q->andWhere("standort_id=?", $standort_id);
		$jr->headline = "Termine " . $q;
		$jr->q = $q;
		$jr->headers = $this->getHeaders();

		return $jr->renderSingle();
	}

	function renderJson() {
		qlog(__CLASS__ . ": renderJson()");
		qdir($_POST);

		$jr = new MosaikJsonResult();
		$jr->filter = $this;

		if (MosaikConfig::getEnv('advancedSearch')) {
			$jr->headline = "Termin erweiterte Suche nach: " . advancedSearchAsString();
			$jr->q = $this->advancedSearch(MosaikConfig::getEnv('rules'));
		} else if (MosaikConfig::getEnv('q')) {
			// USED IN V2
			$jr->headline = "Termin Suche nach: " . $_POST['q'];
			$jr->q = $this->search(MosaikConfig::getEnv('q'));
		} else if (MosaikConfig::getEnv('seminarArt')) {
			// USED IN V2
			qlog(__CLASS__ . " suche nach seminarArt:" . MosaikConfig::getEnv('seminarArt'));
			$jr->headline = "XX";
			$jr->q = $this->getBySeminarArt( MosaikConfig::getEnv('seminarArt') );
		} else if (MosaikConfig::getEnv('v')) {
			// USED IN V2

			$jr->headline = "Termine von " . MosaikConfig::getEnv('v') . " bis " . MosaikConfig::getEnv('b');
			qlog("Termine von " . MosaikConfig::getEnv('v') . " bis " . MosaikConfig::getEnv('b'));

			$jr->q = $this->getByDate(MosaikConfig::getEnv('v'), MosaikConfig::getEnv('b'));
		}

		// V2 Additions
		if (MosaikConfig::getEnv('naechste90Tage', false) == 1) {
			//NAECHSTE 90 TAGE
			qlog("naechste 90 tage");
			$ts = time() + (90 * 24 * 60 * 60);
			$datum = date("Y-m-d", $ts);
			$now = currentMysqlDate();
			$jr->q->andWhere("seminar.datum_begin >= ? AND seminar.datum_ende <= ?", array($now, $datum));
		} else if (MosaikConfig::getEnv('naechste60Tage', false) == 1) {
			// NAECHSTE 60 TAGE
			qlog("naechste 60 tage");
			$ts = time() + (60 * 24 * 60 * 60);
			$datum = date("Y-m-d", $ts);
			$now = currentMysqlDate();
			$jr->q->andWhere("seminar.datum_begin >= ? AND seminar.datum_ende <= ?", array($now, $datum));
		} else if (MosaikConfig::getEnv('naechste14Tage', false) == 1) {
			// NAECHSTE 14 TAGE
			qlog("naechste 14 tage");
			$ts = time() + (14 * 24 * 60 * 60);
			$datum = date("Y-m-d", $ts);
			$now = currentMysqlDate();
			$jr->q->andWhere("seminar.datum_begin >= ? AND seminar.datum_ende <= ?", array($now, $datum));
		}

		$jr->headers = $this->getHeaders();
		return $jr->render();
	}

	function getHeaders() {
		//fixme
		global $_tableHeaders;
		return $_tableHeaders['InhouseSeminar'];
	}

}

?>
