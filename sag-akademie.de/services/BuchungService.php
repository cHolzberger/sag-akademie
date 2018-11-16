<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

include_once("services/DatabaseService.php");


/**
 * Der BuchungService verarbeitet alle Anfragen an Buchungen
 *
 * Rueckblicke:
 *  get BuchungenStartingFrom: gibt alle Buchungen seit $dataFrom zurueck
 * @author Christian Holzberger <ch@mosaik-software.de>
 */
class BuchungService extends DatabaseService {
	const BUCHUNGS_DATUM=1;
	const SEMINAR_DATUM=2;

	const STANDORT_DARMSTADT = 2;
	const STANDORT_LUENEN = 1;

	function __construct() {
		parent::__construct(__CLASS__);
	}

	/**
	 * Inistianlisiert dieses Singleton
	 * @return BuchungService
	 */
	static function &getInstance() {
		$data = GenericService::_getInstance(__CLASS__);
		return $data;
	}

	static function &dispatchEvent(&$eventInfo) {
		ob_start();
		$data = GenericService::_dispatchEvent(__CLASS__, $eventInfo);
		qlog(ob_get_contents());
		ob_end_clean();
		return $data;
	}

	static function &getEventQueue() {
		$data = GenericService::_getEventQueue(__CLASS__);
		return $data;
	}

	/**
	 * Ermittelt alle Buchungen nach Startdatum des Termins
	 *
	 * @param int $dateFrom Startdatum (Format: Y-m-d)
	 * @param int $range Reichweite (Format: 14, -2 ...)
	 */
	function getBuchungenByBuchungsdatumStartingFrom($dateFrom = null, $range = 14, $mode=BuchungService::BUCHUNGS_DATUM, $fetch=true) {
		// anfangsdatum
		// erst konvertierung zu timestamp
		$startTime = 0;
		$endTime = 0;
		$data = array();
		if ($dateFrom == null) {
			$data[0] = date("Y");
			$data[1] = date("m");
			$data[2] = date("d");
		} else {
			$data = explode("-", $dateFrom);
		}


		$y = $data[0];
		$m = $data[1];
		$d = $data[2];

		if ($range > 0) {
			$startTime = mktime(0, 0, 0, $m, $d, $y);
			$endTime = mktime(0, 0, 0, $m, $d + $range, $y);
		} else {
			$startTime = mktime(0, 0, 0, $m, $d + $range, $y);
			$endTime = mktime(0, 0, 0, $m, $d, $y);
		}

		$start = date("Y-m-d", $startTime);
		$end = date("Y-m-d", $endTime);

		// wir zeigen eine Menge Daten an
		$q = Doctrine_Query::create()
		  ->from('ViewBuchungPreis')
		  ->leftJoin('ViewBuchungPreis.Seminar')
		//  ->leftJoin('seminar.SeminarArt seminarart')
		 // ->leftJoin('buchung.Person person')
		  //->leftJoin('person.Kontakt kontakt')
		  //->leftJoin('buchung.HotelBuchung hotelbuchung')
		  //->leftJoin('hotelbuchung.Hotel hotel')
		  ->orderBy("datum")
		  ->where("deleted_at = ?",'0000-00-00 00:00:00');

		if ($mode == BuchungService::BUCHUNGS_DATUM) {
			$q->andWhere("DATE(datum) >= ? AND DATE(datum) <= ?", array($start, $end));
		} else if ($mode == BuchungService::SEMINAR_DATUM) {
			$q->andWhere("DATE(ViewBuchungPreis.Seminar.datum) >= ? AND DATE(ViewBuchungPreis.Seminar.datum) <= ?", array($start, $end));
		}

		$this->currentQuery = $q;
		if ( $fetch ) {
				
			return $this->_doFetch();
		}
	}

	

	function getBuchungenCountByTerminId($id) {
		return Doctrine::getTable("ViewBuchungPreis")->countbystatus($data['id']);
	}

}
