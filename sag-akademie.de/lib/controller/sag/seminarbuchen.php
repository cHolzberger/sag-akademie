<?php

include_once ("mosaikTaglib/dlog.php");
include_once("Events/BuchungNewEvent.php");
include_once("services/BuchungService.php");

class SAG_SeminarBuchen extends SAG_Component {

	var $entryTable = "seminarArt";
	var $entryClass = "SeminarArt";

	function construct() {
		list ($config, $content) = $this->createPageReader();

		$this->dTable = Doctrine::getTable($this->entryClass);
		$this->dsDb = new MosaikDatasource("dbtable");
		$this->pageReader->addDatasource($this->dsDb);
	}

	function POST() {
		$user = Mosaik_ObjectStore::getPath("/current/identity");

		$fb = $GLOBALS['firephp'];
		$fb->info("DBText::POST");
		if ($user->anonymous()) {
			/*  if($_SESSION[MosaikConfig::getEnv('sessionname').'_captcha'] == strtoupper(MosaikConfig::getEnv('captcha')) && MosaikConfig::getEnv('sessionname', false)) {
			 * 		$this->buchen($_POST);
			  }else {
			  list($config, $content) = $this->createPageReader();
			  $content->loadPage("seminar/captcha_error");
			  return $content->output->get();
			  } */
			// kein buchen mehr als anon
			instantRedirect("/kunde");
		} else {
			$this->buchenBestandskunde($_POST);
		}
		instantRedirect("/seminar/buchen/danke");
		return "";
	}

	function GET() {
		return "";
	}

	function map($name) {
		return "SAG_SeminarBuchen";
	}

	function danke() {
		list ($config, $content) = $this->createPageReader();

		$content->loadPage("seminar/buchen_danke.xml");
		$this->dataStore->add("text", $content->output->get());

		return $content->output->get();
	}

	function forward($class, $namespace = "") {
		
		$user = Mosaik_ObjectStore::getPath("/current/identity");
		qlog( "Buchungsform: user: {$user->id}");
		$name = $this->next();
		$fb = $GLOBALS['firephp'];

		if ($this->next() == "danke") {
			return $this->danke();
		}

		if (isset($_GET['buchen'])) {
			return $this->POST();
		}

		// big page reader

		list ($config, $content) = $this->createPageReader();
		$content->addDatasource($this->dsDb);

		$this->dataStore->add("image", "/img/title_$name.png");

		/*
		 * rubriken
		 */
		$name = str_replace("_u", "��", $name);
		$name = utf8_decode($name);

		$tab = Doctrine::getTable("ViewSeminarPreis");

		$result = $tab->mitHotels()->fetchArray(array($name));
		if (is_array($result)) {
			$add = $result[0];

			// dynamische Preis fuer Hotels

			if ( is_array( $result[0]['Standort']['Hotels'] ) ) foreach ($result[0]['Standort']['Hotels'] as $key => $hotel) {
				$hotelpreis = Doctrine::getTable("Hotel")->getPreis($hotel['id'], $result[0]['datum_begin'], "ViewHotelPreis")->fetchArray();
				$add['Standort']['Hotels'][$key]['Preis'] = $hotelpreis[0];
			}

			$add['name'] = $name;
			$add['dbtable'] = "seminar";

			$this->dsDb->set($add);
			
			$obj = $tab->find( $add['id'] );
			
			if ($obj->isAusgebucht()) {
				$content->loadPage("seminar/" . $this->name() . "_ausgebucht");
			} else if ($obj->isStoniert()) {
				if ($add['verlegt_seminar_id'] != 0) {
					$tab->findById($add['verlegt_seminar_id']);
					$content->loadPage("seminar/" . $this->name() . "_verlegt");
				}
			} else if ($user->anonymous()) {
				$content->loadPage("seminar/" . $this->name());
			} else {
				$personen = $user->getPersonen();
				if ($personen) {
					$this->dsDb->add("Personen", $personen->toArray());
				} else {
					$this->dsDb->add("Personen", array($user->getPerson()->toArray()));
				}
				$content->loadPage("seminar/" . $this->name() . "_bestandskunde");
			}
		} else {
			// kein seminar gefunden
			$content->loadPage("seminar/buchen_empty.xml");
		}
		return $content->output->get();
	}

