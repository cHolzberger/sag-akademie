<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 *
 * liefert alle infos die fuer die neue startseite gebraucht werden zurueck
 */
require_once("templates/sag/JsonComponent.php");

class JSON_Startseite extends JsonComponent {

	function map() {
		return "JSON_Startseite";
	}

	function getTable() {
		return Doctrine::getTable("seminar");
	}

	function forward() {
		if (array_key_exists("data", $_POST)) {
			$this->POST();
		}

		return $this->renderJson();
	}

	function getStatistics(&$data) {
		$startPM = date("Y-m-01 00:00:00", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
		$endPM = date("Y-m-t 00:00:00", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));

		$startPY = date("Y-m-01 00:00:00", mktime(0, 0, 0, 1, 1, date("Y") - 1));
		$endPY = date("Y-m-t 00:00:00", mktime(0, 0, 0, 31, 12, date("Y") - 1));

		$startTY = date("Y-m-01 00:00:00", mktime(0, 0, 0, 1, 1, date("Y")));
		$endTY = date("Y-m-t 00:00:00", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

		$startTM = date("Y-m-01 00:00:00");
		$endTM = date("Y-m-t 00:00:00");

		//  derzeitiger Monat
		$data['currentMonth']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startTM, $endTM);
		$data['currentMonth']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startTM, $endTM);
		$data['currentMonth']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startTM, $endTM);

		// letzter Monat
		$data['currentYear']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startTY, $endTY);
		$data['currentYear']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startTY, $endTY);
		$data['currentYear']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startTY, $endTY);

		// diese Jahr
		$data['lastMonth']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startPM, $endPM);
		$data['lastMonth']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startPM, $endPM);
		$data['lastMonth']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startPM, $endPM);

		// letztes Jahr
		$data['lastYear']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startPY, $endPY);
		$data['lastYear']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startPY, $endPY);
		$data['lastYear']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startPY, $endPY);
	}

	function renderJson() {
		$idDarmstadt = 1;
		$idLuenen = 1;

		$jsonProxy = array();
		$jsonProxy['namespace'] = MosaikConfig::getEnv("namespace", "global");
		$jsonProxy['format'] = "object";
		$jsonProxy['data'] = array();

		$count = MosaikConfig::getEnv("count", 4);

		$q = Doctrine::getTable("Seminar")->getNext($idDarmstadt, $count);
		$jsonProxy['data']['termineDarmstadt'] = $q->fetchArray();

		$q = Doctrine::getTable("Seminar")->getNext($idDarmstadt, $count);
		$jsonProxy['data']['termineLuenen'] = $q->fetchArray();

		// statistik
		$this->getStatistics($jsonProxy['data']);

		return json_encode($jsonProxy);
	}

}
