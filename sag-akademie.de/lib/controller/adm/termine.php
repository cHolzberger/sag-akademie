<?php

include("adm/dbcontent.php");
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
include_once("Events/TerminNewEvent.php");
include_once("Events/TerminAbgesagtEvent.php");
include_once("services/TerminService.php");

class ADM_Termine extends ADM_DBContent {

	function map($name) {
		return "ADM_Termine";
	}

	function mapIntention($int) {
		if ($int == "showList" && isset($_GET['list'])) {
			return "show";
		}
		else
			return $int;
	}

	function onSearch(&$pr, &$ds) {
		$fn = $this->name() . "/search.xml";
		$pr->loadPage($fn);
	}

	function onDelete(&$pr, &$ds) {
		$fn = $this->name() . "/delete.xml";
		$this->delete();
		$pr->loadPage($fn);
	}

	function onEdit(&$pr, &$ds) {
		$standorte = Doctrine::getTable("Standort")->findAll(Doctrine::HYDRATE_ARRAY);
		foreach ($standorte as $key => $x) {
			$standorte[$key]['seminar_id'] = $this->entryId; // quickbugfix
		}
		$ds->add("Standort", $standorte);

		$obj = Doctrine::getTable("Seminar")->detailed()->execute($this->entryId)->getFirst();
		$ds->add("SeminarArt", $obj->SeminarArt->toArray());
		$ds->add("seminar", $obj->toArray(true));
		$ds->add("Seminar", $obj->toArray(true));
		$cB = Doctrine::getTable("ViewBuchungPreis")->countbystatus($this->entryId, 1);
		$cN = Doctrine::getTable("ViewBuchungPreis")->countbystatus($this->entryId, 0);
		$cS = Doctrine::getTable("ViewBuchungPreis")->countStorno($this->entryId);
		$cU = Doctrine::getTable("ViewBuchungPreis")->countUmbuchung($this->entryId);
		$nT = Doctrine::getTable("ViewBuchungPreis")->countNichtTeilgenommen($this->entryId);

		$ds->add("anzahlBestaetigt", $cB);
		$ds->add("anzahlNichtTeilgenommen", $nT);
		$ds->add("anzahlStorniert", $cS);
		$ds->add("anzahlUmgebucht", $cU);
		$ds->add("jahr", substr($obj->datum_begin, 0, 4));
		$fn = $this->name() . "/edit.xml";
		$pr->loadPage($fn);
	}

	function onNew(&$pr, &$ds) {
		if (!isset($_GET['seminarArt'])) {
			$sar = Doctrine::getTable("SeminarArtRubrik")->detailed()->fetchArray();
			$ds->add("SeminarArtRubrik", $sar);
			$fn = $this->name() . "/seminarArtAuswahl";
			$pr->loadPage($fn);
		} else {
			$sa = Doctrine::getTable("SeminarArt")->detailed($_GET['seminarArt'])->fetchArray();
			$sa = $sa[0];

			$ds->add("SeminarArt", $sa);

			$fn = $this->name() . "/edit.xml";
			$pr->loadPage($fn);
		}
	}

	function onIndex(&$pr, &$ds) {
		return $this->onShowList($pr, $ds);
	}

	function onShowList(&$pr, &$ds) {
		$GLOBALS['dbtableDataFetch'] = $this;
		if (isset($_GET['v'])) {
			/* 	$data = Doctrine::getTable("Seminar")->detailedList()->where("seminar.datum_begin >= ? AND seminar.datum_ende <= ?", array (
			  mysqlDateFromLocal($_GET['v']),
			  mysqlDateFromLocal($_GET['b']))
			  );

			  $data = $data->fetchArray();

			  foreach ( $data as $key=>$item) {
			  $data[$key]['anzahlBestaetigt']= Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 1);
			  $data[$key]['anzahlNichtBestaetigt']=Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 0);
			  } */
			//$hd = "Termine von ". $_GET['v'] . " bis " . $_GET['b'];
			//$ds->add("Headline", $hd);
			//$ds->add("Seminar", $data);
			//$ds->log();
			$pr->loadPage($this->name() . "/list");
		} else if (isset($_GET['seminarArt'])) {
			//$seminarArt = Doctrine::getTable("SeminarArt")->find($_GET['seminarArt']);
			//$hd = "Termine des folgenden Kurses:  " .  $seminarArt->bezeichnung;
			//$ds->add("Headline", $hd);
			$pr->loadPage($this->name() . "/list");
		} else if (isset($_GET['rubrik'])) {
			//$rubrik = Doctrine::getTable("SeminarArtRubrik")->find($_GET['rubrik']);
			/*
			  $data = Doctrine::getTable("Seminar")->detailedList()->where("seminarArt.rubrik = ?", $_GET['rubrik']);
			  $data = $data->fetchArray();

			  foreach ( $data as $key=>$item) {
			  $data[$key]['anzahlBestaetigt']= Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 1);
			  $data[$key]['anzahlNichtBestaetigt']=Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 0);
			  }

			  $ds->add("Seminar", $data); */
			//$hd = "Termine in der Rubrik: " . $rubrik->name;
			//$ds->add("Headline", $hd);
			//$ds->log();
			$pr->loadPage($this->name() . "/list");
		} else {

			$sar = Doctrine::getTable("SeminarArtRubrik")->overview()->fetchArray();
			$ds->add("SeminarArtRubrik", $sar);


			$fn = $this->name();
			$pr->loadPage($fn);
		}
	}

