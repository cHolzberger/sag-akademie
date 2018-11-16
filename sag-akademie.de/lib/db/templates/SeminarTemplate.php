<?php
include_once ("lib/Events/TerminAbgesagtEvent.php");
include_once ("services/TerminService.php");

class SeminarListener extends Doctrine_Record_Listener {
	public function preInsert(Doctrine_Event $event) {
		global $identity;
		//$event->getInvoker()->created = date('Y-m-d', time());
		//$event->getInvoker()->updated = date('Y-m-d', time());
		//$event->getInvoker()->angelegt_user_id = Mosaik_ObjectStore::init()->get("/current/identity")->uid();


			if (!is_object($identity)) {
				$event->getInvoker()->geaendert_von = -1;
			} else {
				$event->getInvoker()->geaendert_von = $identity->uid();
			}
			$target = $event->getInvoker();
			$template = Doctrine::getTable("SeminarArt")->find($event->getInvoker()->seminar_art_id);

			if (!$target->kursgebuehr)
				$target->kursgebuehr = $template->kursgebuehr;

			if (!$target->kosten_pruefung)
				$target->kosten_pruefung = $template->kosten_pruefung;
			if (!$target->kosten_verpflegung)
				$target->kosten_verpflegung = $template->kosten_verpflegung;
			if (!$target->kosten_unterlagen)
				$target->kosten_unterlagen = $template->kosten_unterlagen;
			if (!$target->kosten_refer)
				$target->kosten_refer = $template->kosten_refer;
			if (!$target->stunden_pro_tag)
				$target->stunden_pro_tag = $template->stunden_pro_tag;
			if (!$target->pause_pro_tag)
				$target->pause_pro_tag = $template->pause_pro_tag;
			if (!$target->teilnehmer_min)
				$target->teilnehmer_min = $template->teilnehmer_min_tpl;
			if (!$target->teilnehmer_max)
				$target->teilnehmer_max = $template->teilnehmer_max_tpl;

			$event->getInvoker()->geaendert = currentMysqlDatetime();

	}

	public function postInsert(Doctrine_Event $event) {
	    $this->updateReferenten();
	}

	function updateReferenten () {
	MosaikDebug::msg("Referenten", "Update");
	$seminare = Doctrine::getTable("ViewSeminarOhneReferent")->findAll();

	for ( $i=0, $count = count($seminare) ; $i<$count; $i++ ) {
	    $seminar = $seminare[$i];
	    if ( $seminar->createReferentenFromTemplate() ) {
		//echo "Erstelle Referenten f&uuml;r: " . $seminar->kursnr  .  "<br/>";
	    } else {
		//echo "Keine Referenten f&uuml;r: " . $seminar->kursnr  .  "<br/>";
	    }
	}
	}


	public function preUpdate(Doctrine_Event $event) {
		global $identity;
		//$event->getInvoker()->updated = date('Y-m-d', time());
		$cache = DBPool::$cacheDriver;
		$cache->delete("kalender_seminar_" . $event->getInvoker()->id);
		
		$mod = $event->getInvoker()->getModified();

		if (array_key_exists("freigabe_status", $mod)) {
			qlog("Freigabe Update");
			$event->getInvoker()->freigabe_datum = currentMysqlDate();
		}

		// check if we have a storno
		if (array_key_exists("storno_datum", $mod)) {
			$event->getInvoker()->freigabe_status = STATUS_STORNO;
			$event->getInvoker()->freigabe_datum = currentMysqlDate();

			// seminar wurde gerade storniert von SAG
			$evt = new TerminAbgesagtEvent();
			$evt->sendMail = true;
			$evt->seminarId = $event->getInvoker()->id;
			TerminService::getInstance()->dispatchEvent($evt);
		}

		// neues handling:
		if (array_key_exists("freigabe_status", $mod)) {
			if ($mod['freigabe_status'] == STATUS_STORNO) {
				$event->getInvoker()->storno_datum = currentMysqlDatetime();
			} 
			$event->getInvoker()->freigabe_datum = currentMysqlDate();

		}

		if (!is_object($identity)) {
			$event->getInvoker()->geaendert_von = -1;
		} else {
			$event->getInvoker()->geaendert_von = $identity->uid();
		}
		$event->getInvoker()->geaendert = currentMysqlDatetime();
	}
	