	function buchenBestandskunde($data) {
		$user = $this->identity();
		$kontakt = $user->getKontakt();

		$post = new PostInfo();
		$post->data = serialize($_POST);
		$post->url = $this->url();
		$post->user_id = $kontakt->id;
		$post->save();
		$uIds = array();

		$tab = Doctrine::getTable("Seminar");
		$seminar = $tab->find($data['seminar']['id']);
		$buchungen = array();
		$ansprechpartnerEmail = $user->info->email;

		if ($seminar->isStoniert()) {
			return ""; // FIXME storno behandlung
		}

		$fb = $GLOBALS['firephp'];
		$uniqueId = md5(uniqid(rand(), true));

		// alle personen der buchung durchlaufen
		foreach ($data['person'] as $dPerson) {
			$person = null;

			// string id fuer die buchung erstellen
			$uniqueId = md5(uniqid(time(), true));
			array_push($uIds, $uniqueId);

			if (@ $dPerson['person_neu'] == "1") { // person soll neu angelegt werden
				$person = new Person();
				/*
				 * 		$person->name = $dPerson['name'];
				 * 		$person->vorname = $dPerson['vorname'];
				 * 		$person->geburtstag = mysqlDateFromLocal($dPerson['geburtstag']);
				 *
				 */
				$personData = mergeFilter("Person", $dPerson);
				$person->merge($personData);
				$person->id = null;
				$person->geburtstag = mysqlDateFromLocal($dPerson['geburtstag']);
				$person->kontakt_id = $kontakt->id;
				$person->save();
				$person->refresh();
			} else {
				$person = Doctrine::getTable("Person")->find($dPerson['id']);
				if (Doctrine::getTable("Buchung")->buchungExists($dPerson['id'], $seminar->id)) {
					continue;
				}
			}


			$info = array();
			try {
				$buchung = new Buchung();
				if ($user->isFromVdrk()) {
					$buchung->vdrk_referrer = 1;
				}
				$buchung->seminar_id = $seminar->id;
				$buchung->person_id = $person->id;

				// fixme rechtschreibung!!!!
				@$buchung->arbeitsagentur = $dPerson['arbeitsagentur'];
				@$buchung->zustaendige_arbeitsagentur = $dPerson['zustaendige_arbeitsagentur'];
				@$buchung->bildungscheck = $dPerson['bildungscheck'];
				@$buchung->bildungscheck_ausstellung_art = $dPerson['bildungscheck_ausstellung_art'];
				@$buchung->bildungscheck_ausstellung_ort = $dPerson['bildungscheck_ausstellung_ort'];
				@$buchung->bildungscheck_ausstellung_datum = $dPerson['bildungscheck_ausstellung_datum'];
				@$buchung->bemerkung = $data['bemerkung'];
				@$buchung->seminar_unterlagen = $dPerson['seminar_unterlagen'];
				@$info['seminar_unterlagen'] = $dPerson['seminar_unterlagen'];


				if (array_key_exists("bildungscheck_ausstellung_bundesland", $dPerson)) {
					$buchung->bildungscheck_ausstellung_bundesland = $dPerson['bildungscheck_ausstellung_bundesland'];
				}

				$buchung->uuid = $uniqueId;
				$buchung->datum = currentMysqlDatetime();
				$buchung->bemerkung = $data['bemerkung'];

				// preis kalkulieren
				$buchung->pinit();

				// speichern
				$buchung->save();
				$buchung->refresh();
			} catch (Exception $e) {
				qlog("Buchung - CREATE Exception");
				qdir($e);
				continue;
			}

			// HOTEL BUCHUNG
			try {
				if (@ array_key_exists('uebernachtungen', $dPerson) && @ $dPerson['hotelBuchen'] == 1 && @ $dPerson['uebernachtungen'] > 0) {
					$hotel = new HotelBuchung();
					$hotelData = mergeFilter("HotelBuchung", $dPerson);
					$hotel->merge($hotelData);
					$hotel->id = 0;
					$hotel->buchung_id = $buchung->id;

					$hotelpreis = Doctrine::getTable("Hotel")->getPreis($hotel['hotel_id'], $buchung->Seminar['datum_begin'], "ViewHotelPreis")->fetchArray();
					$hotel->pinit($hotelpreis[0]);
					$hotel->save();

					$hotel->refreshRelated();
					$info['HotelBuchung'] = $hotel->toArray(true);
					$info['HotelBuchung']['Hotel']['Preis'] = $hotelpreis[0];

					$info['HotelBuchung']['verkaufspreis_ez'] = $hotelpreis[0]['verkaufspreis_ez'];
					$info['HotelBuchung']['verkaufspreis_dz'] = $hotelpreis[0]['verkaufspreis_dz'];
				}
			} catch (Exception $e) {
				qlog("HotelBuchung - CREATE Exception");
				qdir($e);
			}
			// hotelbuchungen in den rechunngsbetrag mit einrechnen

			$buchung->refreshRelated();
			// EMAIL
			try {
				$info['Buchung'] = $buchung->toArray(true);
				$info = array_merge($info['Buchung'], $info);
				
				$buchung->Seminar->refreshRelated();
				$buchung->Seminar->Standort->refreshRelated();
				$buchung->Seminar->Standort->Ort->refreshRelated();

				$info['Seminar'] = $buchung->Seminar->toArray(true);

				$info['Person'] = $person->toArray(true);

				$buchungen[] = $info;
			} catch (Exception $e) {
				qlog("Info - CREATE Exception");
				qdir($e);
			}
		}

		try {
			// namen aufloesen
			$kontakt->Land->name;
			$kontakt->Bundesland->name;


			/* EMAIL NOTIFICATION */
			$event = new BuchungNewEvent();
			$event->sendMail = true;
			$event->multiple = true;
			$event->targetId = $uIds;

			BuchungService::getInstance()->dispatchEvent($event);
		} catch (Exception $e) {
			qlog("SendMail - Exception");
			qdir($e);
		}
	}

