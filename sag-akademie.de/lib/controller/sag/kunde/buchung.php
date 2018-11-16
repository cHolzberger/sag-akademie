<?php

// FIXME: auf neues EVENT MODEL UMBAUEN!!!
include_once ("Events/BuchungTeilnehmerChangeEvent.php");
include_once("Events/BuchungEditEvent.php");
include_once("Events/BuchungInfoEvent.php");
include_once("Events/BuchungNewEvent.php");
include_once("Events/BuchungStornoEvent.php");
include_once("Events/BuchungUmgebuchtEvent.php");
include_once("services/BuchungService.php");

class SAG_Kunde_Buchung extends SAG_Component {

	var $entryTable = "buchung";
	var $entryClass = "Buchung";

	function construct() {
		list($config, $content) = $this->createPageReader();

		global $ormMap;
		$this->key = "";
		$this->dTable = Doctrine::getTable($this->entryClass);
		$this->dsDb = new MosaikDatasource("dbtable");
	}

	function map($name) {
		return "SAG_Kunde_Buchung";
	}

	function GET() {
		return "";
	}

	function forward($class, $namespace = "") {
		// big page reader
		MosaikDebug::infoDebug("Mosaik_Buchung->forward");

		$GLOBALS['dbselectGet'] = $this;
		$key = $this->next();
		$this->key = $key;

		list($config, $content) = $this->createPageReader();
		$content->addDatasource($this->dsDb);

		$page = "buchungen";

		$result = null;

		if (isset($_GET['teilnehmer'])) {
			
			$result = $this->dTable->detailedByUuid("ViewBuchungPreis", $_GET['teilnehmer'])->fetchArray(array($key));
		} else {
			$result = $this->dTable->detailedByUuid()->fetchArray(array($key));
		}

		$add["formaction"] = $this->url($this->next() . "?umbuchen=1");

		if (is_array($result) && count($result) > 0 && !empty($result[0])) {
			if (isset($_GET['storno'])) {
				$this->onStorno();
			} else if (isset($_GET['stornoTeilnehmer'])) {
				$this->onStornoTeilnehmer();
				$page = "buchungen_storniert";
			} else if (isset($_GET['bestaetigtTeilnehmer'])) {
				$this->onBestaetigungTeilnehmer();
				$page = "buchungen_bestaetigt";
			} else if (isset($_GET['bestaetigt'])) {
				$this->onBestaetigung();
				$page = "buchungen_bestaetigt";
			} else if (isset($_GET['umbuchen']) && isset($_POST['person_id'])) {
				$this->onPersonUmbuchen();
				$page = "buchungen_person_umgebucht";
			} else if (isset($_GET['umbuchen']) && $result[0]['Seminar']['id'] != $_POST['seminar']['id']) {
				$this->onUmbuchen();
				$page = "buchungen_umgebucht";
			} else if (isset($_GET['hotelUmbuchen'])) {
				$this->onHotelUmbuchen();
				$add["formaction"] = $this->url($this->next() . "?hotelUmbuchen=1&teilnehmer=" . $_GET['teilnehmer']);
				$page = "hotel_umbuchen";
			} else if (isset($_GET['zertifikatAnfordern'])) {
				$this->onZertifikatAnfordern();
				$page = "zertifikat_angefordert";
			}

			$add['Buchungen'] = $result;
			$add['bestaetigt'] = 1;
			foreach ($add['Buchungen'] as $buchung) {
				if ($buchung['bestaetigt'] == 0) {
					$add['bestaetigt'] = 0;
				}
			}

			$add['parent'] = "/kunde/buchung/" . $this->key;
			$add['datum'] = $result[0]['datum'];
			$add['Kontakt'] = $result[0]['Person']['Kontakt'];
			$add['Seminar'] = $add['Buchungen'][0]['Seminar'];

			$this->seminar = $add['Buchungen'][0]['Seminar'];
			$add['dbtable'] = 'seminar';
			// fuer dbselect
			$add['dbclass'] = 'Seminar';
			// fuer dbselect

			foreach ($add['Buchungen'] as $key => $buchung) {
				$add['Buchungen'][$key]['dbtable'] = 'seminar';
				// fuer dbselect
				$add['Buchungen'][$key]['dbclass'] = 'Seminar';
				// fuer dbselect
				$add['Buchungen'][$key]["formaction"] = $this->url($this->next() . "?umbuchen=1&buchungId=" . $buchung['id']);

				$add['Buchungen'][$key]['bestaetigenLink'] = $this->url($this->next() . "?bestaetigtTeilnehmer=1&teilnehmer=" . $buchung['person_id']);
				$add['Buchungen'][$key]['stornoLink'] = $this->url($this->next() . "?stornoTeilnehmer=1&teilnehmer=" . $buchung['person_id']);
				$add['Buchungen'][$key]['zertifikatAnfordernLink'] = $this->url($this->next() . "?zertifikatAnfordern=1&teilnehmer=" . $buchung['person_id']);

				$add['Buchungen'][$key]['hotelLink'] = $this->url($this->next() . "?hotelUmbuchen=1&teilnehmer=" . $buchung['person_id']);

				if ($buchung['HotelBuchung']['storno_datum'] == "0000-00-00") {

					$hotelpreis = Doctrine::getTable("Hotel")->getPreis($buchung['HotelBuchung']['Hotel']['id'], $buchung['Seminar']['datum_begin'], "ViewHotelPreis")->fetchArray();

					if (count($hotelpreis) > 0) {
						$add['Buchungen'][$key]['HotelBuchung']['verkaufspreis_ez'] = $hotelpreis[0]['verkaufspreis_ez'];
						$add['Buchungen'][$key]['HotelBuchung']['verkaufspreis_dz'] = $hotelpreis[0]['verkaufspreis_dz'];
						$add['Buchungen'][$key]['HotelBuchung']['Hotel']['Preis'] = $hotelpreis[0];
					}
				} else {
					$add['Buchungen'][$key]['HotelBuchung'] = null;
				}

				// statisch
				$add['Buchungen'][$key]['Personen'] = Doctrine::getTable("Person")->findByKontaktId($add['Kontakt']['id'])->toArray(true);
			}

			$add['bestaetigenLink'] = $this->url($this->next() . "?bestaetigt=1");
			$add['stornoLink'] = $this->url($this->next() . "?storno=1");

			foreach ($add['Seminar']['Standort']['Hotels'] as $key => $hotel) {
				$hotelpreis = Doctrine::getTable("Hotel")->getPreis($hotel['id'], $add['Seminar']['datum_begin'], "ViewHotelPreis")->fetchArray();
				if (count($hotelpreis) > 0) {
					$add['Seminar']["Standort"]['Hotels'][$key]['Preis'] = $hotelpreis[0];
				}
			}

			/* alternative Semiare */

			//	$q = Doctrine_Query::create();
			//	$q->from("Seminar seminar")->where("seminar.seminar_art_id = ?", $add['Seminar']['seminar_art_id'] )->andWhere("seminar.freigabe_status=?", 2)->leftJoin("seminar.Standort standort")->leftJoin($join)join("standort.Ort ort");
			//	$add['AlternativeSeminare']  = $q->fetchArray();
			$add['seminar'] = $add['Seminar'];
			// fuer dbselect
			MosaikDebug::infoDebug("Seminar");
			//MosaikDebug::infoDebug($add['Seminar']);

			$this->dsDb->set($add);
			$this->dsDb->log();
			$variable = array("page_background" => "/img/header_bg_gross.jpg");
			$content->loadPage("kunde/$page.xml");
		} else {
			//fixme

			$content->loadPage("kunde/buchungen_empty.xml");
		}

		return $content->output->get();
	}

