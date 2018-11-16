<?

/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");
include_once("services/BuchungService.php");
include_once("Events/BuchungNewEvent.php");
include_once("Events/BuchungStornoEvent.php");
include_once("Events/BuchungUmgebuchtEvent.php");
include_once("Events/BuchungTeilnehmerChangeEvent.php");
include_once("Events/BuchungInfoEvent.php");

class Database_Buchung {

	var $_table = "Buchung";
	var $_view = "ViewBuchungPreis";

	function table() {
		return Doctrine::getTable($this->_table);
	}
	
	function view() {
		return Doctrine::getTable($this->_view);
	}

	function findObj($id, $table = null) {
		
		if ( $table == null ) {
			$q= $this->view()->detailed();
			$q->useResultCache(true, 3600, "rpc_buchung_" . $id);
			return $q->fetchOne(array($id));
		} else {
			return Doctrine::getTable($table)->find( array($id));
		}
	}

	function find($id) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": $id");
		$result = array();
		$obj = $this->findObj($id);
		$result = $obj->toArray();

		return $result;
	}

	/**
	 * transfer a booking from one seminar to another
	 * 
	 * @param string $buchungId
	 * @param string $terminId 
	 * @return string new buchung id
	 */
	function rebook($buchungId, $terminId, $hinweis) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": buchungId => {$buchungId}, terminId => {$terminId}, hinweis => {$hinweis} ");
		try {
			$neueBuchung = new Buchung();
			$alteBuchung = $this->findObj( $buchungId, "Buchung" );
			$data = mergeFilter("Buchung", $alteBuchung->toArray());
			$user = Identity::get();
			
			$neueBuchung->merge($data);
			$neueBuchung->id = 0;
			$neueBuchung->seminar_id = $terminId;
			$neueBuchung->uuid = $alteBuchung->uuid;
			$neueBuchung->angelegt_user_id = $user->getId();
			$neueBuchung->geaendert_von = $user->getId();
			$neueBuchung->geaendert = currentMysqlDatetime();

			$alteBuchung->rechnungsbetrag = 0;
			$alteBuchung->rechnunggestellt = "0000-00-00";
			$alteBuchung->zahlungseingang_betrag = 0;
			$alteBuchung->zahlungseingang_datum = "0000-00-00";
			$alteBuchung->umbuchungs_datum = currentMysqlDate();
			$alteBuchung->geaendert_von = $user->getId();
			$alteBuchung->geaendert = currentMysqlDatetime();

			//$neueBuchung->bestaetigt = 0;
			//$neueBuchung->bestaetigt_datum = "0000-00-00";
			$neueBuchung->info_kunde = nl2br($hinweis);
			$neueBuchung->alte_buchung = $alteBuchung->id;

			$neueBuchung->save();
			$neueBuchung->refresh();

			$alteBuchung->umgebucht_id = $neueBuchung->id;

			$alteBuchung->save();
			$alteBuchung->refresh();
		} catch (Exception $e) {
			qlog ("Exception:");
			qlog($e->getMessage());
		}
		
		

		/* !!! EVENT SENDEN ** */
		qlog ("Sending UmgeguchtEvent");
		try {
			$event = new BuchungUmgebuchtEvent();
			$event->newTargetId = $neueBuchung->uuid;
			$event->oldTargetId = $alteBuchung->id;
			$event->sendMail = true;
			BuchungService::dispatchEvent($event);
			//$this->sendMail($neueBuchung->uuid, $alteBuchung->toArray(true),  $neueBuchung->info_kunde);
		} catch ( Exception $e ) {
			qlog("Exception:");
			qlog($e->getMessage());
		}
		return $neueBuchung->id;
	}

	function save($id, $obj) {
		$result = array();

		$result = $this->table()->find($id);
		$mergeData = mergeFilter($this->_table, $obj);
		$originalData = $result->toArray();

		$result->merge($mergeData);
		$identity = Identity::get();

		
		$result->save();
		
		$evt = new BuchungTeilnehmerChangeEvent();
	

		$evt->buchungId = $result->id;
		$evt->teilnehmerFrom = $originalData['person_id'];
		$evt->teilnehmerTo = $result->person_id;
		
		BuchungService::dispatchEvent($evt);


		return $result->toArray();
	}

	function create($personId, $seminarId, $buchungsdatum, $hinweis, $hotelId, $anreisedatum, $uebernachtungen) {
		
		// is there a buchung for the same seminar?

		$termin = Doctrine::getTable("Seminar")->findById($seminarId)->getFirst();
		$person = Doctrine::getTable("Person")->findById($personId)->getFirst();
		qlog("createNew => person:" . $personId . " seminar: " . $seminarId . " hotelId: " . $hotelId);


		$res = Doctrine::getTable("Buchung")->findByDql("seminar_id = ? AND person_id = ?", array($seminarId, $personId));

		if ($res->count() != 0) {
			qlog("Buchung exists finding it...");
			return $this->find($res[0]->id);
		}

		$buchung = new Buchung();
		
		$identity = Identity::get();
		
		$buchung->seminar_id = $seminarId;
		$buchung->person_id = $personId;
		$buchung->uuid = md5(uniqid(rand(), true));

		
		// sync termin info to buchung
		$buchung->kursgebuehr = $termin->kursgebuehr;
		$buchung->kosten_unterlagen = $termin->kosten_unterlagen;
		$buchung->kosten_verpflegung = $termin->kosten_verpflegung;
		// sync person info to buchung
		$buchung->vdrk_mitglied = $person->Kontakt->vdrk_mitglied;
		$buchung->info_kunde = $hinweis;
        $buchung->datum = $buchungsdatum;

		$buchung->save();
		$buchung->refresh();
	
		//TODO use HotelBuchung controller?!
		if ( $hotelId ) {
			// TODO: preis nach datum holen
			qlog("create HotelBuchung");
			
			$hotelBuchung = new HotelBuchung();
			$hotelBuchung->buchung_id = $buchung->id;
			$hotelBuchung->hotel_id = $hotelId;
			$hotelBuchung->uebernachtungen = $uebernachtungen;
			$hotelBuchung->anreise_datum = $anreisedatum;
			$hotelBuchung->save();
		}	
		
		qlog("Send event");
		$event = new BuchungNewEvent();
		$event->sendMail = true;
		$event->multiple = false;
		$event->targetId = $buchung->uuid;
		$event->sourceClass = __CLASS__;
		$event->sourceFunction = __FUNCTION__;
		$event->sourceLine = __LINE__;
		
		BuchungService::getInstance()->dispatchEvent($event);

		try {
			//qdir($buchung->toArray());
			return $this->find($buchung->id);
		} catch ( Exception $e ) {
			qlog( __CLASS__ . ":" . __FUNCTION__ . " Exception: " . $e->getMessage() );
		}
	}

	function sendInfo($buchungId) {
		qlog("Sende Info E-Mail");

		// !!! EVENT SENDEN
		$event = new BuchungInfoEvent();
		$event->sendMail = true;
		$event->targetId = $buchungId;
		$event->setSourceInfo(__FILE__, __LINE__, __CLASS__, __FUNCTION__);
		

		BuchungService::getInstance()->dispatchEvent($event);
	}
	
	function storno($buchungId) {
		qlog("Storniere Buchung...");
		
		$buchung = $this->table()->find($buchungId);
		
		$buchung->storno_datum = currentMysqlDatetime();
		$buchung->save();
		
		$event = new BuchungStornoEvent();
		$event->sendMail = true;
		$event->targetId = $buchung->uuid;
		$event->personId = $buchung->person_id;
		
		BuchungService::getInstance()->dispatchEvent($event);
		
		return $buchung->id;
	}


}