	public function postUpdate(Doctrine_Event $event) {
	    $this->updateReferenten();
	}
}

class SeminarTemplate extends Doctrine_Template {

	// per result
	function isStoniert() {
		$invoker = $this->getInvoker();
		return ($invoker->storno_datum != "0000-00-00");
	}

	function isAusgebucht() {
		$invoker = $this->getInvoker();
		return $invoker->freigabe_status == STATUS_AUSGEBUCHT;
	}

	function setUp() {
		$this->hasOne('Standort', array('local' => 'standort_id', 'foreign' => 'id'));

		$this->hasOne('SeminarFreigabestatus', array('local' => 'freigabe_status', 'foreign' => 'id'));
		$this->hasOne('XUser as GeaendertVon', array('local' => 'geaendert_von', 'foreign' => 'id', ));

		$this->hasOne('SeminarArt', array('local' => 'seminar_art_id', 'foreign' => 'id'));

		$this->hasOne('InhouseOrt', array("local" => "id", "foreign" => "seminar_id"));

		$this->hasMany('Buchung as RBuchungen', array('local' => 'id', 'foreign' => 'seminar_id', ));

		$this->hasMany('ViewBuchungPreis as Buchungen', array('local' => 'id', 'foreign' => 'seminar_id', ));

		$this->hasMany("SeminarReferent as Referenten", array("foreign" => "seminar_id", "local" => "id"));

		$this->hasMany("ViewSeminarTeilnehmer as Teilnehmer", array("foreign" => "seminar_id", "local" => "id"));

		/* $this->hasOne('Ort as Ort', array (
		 'foreign'=>'id',
		 'local'=>'standort_id'
		 )); */
		$this->addListener(new SeminarListener());
		$this->actAs("ChangeCounted");

	}

	// per table
	public function detailedTableProxy($view = "ViewSeminarPreis") {
		$q = Doctrine_Query::create()->from($view . ' a')->leftJoin('a.GeaendertVon as updater')->leftJoin('a.Standort b')->leftJoin('b.Ort e')->leftJoin('a.SeminarArt c')->leftJoin('a.Buchungen d')->leftJoin("d.HotelBuchung f")->leftJoin("f.Hotel g")->leftJoin("d.Person h")->leftJoin("h.Kontakt i")->leftJoin("a.SeminarFreigabestatus status")->where('a.id = ?');
		return $q;
	}

	public function detailedListTableProxy() {
		$q = Doctrine_Query::create()->from('ViewSeminarPreis  seminar')->leftJoin('seminar.SeminarArt seminarArt')->leftJoin('seminar.Standort b')->leftJoin('seminar.Buchungen d')->leftJoin("d.Person f")->leftJoin("f.Kontakt g")
		//->leftJoin('b.Ort e')
		->leftJoin('seminar.SeminarArt c');

		return $q;
	}

	public function detailedInTableProxy($ids) {
		$q = Doctrine_Query::create()->from('ViewSeminarPreis  seminar')->leftJoin('seminar.Standort b')->leftJoin('seminar.Buchungen d')->leftJoin("d.Person f")->leftJoin("f.Kontakt g")
		//->leftJoin('b.Ort e')
		->leftJoin('seminar.SeminarArt c')->whereIn("seminar.id", $ids);

		return $q;
	}

	public function mitHotelsTableProxy() {
		$invoker = $this->getInvoker();

		$q = Doctrine_Query::create()->from('ViewSeminarPreis  a')->leftJoin('a.SeminarArt c')->leftJoin('a.Standort b')->leftJoin('b.Ort e')->leftJoin('b.Hotels g')->where("a.id = ?")->andWhere("g.aktiv = 1 OR g.name IS NULL");
		return $q;
	}

	public function searchTableProxy($string) {
		$string = strtolower(trim(utf8_decode($string)));
		$string = utf8_encode("%$string%");

		$q = Doctrine_Query::create()->select('k.id')->from("ViewSeminarPreis  k")->where("k.kursnr LIKE ?", $string);

		return $q->fetchArray();
	}

