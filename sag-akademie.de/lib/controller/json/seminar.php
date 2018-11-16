<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
require_once("services/BuchungService.php");

class JSON_Seminar extends JsonComponent {

	function map() {
		return "JSON_Seminar";
	}

	function getTable() {
		return Doctrine::getTable("Seminar");
	}

	function beforeSave(&$data, $row) {

		if ($data['freigabe_status'] != $row['freigabe_status']) {
			$data['freigabe_datum'] = currentMysqlDate();
		}
	}

	function search($q) {
		$table = "ViewSeminarPreis";
		$data = array();
		return Doctrine::getTable($table)->tableSearch($q);
	}

	function getByRubrik($rubrikId) {
		$data = Doctrine::getTable("Seminar")->detailedList()->where("seminarArt.rubrik = ?", $rubrikId);
		return $data;
	}

	function getBySeminarArt($seminarArtId) {
		//$seminarArt = Doctrine::getTable("SeminarArt")->find($seminarArtId);
		$data = Doctrine_Query::create()->from("ViewSeminarPreis seminar")->where("seminar.seminar_art_id = ?", $seminarArtId);
		return $data;
	}

	function getByDate($from, $to) {
		$mFrom = mysqlDateFromLocal($from);
		$mTo = mysqlDateFromLocal($to);
		qlog("From " . $mFrom);
		qlog("To " . $mTo);

		$data = Doctrine::getTable("Seminar")->detailedList()->where("seminar.datum_begin >= ? AND seminar.datum_begin <= ?", array($mFrom, $mTo));

		return $data;
	}

	function advancedSearch($rules) {
		if (is_array($rules)) {
			ksort($rules);
		} else if (is_string($rules)) {
			$rules = explode("#:#", $rules);
		}
		$table = "ViewSeminarPreis";
		return Doctrine::getTable($table)->advancedSearch($rules);
	}

	function extend(&$data) {
		$data['anzahlBestaetigt'] = Doctrine::getTable("ViewBuchungPreis")->countbystatus($data['id'], 1);

		if (isset($_GET['convert'])) {
			$data['datum_begin'] = mysqlDateToLocal($data['datum_begin']);
			$data['datum_ende'] = mysqlDateToLocal($data['datum_ende']);
		}
	}