	function buchen($data) {
		$user = $this->identity();

		$post = new PostInfo();
		$post->data = serialize($_POST);
		$post->url = $this->url();
		$post->save();

		$tab = Doctrine::getTable("Seminar");
		$seminar = $tab->find($data['seminar']['id']);
		$buchungen = array();
		$ansprechpartnerEmail = "";

		if ($seminar->isStoniert()) {
			return ""; // FIXMKE storno behandlung
		}

		$fb = $GLOBALS['firephp'];
		$uniqueId = md5(uniqid(rand(), true));
		$kontakt = null;

		if ($data['art'] == "Privat") {
			$kontakt = Doctrine::getTable("Kontakt")->find(SAG_PRIVAT_KONTAKT);
		} else {
			$kontakt = new Kontakt();
			$kontakt->merge($data['kontakt']);
			if ($kontakt->newsletter == "1") {
				$kontakt->newsletter_anmeldedatum = currentMysqlDate();
			}
		}
		if (empty($kontakt->alias)) {
			$kontakt->alias = $kontakt->firma;
		}
		$kontakt->save();

		// strings nachladen
		$kontakt->Land->name;
		$kontakt->Bundesland->name;
		
		$ansprechpartner = $kontakt->getAnsprechpartner();
		if ( $ansprechpartner ) {
			$ansprechpartnerEmail = $ansprechpartner->email;
		} else {
			$ansprechpartnerEmail = SMTP_ADMIN_SENDER;
		}
		
		foreach ($data['person'] as $dPerson) {
			if (empty($dPerson['name']) && empty($dPerson['strasse']))
				continue;
			$info = array();

			$person = new Person();
			$person->Kontakt = $kontakt;

			//datenfilter
			if (array_key_exists("geburtstag", $dPerson)) {
				$dPerson['geburtstag'] = mysqlDateFromLocal($dPerson['geburtstag']);
			}

			$person->merge($dPerson);
			if ($person->newsletter == "1") {
				$person->newsletter_anmeldedatum = currentMysqlDate();
			}
			$person->save();
		//	if (array_key_exists("ansprechpartner", $dPerson) && $dPerson['ansprechpartner'] == "1") {
		//		$ansprechpartnerEmail = $dPerson['email'];
		//	}

			if ($dPerson['teilnehmer'] == "1") {
				$buchung = new Buchung();
				if ($user->isFromVdrk()) {
					$buchung->vdrk_referrer = 1;
				}
				$buchung->seminar_id = $seminar->id;
				$buchung->person_id = $person->id;

				// fixme rechtschreibung!!!!
				$buchung->arbeitsagentur = $dPerson['arbeitsagentur'];
				$buchung->zustaendige_arbeitsagentur = $dPerson['zustaendige_arbeitsagentur'];
				$buchung->bildungscheck = $dPerson['bildungscheck'];
				$buchung->bildungscheck_ausstellung_ort = $dPerson['bildungscheck_ausstellung_ort'];
				$buchung->bildungscheck_ausstellung_datum = $dPerson['bildungscheck_ausstellung_datum'];
				$buchung->bemerkung = $data['bemerkung'];
				$buchung->seminar_unterlagen = $dPerson['seminar_unterlagen'];
				if (array_key_exists("bildungscheck_ausstellung_bundesland", $dPerson)) {
					$buchung->bildungscheck_ausstellung_bundesland = $dPerson['bildungscheck_ausstellung_bundesland'];
				}
				$buchung->uuid = $uniqueId;

				$buchung->datum = currentMysqlDatetime();
				$buchung->bemerkung = $data['bemerkung'];

				// preis kalkulieren
				$buchung->pinit();

				// speichern
				$buchung->save();

				if (array_key_exists('uebernachtungen', $dPerson) && $dPerson['hotelBuchen'] == 1 && $dPerson['uebernachtungen'] > 0) {
					$hotel = new HotelBuchung();
					$hotel->merge($dPerson);
					$hotel->buchung_id = $buchung->id;


					$hotelpreis = Doctrine::getTable("Hotel")->getPreis($hotel['hotel_id'], $buchung->Seminar['datum_begin'], "ViewHotelPreis")->fetchArray();
					$hotel->pinit($hotelpreis[0]);
					$hotel->save();

					$hotel->refreshRelated();
					$info['HotelBuchung'] = $hotel->toArray(true);
					$info['HotelBuchung']['Hotel']['Preis'] = $hotelpreis[0];

					$info['HotelBuchung']['verkaufspreis_ez'] = $hotelpreis[0]['verkaufspreis_ez'];
					$info['HotelBuchung']['verkaufspreis_dz'] = $hotelpreis[0]['verkaufspreis_dz'];
				}

				// hotelbuchungen in den rechunngsbetrag mit einrechnen

				$buchung->refreshRelated();
				$info['Buchung'] = $buchung->toArray(true);

				$info = array_merge($info['Buchung'], $info);
				$buchung->Seminar->refreshRelated();
				$buchung->Seminar->Standort->refreshRelated();
				$buchung->Seminar->Standort->Ort->refreshRelated();

				$info['Seminar'] = $buchung->Seminar->toArray(true);
			}
			$info['Person'] = $person->toArray(true);
			$info['Person']['teilnehmer'] = $dPerson['teilnehmer'];

			$buchungen[] = $info;
		}


		/* EMAIL NOTIFICATION */
		$email = new MosaikEmail();
		$email->setContainer("__engine");
		$email->setPage("buchung");
		$email->addData("Buchungen", $buchungen);
		$email->addData("Kontakt", $kontakt->toArray(true));
		$email->addData('bemerkung', $data['bemerkung']);

		$email->addData("link", MosaikConfig::getVar("webUrl") . "kunde/buchung/$uniqueId");
		$email->addData("Uuid", $uniqueId);

		$datum_begin = mysqlDateToLocal($seminar->datum_begin);
		$email->send($ansprechpartnerEmail, SMTP_ADMIN_SENDER, $seminar->kursnr . " - " . $datum_begin . " - " . $kontakt->firma, SMTP_ADMIN_RECIVER);
	}

}