	function onHotelUmbuchen() {
		if (isset($_POST['HotelBuchung'])) {
			// buchung holen
			$buchung = $this->dTable->getId($this->key, $_GET['teilnehmer'])->execute()->getFirst();

			$hb = new HotelBuchung();

			$hb->buchung_id = $buchung->id;
			$hotelpreis = Doctrine::getTable("Hotel")->getPreis($_POST['HotelBuchung']['hotel_id'], $hb->Buchung->Seminar->datum_begin, "ViewHotelPreis")->fetchArray();
			$hb->merge($hotelpreis[0]);
			$hb->merge($_POST['HotelBuchung']);
			$hb->id = 0;
			$hb->save();

			$buchung = $this->dTable->detailed()->execute(array($buchung->id));
			$this->sendMail($buchung->toArray(true), $this->key, "hotel_umgebucht");

			instantRedirect("/kunde/buchung/" . $this->key);
		}
	}

	function sendMail($data, $uuid, $template) {
		$user = Mosaik_ObjectStore::getPath("/current/identity");

		$email = new MosaikEmail();
		$email->setContainer("__engine");
		$email->setPage("kunde/" . $template);
		$email->addData("bemerkung", $data[0]['bemerkung']);
		$email->addData("info_kunde", $data[0]['info_kunde']);
		$email->addData("Buchungen", $data);
		$email->addData("Buchung", $data[0]);
		$email->addData("Person", $data[0]['Person']);
		$email->addData("Seminar", $data[0]['Seminar']);
		$email->addData("HotelBuchung", $data[0]['HotelBuchung']);

		$email->addData("Kontakt", $user->getKontakt()->toArray(true));
		$email->addData("Ansprechpartner", $user->toArray());
		$email->addData("uuid", $uuid);
		$email->addData("link", MosaikConfig::getVar("webUrl") . "kunde/buchung/$uuid");

		$ansprechpartner = Doctrine::getTable("ViewBuchungAnsprechpartner")->find($uuid);
		$seminar = Doctrine::getTable("ViewBuchungSeminar")->find($uuid);

		$kursnr = $seminar->kursnr;
		$firma = "";
		$emailAddr = "";
		if (is_object($ansprechpartner)) {
			$firma = $ansprechpartner->firma;
			$emailAddr = $ansprechpartner->email;
		}

		$datum_begin = mysqlDateToLocal($seminar->datum_begin);

		$email->send($emailAddr, SMTP_SENDER, $kursnr . " - " . $datum_begin . " - " . $firma, SMTP_ADMIN_RECIVER);
	}