	function onShowDetail(&$pr, &$ds) {
		$GLOBALS['dbtableDataFetch'] = $this;
		$GLOBALS['firephp']->log($this->entryId, "id:");
		$data = Doctrine::getTable("Seminar")->detailed();
		$data = $data->fetchArray(array($this->entryId));
		$data = $data[0];
		$ds->add("Seminar", $data);
		$ds->add("BuchungenGesamt", count($data['Buchungen']));
		$best = 0;
		foreach ($data['Buchungen'] as $buchung) {
			if ($buchung ['bestaetigt'] != 0) {
				$best++;
			}
		}
		$ds->add("BuchungenBestaetigt", $best);

		$fn = $this->name() . "/show.xml";

		$pr->loadPage($fn);
	}

	function onSave(&$pr, &$ds) {
		$tSeminarArt = Doctrine::getTable("SeminarArt");

		$result = $this->getOneClass($this->entryId);
		$data = $_POST[$this->entryClass];
		// quick fix
		$data['standort_id'] = $_POST['Seminar']['standort_id'];

		$seminarArt = $_POST['SeminarArt'];
		$data['seminar_art_id'] = $seminarArt['id'];
		$data['datum_begin'] = mysqlDateFromLocal($data['datum_begin']);
		$data['datum_ende'] = mysqlDateFromLocal($data['datum_ende']);
		$data['storno_datum'] = mysqlDateFromLocal($data['storno_datum']);
		$data["geaendert_von"] = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
		$data["geaendert"] = currentMysqlDatetime();

		//if ($result->storno_datum != $data['storno_datum']) {
		// seminar wurde gerade storniert von SAG
		//$evt = new TerminAbgesagtEvent();
		//$evt->sendMail = true;
		//$evt->seminarId = $result->id;
		//TerminService::getInstance()->dispatchEvent($evt);
		//}

		$result->merge($data);
		$result->save();

		/* kopiert die referenten vorlage aus der tabelle seminar_art_referent in die tabelle seminar_referent */
		if ($this->entryId == "new") {
			// !!! EVENT SENDEN
			$event = new TerminNewEvent();
			$event->targetId = $result->id;

			TerminService::getInstance()->dispatchEvent($event);
		}
		$this->entryId = $result->id;

		$fn = $this->name() . "/saved.xml";
		$pr->loadPage($fn);
		instantRedirect("/admin/" . $this->name() . "/" . $this->entryId . ";iframe?edit");
	}

	/** Data fetcher function * */
	function fetchOne($id, $res=false) {
		$info = Doctrine::getTable("Seminar")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
		// FIXME: optimize and put into SeminarTable
		$info['anzahlBestaetigt'] = Doctrine::getTable("ViewBuchungPreis")->countbystatus($id, 1);
		$info['anzahlNichtBestaetigt'] = Doctrine::getTable("ViewBuchungPreis")->countbystatus($id, 0);

		if (@ $info['Buchungen'][0]['id'] == NULL) {
			$info['Buchungen'] = array();
		}
		return $info;
	}

	function getData($table) {
		$data = parent::getData($table);

		foreach ($data as $key => $item) {
			if (is_object($item)) {
				$item->mapValue('anzahlBestaetigt', Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 1));
				$item->mapValue('anzahlNichtBestaetigt', Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 0));
			} else {
				$data[$key]['anzahlBestaetigt'] = Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 1);
				$data[$key]['anzahlNichtBestaetigt'] = Doctrine::getTable("ViewBuchungPreis")->countbystatus($item['id'], 0);
			}
		}
		return $data;
	}

}
