<?

/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");
include_once("lib/Events/TerminNewEvent.php");
include_once("lib/Events/TerminAbgesagtEvent.php");
include_once("services/TerminService.php");

class Database_Termin {
	function findObj($id, $view = "ViewSeminarPreis") {
		$q =  Doctrine::getTable("Seminar")->detailed($view);
		$q->useResultCache(true, 3600, "rpc_{$view}_{$id}");

		return $q->execute($id)->getFirst();
	}
	
	/**
	 *
	 * @param String $id
	 * @return array ob objects
	 */
	function findAlternatives ( $id ) {
		qlog(__CLASS__ . "::" . __METHOD__ . ": id => " . $id );
		
		$obj = $this->findObj($id, "Seminar");	
		qlog("SeminarArtId: {$obj->seminar_art_id} Date: ". currentMysqlDate());
		$q = Doctrine_Query::create()->from("ViewSeminarPreis")
		->where ( "seminar_art_id = ?" , $obj->seminar_art_id )
		->andWhere("freigabe_status = 2")
		->andWhere("datum_ende >= ?", currentMysqlDate() )
		->orderBy("datum_begin ASC");
		
		$data = $q->fetchArray();
		
		qdir ( $data );
		return $data;
	}

	function find($id, $view = "ViewSeminarPreis") {
		qlog(__CLASS__ . "::" . __METHOD__ . ": id => " . $id . "view => " . $view);

		$result = array();
		$obj = $this->findObj($id, $view);
		$result = $obj->toArray(true);

		$cB = Doctrine::getTable("ViewBuchungPreis")->countbystatus($id, 1);
		$cN = Doctrine::getTable("ViewBuchungPreis")->countbystatus($id, 0);
		$cS = Doctrine::getTable("ViewBuchungPreis")->countStorno($id);
		$cU = Doctrine::getTable("ViewBuchungPreis")->countUmbuchung($id);
		$nT = Doctrine::getTable("ViewBuchungPreis")->countNichtTeilgenommen($id);
		$result["anzahlBestaetigt"] = $cB;
		$result["anzahlNichtTeilgenommen"] = $nT;
		$result["anzahlStorniert"] = $cS;
		$result["anzahlUmgebucht"] = $cU;
		$result["jahr"] = substr($obj->datum_begin, 0, 4);
		// array_merge is dangerous
		$ret = $result;

		if ($obj->inhouse == "1") {
			//FIXME: ggf. noch auf Fehldende Ortangabe hinweisen
			
			$standort = Doctrine::getTable("InhouseOrt")->findBySeminar_id($obj->id)->getFirst();
			
			if ( is_object($standort) ) {
				$ret = array_merge($result, $standort->toArray());
			}
		}
		//qdir($ret);
		return $ret;
	}