	function onBestaetigung() {
		$resultSet = $this->dTable->detailedByUuid("Buchung")->execute(array($this->key));

		foreach ($resultSet as $result) {
			$result->bestaetigt = 1;
			$result->bestaetigungs_datum = currentMysqlDate();
			$result->save();
		}

		$this->sendMail($resultSet->toArray(true), $this->key, "buchungen_bestaetigt");
	}

	function onBestaetigungTeilnehmer() {
		$teilnehmerId = $_GET['teilnehmer'];
		$resultSet = $this->dTable->detailedByUuid("Buchung")->andWhere("a.person_id = ?")->execute(array($this->key, $teilnehmerId));
		$resultSet[0]->bestaetigt = 1;
		//currentMysqlDate();
		$resultSet[0]->bestaetigungs_datum = currentMysqlDate();
		$resultSet[0]->save();

		$this->sendMail($resultSet->toArray(true), $this->key, "buchungen_bestaetigt");
	}

	function onStorno() {
		MosaikDebug::infoDebug($this->key, "Mosaik_Buchung::onStorno");

		// zum umbuchen
		$buchungen = $this->dTable->findByUuid($this->key);
		// fuer die email
		$resultSet = $this->dTable->detailedByUuid()->execute(array($this->key));

		foreach ($buchungen as $result) {
			if ($result->storno_datum == "0000-00-00") {
				$result->storno_datum = currentMysqlDate();
			}
			$result->save();
		}

		$this->sendMail($resultSet->toArray(true), $this->key, "buchungen_storniert");
	}

