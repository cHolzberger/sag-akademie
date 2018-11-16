<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once ("templates/sag/JsonComponent.php");
require_once ("Mosaik/JsonResult.php");
require_once ("services/BuchungService.php");

class JSON_Buchung extends JsonComponent {

	function map($name="") {
		return "JSON_Buchung";
	}

	function getTable() {
		return Doctrine::getTable("Buchung");
	}

	function advancedSearch($rules) {
		if (is_array($rules)) {
			ksort($rules);
		} else if (is_string($rules)) {
			$rules = explode("#:#", $rules);
		}
		$table = "ViewBuchungPreis";
		return Doctrine::getTable($table)->advancedSearch($rules);
	}

	function renderJson() {
		qlog(__CLASS__ . "::" . __FUNCTION__);
		qdir($_POST);
		$jr = new MosaikJsonResult();
		$cacheId = "buchung_";
		$headline = "";

		// NEW INTERFACE WITH COMBINDES FILTERS
		// USED ON NEW START PAGE
		if (MosaikConfig::getEnv("version") == "2") {
			// Query aus der neuen Admin UI

			$q = null;
			MosaikConfig::logEnv();

			if (MosaikConfig::getEnv("siebenTageRueckblick") == "1") {
				qlog("SiebenTageRueckblick");
				$bs = new BuchungService();
				$bs->getBuchungenByBuchungsdatumStartingFrom(null, -7, BuchungService::BUCHUNGS_DATUM, false);
				$q = $bs->currentQuery;
				$cacheId.="siebentagerueckblick_";
			} else {
				qlog("Normal");
				$q = Doctrine_Query::create()->from("ViewBuchungPreis")
				->leftJoin("ViewBuchungPreis.Seminar seminar")
				->where("deleted_at = ?", "0000-00-00 00:00:00")->orderBy("datum");
				$cacheId.="normal_";
			}

			if (MosaikConfig::getEnv("neueBuchungen") == "1") {
				qlog("NeueBuchungen seit:" . MosaikConfig::getEnv("lastLogin"));
				$cacheId.="lastlogin_".MosaikConfig::getEnv("lastLogin");
				$q->andWhere("datum >= ?", MosaikConfig::getEnv("lastLogin"));
			}
			
			
			if (MosaikConfig::getEnv("buchungenDarmstadt") == "1") {
				$cacheId.="darmstadt_";
				$q->andWhere("ViewBuchungPreis.Seminar.standort_id = ?", array(BuchungService::STANDORT_DARMSTADT));
			}

			if (MosaikConfig::getEnv("buchungenLuenen") == "1") {
				$cacheId.="luenen_";
				$q->andWhere("ViewBuchungPreis.Seminar.standort_id = ?", array(BuchungService::STANDORT_LUENEN));
			}

			$q->orderBy("datum DESC");
		} else if (($personId = MosaikConfig::getEnv("personId", false))) {
			$jr->setCacheId("buchung_person_" + $personId);
			$cacheId.="person_{$personId}";
			qlog("PersonId: " . $personId);
			$q = Doctrine_Query::create();
			$q->from("ViewBuchungPreis")->where("person_id = ?", array($personId));
			$headline = "Person";
		} else if (MosaikConfig::getEnv('advancedSearch')) {
			//MosaikDebug::msg($_POST, "POST");
			$q = $this->advancedSearch(MosaikConfig::getEnv('rules'));
			$headline = "Buchungen - erweiterte Suche";
			$jr->setNocache(true);
		} else if (MosaikConfig::getEnv('month') !== null) {
			$q = Doctrine::getTable("ViewBuchungPreis")->important(MosaikConfig::getEnv('year'), MosaikConfig::getEnv('month'));
			$headline = "Buchungen - " . (MosaikConfig::getEnv('month') + 1) . "." . MosaikConfig::getEnv('year');
			$cacheId.="month_" . (MosaikConfig::getEnv('month') + 1) . "." . MosaikConfig::getEnv('year');
			//MosaikDebug::msg($info[0], "ViewBuchungPreis");
		} else if (MosaikConfig::getEnv('year')) {
			$q = Doctrine::getTable("ViewBuchungPreis")->important(MosaikConfig::getEnv('year'));
			$headline = "Buchungen - " . MosaikConfig::getEnv('year');
			$cacheId.="year_".MosaikConfig::getEnv('year');
			//MosaikDebug::msg($info[0], "ViewBuchungPreis");
		} else if (MosaikConfig::getEnv('kursnr')) {
			$q = Doctrine::getTable("ViewBuchungPreis")->important()->andWhere("seminar.kursnr LIKE ?", "%" . MosaikConfig::getEnv('kursnr') . "%");
			$headline = "Buchungen - " . MosaikConfig::getEnv('kursnr');
			$cacheId.="kursnr".MosaikConfig::getEnv('kursnr');
			
		} else if (MosaikConfig::getEnv('seminar')) {
			$q = Doctrine::getTable("ViewBuchungPreis")->important()->andWhere("seminar_art.id LIKE ?", "%" . MosaikConfig::getEnv('seminar') . "%");
			$headline = "Buchungen - " . MosaikConfig::getEnv('seminar');
			$cacheId.="seminar_".MosaikConfig::getEnv('seminar');
			
		} else if (MosaikConfig::getEnv('lastweek')) {
			$bs = new BuchungService();
			$bs->getBuchungenByBuchungsdatumStartingFrom(null, -7, BuchungService::BUCHUNGS_DATUM);
			$q = $bs->lastQuery;
			$headline = "Buchungen - 7 Tage Rückblick";
			$cacheId.="lastweek";
			
		} else if (MosaikConfig::getEnv('next2week')) {
			$bs = new BuchungService();
			$bs->getBuchungenByBuchungsdatumStartingFrom(null, 14, BuchungService::SEMINAR_DATUM);
			$q = $bs->lastQuery;
			$headline = "Teilnehmer - 14 Tage Vorschau";
			$cacheId.="next2week";
		} else if (MosaikConfig::getEnv('trash')) {
			$q = Doctrine::getTable("ViewBuchungenPapierkorb")->trash();
			$headline = "Buchungen - Papierkorb";
			$cacheId.="trash";
		} else {
			$q = Doctrine::getTable("ViewBuchungPreis")->important();
			$headline = "Buchungen";
			$cacheId.="all";
		}
		$jr->headers = $this->getHeaders();

		// add the statement to the json response
		$jr->headline = $headline;
		$jr->q = $q;
		$jr->setCacheId($cacheId);

		return $jr->renderAll("ViewBuchungPreis");
	}

	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['Buchung'];
	}

	function filter() {

	}

}
?>