	public function autocompleteTableProxy($string) {
		$string = strtolower(trim($string));

		$q = Doctrine_Query::create()
		->select('k.id, k.kursnr, k.datum_begin, k.datum_ende, standort.name as standort, k.kosten_unterlagen, k.kursgebuehr, k.kosten_verpflegung, k.standort_id')
		->from("Seminar  k")
		->leftJoin('k.Standort standort')
		->leftJoin('k.SeminarFreigabestatus status')
		->where("k.kursnr LIKE ? AND (status.flag = 'F' OR status.flag = 'A') ", "%" . $string . "%")
		->orderBy("k.datum_begin ASC");

		return $q;
	}

	public function simpleSearchTableProxy($string) {
		$now = currentMysqlDate();
		$dateMode = TRUE;
		
		if (substr($string,0,1) == "!") {
			$dateMode=FALSE;
			$string = substr($string, 1,strlen($string)-1);		
		}

		$q = $this->autocompleteTableProxy($string);
		
		if ( $dateMode ) {
			$q->andWhere("k.datum_ende >= ?", array($now));
		}
		return $q;
	}

	public function tableSearchTableProxy($q) {
		$string = strtolower(trim($q));
		$string = utf8_encode("%$string%");

		$q = Doctrine_Query::create()->select('*')->from('ViewSeminarPreis  seminar')->leftJoin('seminar.Standort b')->leftJoin('seminar.Buchungen d')->leftJoin("d.Person f")->leftJoin("f.Kontakt g")->leftJoin('seminar.SeminarArt c')->where("seminar.kursnr LIKE ?", $string);

		return $q;
	}

	public function detailedSearchTableProxy($q) {
		$string = strtolower(trim($q));
		$string = utf8_encode("%$string%");

		$q = Doctrine_Query::create()->select('*')->from('ViewSeminarPreis  seminar')->leftJoin('seminar.Standort b')->leftJoin('seminar.Buchungen d')->leftJoin("d.Person f")->leftJoin("f.Kontakt g")->leftJoin('seminar.SeminarArt c')->where("seminar.kursnr LIKE ?", $string);

		return $q->fetchArray();
	}

	public function teilnehmer() {
		$q = Doctrine_Query::create()->select('*')->from("ViewSeminarTeilnehmer seminar")->where("seminar.seminar_id = ?", $this->getInvoker()->id);

		return $q;
	}

	public function getTeilnehmer() {
		$q = Doctrine_Query::create()->select('*')->from("ViewSeminarTeilnehmer seminar")->where("seminar.seminar_id = ?", $this->getInvoker()->id)->andWhere("seminar.teilgenommen = 0");
		return $q;
	}