	function onZertifikatAnfordern() {
		// ZERTIFKAT ANFORDERN
		// fuer die email
		$resultSet = $this->dTable->detailedByUuid()->execute(array($this->key));
		$person = $resultSet[0]->Person;
		// neuen task anlegen
		$buchung = $resultSet[0];
		$seminar = $buchung->Seminar;
		
		$aufgabe = new XTodo();
		$aufgabe->betreff = "Zertifikat austellen für: " .$person->name . ", ". $person->vorname . " (". $person->Kontakt->firma.")";
		$aufgabe->buchung_id = $resultSet[0]->id;
		$aufgabe->person_id = $resultSet[0]->person_id;
		$aufgabe->seminar_id = $resultSet[0]->seminar_id;
		$aufgabe->rubrik_id = 2;
		$aufgabe->kategorie = 4;
		$aufgabe->kategorie_id = 4;
		
		$aufgabe->status_id = 1;
		$aufgabe->erstellt_von_id = -1;
		$aufgabe->erstellt = currentMysqlDate();		
		
		$aufgabe->notiz = "Anfrage aus dem Kundenbereich,<br/>".
		"Person: " . $person->name . ", " . $person->vorname . "<br/>".
		"Kontakt: " . $person->Kontakt->firma. "<br/>".
		"KursNr.: " . $seminar->kursnr;
		
		$aufgabe->save(); 
		
		$this->sendMail($resultSet->toArray(true), $this->key, "buchungen_zertifikat_anfordern");
	}

	function onStornoTeilnehmer() {
		MosaikDebug::infoDebug($_GET['teilnehmer'], "Mosaik_Buchung::onStornoTeilnehmer");

		$teilnehmerId = $_GET['teilnehmer'];

		$resultSet = $this->dTable->detailedByUuid("Buchung")->andWhere("buchung.person_id = ?",$teilnehmerId)->execute(array($this->key));
		//MosaikDebug::infoDebug($resultSet, "Mosaik_Buchung::onStornoTeilnehmer");
		//MosaikDebug::infoDebug(count($resultSet), "Mosaik_Buchung::onStornoTeilnehmer count");

		$resultSet[0]->storno_datum = currentMysqlDate();
		$resultSet[0]->Person;
		$resultSet[0]->Seminar->SeminarArt;
		$resultSet[0]->HotelBuchung;
		if (is_object($resultSet[0]->HotelBuchung)) {
			$resultSet[0]->HotelBuchung->Hotel;
		}
		$resultSet[0]->save();
		$ret = $resultSet->toArray(true);
		for ($i = 0; $i < count($ret); $i++) {
			if (!isset($ret[$i]['HotelBuchung'])) {
				$ret[$i]['HotelBuchung'] = null;
			}
		}

		$this->sendMail($ret, $this->key, "buchungen_storniert");
	}

	function onPersonUmbuchen() {
		$evt = new BuchungTeilnehmerChangeEvent();
		$resultSet = $this->dTable->detailedByUuid("Buchung")->andWhere("buchung.id = ?", $_GET['buchungId'])->execute(array($this->key));

		$evt->buchungId = $resultSet[0]->id;
		$evt->teilnehmerFrom = $resultSet[0]['person_id'];
		$evt->teilnehmerTo = $_POST['person_id'];

		$resultSet[0]['person_id'] = $_POST['person_id'];
		$resultSet[0]->save();

		$evt->sendMail = true;

		BuchungService::dispatchEvent($evt);
	}

