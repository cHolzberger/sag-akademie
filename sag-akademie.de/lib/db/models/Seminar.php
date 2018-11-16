<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Seminar extends BaseSeminar {
	public function prepareMerge ( $data ) {
		$data["datum_begin"] = mysqlDateFromLocal( $data['datum_begin'] );
	}

	public function setUp () {
		parent::setUp();

		$this->actAs( "SeminarTemplate" );

	}

	/**
	 *    Gibt true zurueck wenn ein besimmter referent schon am $tag vorhanden ist
	 *
	 * @param Int $referent_id
	 * @param Int $tag
	 */
	public function hasReferent ( $referent_id, $tag, $standort_id ) {
		$q = Doctrine_Query::create();
		$q->from( "SeminarReferent sr" );
		$q->where( "sr.tag = ?", $tag );
		$q->andWhere( "sr.seminar_id = ?", $this->id );
		$q->andWhere( "sr.referent_id = ?", $referent_id );
		$q->andWhere( "sr.standort_id = ?", $standort_id );

		return $q->count();
	}

	/**
	 * fuegt diesem seminar den referenten mit der id $id hinzu
	 *
	 * @param Int $id
	 * @param Int $tag
	 * @param Int $standort_id
	 */
	public function addReferent ( $referent_id, $tag, $standort_id, $theorie = 0, $praxis = 0, $start_stunde = 0, $start_minute = 0, $ende_stunde = 0, $ende_minute = 0, $start_praxis_stunde = 0, $start_praxis_minute = 0, $ende_praxis_stunde = 0, $ende_praxis_minute = 0, $optional = 0 ) {
		// doubletten check:
		$res = Doctrine::getTable( "SeminarReferent" )->findByDql(
			"tag = ? AND " .
				"referent_id = ? AND " .
				"seminar_id = ? AND " .
				"standort_id = ?", array($tag, $referent_id, $this->id, $standort_id) );
		if ( $res->count() > 0 ) return;

		// neuen referenten anlegen:
		$ref               = new SeminarReferent();
		$ref->tag          = $tag;
		$ref->referent_id  = $referent_id;
		$ref->seminar_id   = $this->id;
		$ref->standort_id  = $standort_id;
		$ref->theorie      = $theorie;
		$ref->praxis       = $praxis;
		$ref->start_stunde = $start_stunde;
		$ref->start_minute = $start_minute;
		$ref->ende_stunde  = $ende_stunde;
		$ref->ende_minute  = $ende_minute;

		/** praxis **/
		$ref->start_praxis_stunde = $start_praxis_stunde;
		$ref->start_praxis_minute = $start_praxis_minute;
		$ref->ende_praxis_stunde  = $ende_praxis_stunde;
		$ref->ende_praxis_minute  = $ende_praxis_minute;

		$ref->optional = $optional;

		$ref->save();
	}


	public function preSave ( $obj ) {
		$this->kursgebuehr        = NumberFormat::toNumber( $this->kursgebuehr );
		$this->kosten_pruefung    = NumberFormat::toNumber( $this->kosten_pruefung );
		$this->kosten_verpflegung = NumberFormat::toNumber( $this->kosten_verpflegung );
		$this->kosten_unterlagen  = NumberFormat::toNumber( $this->kosten_unterlagen );
		$this->kosten_refer       = NumberFormat::toNumber( $this->kosten_refer );
		$this->stunden_pro_tag    = NumberFormat::toNumber( $this->stunden_pro_tag );
		$this->pause_pro_tag      = NumberFormat::toNumber( $this->pause_pro_tag );

	}

	/**
	 *
	 * Speert Seminare abhängig von Ihrer seminarArtId und Ihrem Standort
	 * Wenn $standort == Null ist werden alle Seminare der SeminarArt gesperrt.
	 */
	public function lockBySeminarArtIdTableProxy ( $seminarArtId ) {
		$q = Doctrine_Query::create();
		$q->update()->from( "Seminar" )->set( "aktualisierung_gesperrt", "?", 1 )->where( "seminar_art_id=?", $seminarArtid );
		$q->execute();
	}

	/**
	 * Sperrt Seminare abhängig vom Datum - alle vergangenen Termine werden gesperrt.
	 */

	public function lockOldTableProxy () {
		$q = Doctrine_Query::create();
		$q->update()->from( "Seminar" )->set( "aktualisierung_gesperrt", "?", 1 )->where( "datum_begin <= NOW()" );

		$q->execute();
	}

	public function syncToSeminarArt () {
		Doctrine::getTable( "Seminar" )->lockOld();

		if ( $this->aktualisierung_gesperrt == 1 ) return;
		if ( $this->SeminarArt->aktualisierung_gesperrt == 1 ) return;

		$this->kursgebuehr        = $this->SeminarArt->kursgebuehr;
		$this->kosten_pruefung    = $this->SeminarArt->kosten_pruefung;
		$this->kosten_verpflegung = $this->SeminarArt->kosten_verpflegung;
		$this->kosten_unterlagen  = $this->SeminarArt->kosten_unterlagen;
		$this->kosten_refer       = $this->SeminarArt->kosten_refer;
		$this->stunden_pro_tag    = $this->SeminarArt->stunden_pro_tag;
		$this->pause_pro_tag      = $this->SeminarArt->pause_pro_tag;
		$this->teilnehmer_min     = $this->SeminarArt->teilnehmer_min_tpl;
		$this->teilnehmer_max     = $this->SeminarArt->teilnehmer_max_tpl;

		$start = mysqlDateToSeconds( $this->datum_begin );

		$end              = $start + (($this->SeminarArt->dauer - 1) * 24 * 3600);
		$mend             = mysqlDateFromSeconds( $end );
		$this->datum_ende = $mend;
		$this->save();

		$this->createReferentenFromTemplate();
	}

	public function lock () {
		$this->aktualisierung_gesperrt = 1;
		$this->save();
	}

	public function unlock () {
		$this->aktualisierung_gesperrt = 0;
		$this->save();
	}
}

?>