	// im moment nur von der ReferentenPlanung genutzt
	function forward($class_name, $namespace = "") {
		qlog(__CLASS__ . "(" . __FILE__ . ":" . __LINE__ . "): " . __FUNCTION__);

		$jr = new MosaikJsonResult();
		$jr->filter = $this;
		$seminar_id = $this->next();
		$standort_id = $_GET['standort_id'];
		$q = Doctrine_Query::create();
		$q->from("ViewSeminarDauer");
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
		if (MosaikConfig::getEnv('referentId', false)) {
            qlog("Nach Referent: " . MosaikConfig::getEnv('referentId', false));
			$referentId = MosaikConfig::getEnv('referentId', false);
			$jr->q = Doctrine_Query::create()->from("ViewSeminarPreisReferent seminar")
			->where("seminar.referent_id = ?", 
			array($referentId));
			
		} else if (MosaikConfig::getEnv('kontaktId', false)) {
            qlog("Nach Kontakt: " . MosaikConfig::getEnv('kontaktId', false));

            $kontaktId = MosaikConfig::getEnv('kontaktId', false);
			$jr->q = Doctrine_Query::create()->from("ViewSeminarKontakt seminar")
			->where("seminar.kontakt_id = ?", 
			array($kontaktId));
				
		} else if (MosaikConfig::getEnv('advancedSearch',false)) {
            qlog("Nach Advanced Search: " );
			$jr->headline = "Termin erweiterte Suche nach: " . advancedSearchAsString();
			$jr->q = $this->advancedSearch(MosaikConfig::getEnv('rules'));
		} else if (MosaikConfig::getEnv('year', false)) {
			$year = MosaikConfig::getEnv('year');
			qlog(__CLASS__ . "::". __FUNCTION__.": year =>" . $year);

			$jr->q = Doctrine_Query::create()->from("ViewSeminarPreis  seminar")
			 ->where("YEAR(seminar.datum_begin) = ? OR YEAR(seminar.datum_ende) = ?", array($year, $year));
			// ->andWhere("seminar.freigabe_veroeffentlichen = 1");
		} else if (MosaikConfig::getEnv('q',false)) {
			// USED IN V2
            qlog("Nach Query: ". $_POST['q']);

            $jr->headline = "Termin Suche nach: " . $_POST['q'];
			$jr->q = $this->search(MosaikConfig::getEnv('q'));
		} else if (MosaikConfig::getEnv('seminarArt')) {
			// USED IN V2
			qlog(__CLASS__ . " suche nach seminarArt:" . MosaikConfig::getEnv('seminarArt'));
			$seminarArt = Doctrine::getTable("SeminarArt")->find(MosaikConfig::getEnv('seminarArt'));
			$jr->headline = "Termine nach Seminar:  " . $seminarArt->bezeichnung;
			$jr->q = $this->getBySeminarArt(MosaikConfig::getEnv('seminarArt'));
		} else if (MosaikConfig::getEnv('v')) {
			// USED IN V2
            qlog("Nach Von: ". MosaikConfig::getEnv('v'));

            $jr->headline = "Termine von " . MosaikConfig::getEnv('v') . " bis " . MosaikConfig::getEnv('b');
			qlog("Termine von " . MosaikConfig::getEnv('v') . " bis " . MosaikConfig::getEnv('b'));

			$jr->q = $this->getByDate(MosaikConfig::getEnv('v'), MosaikConfig::getEnv('b'));
		} else if (MosaikConfig::getEnv('rubrik')) {
            qlog("Nach Rubrik:". MosaikConfig::getEnv('v'));

            $rubrik = Doctrine::getTable("SeminarArtRubrik")->find($_POST['rubrik']);
			$jr->headline = "Termine in der Rubrik: " . $rubrik->name;
			$jr->q = $this->getByRubrik(MosaikConfig::getEnv('rubrik'));
		} else {
			qlog("Alle Termine");
			$tbl = Doctrine::getTable("ViewSeminarPreis");
			$jr->q = $tbl->findAll(Doctrine::HYDRATE_ARRAY);
		}

		// V2 Additions
		// do not process this if we query for a whole year
		// if ( MosaikConfig::getEnv("year", false) == false ) {
			if (MosaikConfig::getEnv("planungAnzeigen") != "1" ) {
				qlog("Planung nicht anzeigen");
				$jr->q->andWhere("seminar.freigabe_status = ? OR seminar.freigabe_status = ?", array(STATUS_FREIGEGEBEN, STATUS_AUSGEBUCHT));
			}
			
			if (MosaikConfig::getEnv('naechste90Tage', false) == 1) {
				//NAECHSTE 90 TAGE
				qlog("naechste 90 tage");
				$ts = time() + (90 * 24 * 60 * 60);
				$datum = date("Y-m-d", $ts);
				$now = currentMysqlDate();
				$jr->q->andWhere("seminar.datum_begin >= ?", array($now));
				$jr->q->andWhere("seminar.datum_ende <= ?", array($datum));
			} else if (MosaikConfig::getEnv('naechste60Tage', false) == 1) {
				// NAECHSTE 60 TAGE
				qlog("naechste 60 tage");
				$ts = time() + (60 * 24 * 60 * 60);
				$datum = date("Y-m-d", $ts);
				$now = currentMysqlDate();
				$jr->q->andWhere("seminar.datum_begin >= ?", array($now));
				$jr->q->andWhere("seminar.datum_ende <= ?", array($datum));
			} else if (MosaikConfig::getEnv('naechste14Tage', false) == 1) {
				// NAECHSTE 14 TAGE
				qlog("naechste 14 tage");
				$ts = time() + (14 * 24 * 60 * 60);
				$datum = date("Y-m-d", $ts);
				$now = currentMysqlDate();
				$jr->q->andWhere("seminar.datum_begin >= ?", array($now));
				$jr->q->andWhere("seminar.datum_ende <= ?", array($datum));
			}

			if (  ( MosaikConfig::getEnv("termineDarmstadt",false) == "1" &&  MosaikConfig::getEnv("termineLuenen",false) == "1" ) ||
				MosaikConfig::getEnv("termineDarmstadt",false) == false &&  MosaikConfig::getEnv("termineLuenen",false) == false) {
                qlog("Alle Standorte");
			} else if ( MosaikConfig::getEnv("termineDarmstadt",false) == "1") {
                qlog("Darmstadt");

                $jr->q->andWhere("seminar.standort_id = ?", array(BuchungService::STANDORT_DARMSTADT));
			} else if ( MosaikConfig::getEnv("termineLuenen",false) == "1") {
                qlog("Luenen");
				$jr->q->andWhere("seminar.standort_id = ?", array(BuchungService::STANDORT_LUENEN));
			}
		//}
		$jr->q->orderBy("seminar.datum_begin");
		$jr->headers = $this->getHeaders();
		return $jr->renderAll();
	}

	function getHeaders() {
		//<dbfield name="anzahlBestaetigt" label="Best&auml;tigte Buchungen" style="width:80px;" />
		//<dbfield name="anzahlNichtBestaetigt" label="Nichtbest&auml;tigte Buchungen" style="width:110px;" />
		global $_tableHeaders;
		return $_tableHeaders['Seminar'];
	}

}

?>