	function onUmbuchen() {
		qlog ("Umbuchung {$this->key}");
		$add = array();

		$seminarId = $_POST['seminar']['id'];
		$resultSet = array();

		if (isset($_GET['buchungId'])) {
			$resultSet = $this->dTable->detailedByUuid("Buchung")->andWhere("buchung.id = ?", $_GET['buchungId'])->execute(array($this->key ));
		} else {
			$resultSet = $this->dTable->detailedByUuid("Buchung")->execute(array($this->key));
		}

		$alteBuchung = array();
		$neueBuchung = array();
		
		foreach ($resultSet as $key => $result) {
			qlog("Result found converting...");
			$alteBuchung = $result->toArray(true);
			$neueBuchung = $result->copy(false);

			// daten der neuen Buchung setzen
			$neueBuchung->id = 0;
			$neueBuchung->seminar_id = $seminarId;
			$neueBuchung->bestaetigt = 0;
			$neueBuchung->bestaetigungs_datum = "0000-00-00";
			$neueBuchung->geaendert_von = -1;
			$neueBuchung->uuid = $alteBuchung['uuid'];
			$neueBuchung->alte_buchung = $alteBuchung->id;

			$neueBuchung->save();
			
			// fixme this sux!
			$neueBuchung->refresh(true);

			// die alte buchung stornieren
			$result->uuid = md5(microtime());
			$result->umbuchungs_datum = currentMysqlDate();
			$result->umgebucht_id = $neueBuchung['id'];
			$result->save();


			
			
			// derzeitge hotel buchung stonieren
			if (is_object($resultSet[$key]->HotelBuchung)) {
				$hotelBuchung = Doctrine::getTable("HotelBuchung")->find($resultSet[$key]->HotelBuchung->id);
				$hotelBuchung->storno_datum = currentMysqlDate();
				$hotelBuchung->save();
				$add[$key]['HotelBuchung'] = $hotelBuchung->toArray(true);
			}
			
			
			//$add[$key] = $neueBuchung->toArray(true);
			//$add[$key]['Seminar']['Standort'] = $neueBuchung->Seminar->Standort->toArray(true);
			//$add[$key]['Seminar']['Standort']['Ort'] = $neueBuchung->Seminar->Standort->Ort->toArray(true);

			//$add[$key]['HotelBuchung'] = array();

			

			//$add[$key]["AlteBuchung"] = $alteBuchung;
		}

		//$this->sendMail($add, $this->key, "buchungen_umgebucht");
		/* !!! EVENT SENDEN ** */
		$event = new BuchungUmgebuchtEvent();
		$event->newTargetId = $neueBuchung->uuid;
		$event->oldTargetId = $alteBuchung['id'];
		$event->sendMail = true;
		BuchungService::dispatchEvent($event);
	}

	function dbselectGetData($fromTable) {
		// quick and dirty
		$q = Doctrine_Query::create();
		$q->from("Seminar seminar")->where("seminar.seminar_art_id = ?", $this->seminar['seminar_art_id'])->andWhere("seminar.freigabe_status=?", 2)->leftJoin("seminar.Standort standort")->leftJoin("standort.Ort ort");

		$results = $q->execute();
		$res = array();

		foreach ($results as $result) {
			if (mysqlDateToSeconds($result->datum_begin) > time()) {
				$result->refreshRelated();
				$result->Standort->refreshRelated();
				$result->Standort->Ort->refreshRelated();
				$tmp = $result->toArray(true);
				$tmp['datum_begin'] = mysqlDateToLocal($tmp['datum_begin']);
				$tmp['datum_ende'] = mysqlDateToLocal($tmp['datum_ende']);
				$res[] = $tmp;
			}
		}

		return $res;
	}

	function getEntry() {
		$cl = $this->entryClass;
		return new $cl();
		// FIXME
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
	
	function addInhouseTeilnehmer() {
		// wennn inhouse teilnehmer vorhanden (person_id ist gesetzt)
		
		// wenn teilnehmer nicht vorhanden (person_id ist nicht gesetzt)
		
	}

}
?>