	/**
	 * nur fuer die Jahresplanung
	 */
	public function yearSelectTableProxy($year) {
		$q = new Doctrine_RawSql();

		$q->from("view_seminar_planung a");

		$q->select("{a.seminar_art_id}, {a.id}, {a.datum_begin},
				    {a.datum_ende}, {a.standort_id}, {a.storno_datum},
				    {a.kursnr}, {a.freigabe_status}, {a.freigabe_veroeffentlichen},
				    {a.farbe}, {a.textfarbe}, {a.freigabe_farbe},
				    {a.freigabe_flag}, {a.freigabe_name}, {a.dauer},
				    {a.abgeschlossen}, {a.dauer}, {a.teilnehmer}, {a.inhouse}, {a.inhouse_firma}, {a.inhouse_ort}, {a.inhouse_plz},
				     {a.aktualisierung_gesperrt}, {a.standort_gesperrt}, {a.seminar_art_gesperrt}");

		$q->where("a.datum_begin >= ?", array(trim($year) . "-01-01"));
		$q->andWhere("a.datum_begin <= ?", array(trim($year) . "-12-31"));
		$q->orderBy("a.datum_begin");
		$q->addComponent('a', 'ViewSeminarPlanung');
		return $q;
	}

	/**
	 * nur fuer die jahresplanung
	 * @param <type> $year
	 * @param <type> $month
	 * @return <type>
	 */
	public function yearMonthSelectTableProxy($year, $month) {
		$q = new Doctrine_RawSql();
		$q->from("view_seminar_preis a");
		$q->where("a.datum_begin >= ?", trim($year) . "-" . $month . "-01");
		$q->andWhere("a.datum_begin <= ?", trim($year) . "-" . $month . "-31");
		$q->orderBy("a.datum_begin");
		$q->addComponent("a", "ViewSeminarPreis");
		return $q;
	}

    /* setzt alle referenten fuer diesesn termin zurueck */
    public function clearReferenten($standort_id) {
        $invoker = $this->getInvoker();

        foreach ($invoker->Referenten as $referent) {
            if ($referent->standort_id == $standort_id) {
                $referent->delete();
            }
        }
    }
	/**
	 * erstellt die Refrenten fuer dieses Seminar aus den Vorlagen
	 */
	public function createReferentenFromTemplate() {
		$invoker = $this->getInvoker();
        //referenten löschen
        $invoker->clearReferenten($invoker->standort_id);

		$referentenVorlage = Doctrine::getTable("SeminarArtReferent")->getReferentenForSeminarId($invoker->seminar_art_id, $invoker->standort_id);

		if (count($referentenVorlage) < 1)
			return FALSE;

		foreach ($referentenVorlage as $vorlage) {
			if (!is_array($vorlage)) {
				echo "keine vorlage!";
				continue;
			}

			$referent = new SeminarReferent();
			$referent->referent_id = $vorlage['referent_id'];
			$referent->standort_id = $vorlage['standort_id'];
			$referent->tag = $vorlage['tag'];
			$referent->praxis = $vorlage['praxis'];
			$referent->theorie = $vorlage['theorie'];
			$referent->anwesenheit = $vorlage['anwesenheit'];
			$referent->optional = $vorlage['optional'];
			$referent->start_stunde = $vorlage['start_stunde'];
			$referent->start_minute = $vorlage['start_minute'];
			$referent->ende_stunde = $vorlage['ende_stunde'];
			$referent->ende_minute = $vorlage['ende_minute'];

			/** praxis **/
			$referent->start_praxis_stunde = $vorlage['start_praxis_stunde'];
			$referent->start_praxis_minute = $vorlage['start_praxis_minute'];
			$referent->ende_praxis_stunde = $vorlage['ende_praxis_stunde'];
			$referent->ende_praxis_minute = $vorlage['ende_praxis_minute'];

			$referent->id = 0;
			$referent->seminar_id = $invoker->id;
			//print_r($referent);
			$referent->save();
		}

		return TRUE;
	}

	/**
	 * Liefer die naechsten count Termine fuer den Standort standort_id
	 * @param int $standort_id
	 * @param int $count
	 * @return Doctrine_Query
	 */
	function getNextTableProxy($standort_id, $count, $status=null) {
		if ( $status == null ) {
			return Doctrine_Query::create()->from("ViewSeminarPreis seminar")
			->where("seminar.standort_id = ?", $standort_id)
			->andWhere("seminar.datum_ende > NOW()")
			->orderBy("seminar.datum_begin ASC")->limit($count);
		} else if (is_array($status)) {
			$q= Doctrine_Query::create()->from("ViewSeminarPreis seminar")
			->where("seminar.standort_id = ?", $standort_id)
			->andWhere("seminar.datum_ende > NOW()");
			$qstr = "1=0 ";
			foreach ( $status as $stat) {
				$qstr = " OR seminar.status = ?";
			}
			$q->andWhere($qstr,$status);
			$q->orderBy("seminar.datum_begin ASC")->limit($count);
			return $q;
		} else {
			return Doctrine_Query::create()->from("ViewSeminarPreis seminar")
			->where("seminar.standort_id = ?", $standort_id)
			->andWhere("seminar.datum_ende > NOW()")
			->andWhere("seminar.status = ?",array($status))
			->orderBy("seminar.datum_begin ASC")->limit($count);
		}
	}

}