	function save($id, $data) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": id => " . $id);
		$view = "ViewSeminarPreis";
		$newData = null;
		try {
			$result = array();

			// get old and new info to compare
			$newData = Doctrine::getTable("Seminar")->find($id);
			$oldData = (object) ( $newData->toArray() );
			//qdir($oldData);

			// merge in new data

			$newData->merge(mergeFilter("Seminar", $data));

			// save new data
			$newData->save();


			if ($newData->inhouse == "1") { // save inhouse data
				$view = "ViewInhouseSeminar";
				
			
				$iSt = Doctrine::getTable("InhouseOrt")->findBySeminar_id($newData->id)->getFirst();
				if ( !is_object($iSt)) {
					$iSt = new InhouseOrt();
					$iSt->seminar_id = $newData->id; 
				}
				$iSt->merge( mergeFilter("InhouseOrt", $data) );
				$iSt->save();
			} else {
				$view = "ViewSeminarPreis";
			}

			
			// send a new event to handle creation
			if ($id == "new") {
				// !!! EVENT SENDEN
				$event = new TerminNewEvent();
				$event->targetId = $result->id;

				TerminService::getInstance()->dispatchEvent($event);
			}
		} catch (Exception $e) {
			qlog("Exception:");
			qlog($e->getMessage());
		}

		Doctrine::getTable('XTableVersion')->increment("Seminar");

		return $this->find($newData->id, $view);
	}

	/**
	 * creates new termin using SeminarArt with id $seminarArtId as template.
	 * assignes Standort $standort
	 * and sets begin to $datumBegin
	 * this methods auto calculates the $datumEnde variable based on the dauer field of seminarArt
	 *
	 * optionally one can pass in $inhouse then the inhouse flag of this termin is set
	 * if you set $inhouse to true you must pass $inhouseKundeId
	 *
	 * @param string $seminarArtId
	 * @param string $standortId
	 * @param string $datumBegin
	 * @param bool $inhouse
	 * @parram string $inhouseKundeId
	 * @return object
	 */
	function create($seminarArtId, $standortId, $datumBegin, $gesperrt,$inhouse = false, $extraData = null) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": SeminarArtId:" . $seminarArtId . " StandortId:" . $standortId . " DatumBegin:" . $datumBegin);
		if ($inhouse) {
			qlog("Inhouse-Seminar");
			qdir($extraData);
		}

		$user = Identity::get();

		$seminarArt = Doctrine::getTable("SeminarArt")->find($seminarArtId);
		if ( !is_object ($seminarArt) ) {
			qlog("Error: SeminarArt: $seminarArtId dosn't exist!");
			throw new Exception("SeminarArt: $seminarArtId dosn't exist.");
		}
		$seminar = new Seminar();

		// daten aus vorlage kopieren
		/* alt:
		$seminar->stunden_pro_tag = $seminarArt->stunden_pro_tag;
		$seminar->pause_pro_tag = $seminarArt->pause_pro_tag;
		
		$seminar->kursgebuehr = $seminarArt->kursgebuehr;
		$seminar->kosten_allg = $seminarArt->kosten_allg;
		$seminar->kosten_refer = $seminarArt->kosten_refer;
		$seminar->kosten_unterlagen = $seminarArt->kosten_unterlagen;
		$seminar->kosten_verpflegung = $seminarArt->kosten_verpflegung;
		$seminar->kosten_pruefung = $seminarArt->kosten_pruefung;
		 * neu:
		 */
		$seminar->merge(mergeFilter("Seminar", $seminarArt->toArray()));
		$seminar->teilnehmer_min = $seminarArt->teilnehmer_min_tpl;
		$seminar->teilnehmer_max = $seminarArt->teilnehmer_max_tpl;
		$seminar->seminar_art_id = $seminarArtId;
		$seminar->id=0;
		if ( $gesperrt=="on" || $gesperrt=="1" || $gesperrt=="true") {
			$seminar->aktualisierung_gesperrt = 1;
		}
	
		$seminar->kursnr = "#PL#-" . $seminarArt->id;
		// is this an inhouse termin?

		// inhouse ort speichern
		

		$seminar->datum_begin = $datumBegin;
		$start = mysqlDateToSeconds($datumBegin);
		qlog("Start: " . $start);
		$end = $start + ( ($seminarArt->dauer-1) * 24 * 3600 );
		$mend =  mysqlDateFromSeconds($end);
		qlog("end: " . $end);
		qlog("mend:" . $mend);
		$seminar->datum_ende = $mend;
		

		$view = "";
		if ($inhouse) {
			$seminar->inhouse = 1;
			$seminar->standort_id = $standortId;
			$seminar->inhouse_ausgerichtet_von = $standortId;
			$seminar->inhouse_kunde = $extraData['inhouseKundeId'];

			if ( empty ($extraData['inhouse_strasse']) && empty($extraData['inhouse_ort']) && empty ($extraData['inhouse_plz']) ) {
				$kunde = Doctrine::getTable("Kontakt")->find($extraData['inhouseKundeId']);
				$extraData['inhouse_strasse'] = $kunde->strasse;
				$extraData['inhouse_ort'] = $kunde->ort;
				$extraData['inhouse_plz'] = $kunde->plz;
			}

			// save inhouse standort
			$seminar->InhouseOrt->merge(mergeFilter("InhouseOrt", $extraData));
		} else {
			$seminar->standort_id = $standortId;
		}

		$seminar->save();
		$seminar->refresh();

		$event = new TerminNewEvent();
		$event->targetId = $seminar->id;

		TerminService::getInstance()->dispatchEvent($event);

		return $this->find($seminar->id, $inhouse? "ViewInhouseSeminar" : "ViewSeminarPreis");
	}

	function search($searchstring, $options) {
		$q = Doctrine::getTable("Seminar")->simpleSearch($searchstring);
		return $q->fetchArray();
	}

	function remove( $seminarId ) {
		$seminar = Doctrine::getTable("Seminar")->find($seminarId);
			
		
		$buchungen = Doctrine::getTable("Buchung")->findByDql("seminar_id = ?", array($seminar->id));
		
		foreach ($buchungen as $buchung) {
			$buchung->delete();
		}
		
		$seminar->delete();
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
	
	function sync($terminId) {
		Doctrine::getTable("Seminar")->find($terminId)->syncToSeminarArt();
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
	
	function syncFutur($terminId) {
		$termin = Doctrine::getTable("Seminar")->find($terminId);
		$rows = Doctrine::getTable("Seminar")->findByDql("seminar_art_id = ? AND datum_begin > ?", array( $termin->seminar_art_id, $termin->datum_begin));
		foreach ($rows as $row) {
			$row->syncToSeminarArt();
		}		
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
	
	function syncFuturStandort($terminId) {
		$termin = Doctrine::getTable("Seminar")->find($terminId);
		$rows = Doctrine::getTable("Seminar")->findByDql("seminar_art_id = ? AND datum_begin > ? AND standort_id=?", array( $termin->seminar_art_id, $termin->datum_begin,$termin->standort_id));
		foreach ($rows as $row) {
			$row->syncToSeminarArt();
		}		
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
	
	function lock($terminId) {
		Doctrine::getTable("Seminar")->find($terminId)->lock();
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
	
	function unlock($terminId) {
		Doctrine::getTable("Seminar")->find($terminId)->unlock();
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
	
	function lockFutur($terminId) {
		$termin = Doctrine::getTable("Seminar")->find($terminId);
		$rows = Doctrine::getTable("Seminar")->findByDql("seminar_art_id = ? AND datum_begin > ?", array( $termin->seminar_art_id, $termin->datum_begin));
		foreach ($rows as $row) {
			$row->lock();
		}		
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
	
	function lockFuturStandort($terminId) {
		$termin = Doctrine::getTable("Seminar")->find($terminId);
		$rows = Doctrine::getTable("Seminar")->findByDql("seminar_art_id = ? AND datum_begin > ? AND standort_id=?", array( $termin->seminar_art_id, $termin->datum_begin,$termin->standort_id));
		foreach ($rows as $row) {
			$row->lock();
		}		
		Doctrine::getTable('XTableVersion')->increment("Seminar");
	}
}