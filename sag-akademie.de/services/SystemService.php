<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
include_once(dirname(dirname(__FILE__))  . "/lib/debug.php");
include_once("services/DatabaseService.php");
include_once("Events/SystemRequestEvent.php");

/**
 * Der BuchungService verarbeitet alle Anfragen an Buchungen
 *
 * Rueckblicke:
 *  get BuchungenStartingFrom: gibt alle Buchungen seit $dataFrom zurueck
 * @author Christian Holzberger <ch@mosaik-software.de>
 */
class SystemService extends DatabaseService {
    function __construct () {
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
     static function &dispatchEvent( &$eventInfo ) {
	 MosaikDebug::msg($eventInfo,"Dispatching");
	$data = GenericService::_dispatchEvent(__CLASS__, $eventInfo);
	return $data;
    }
     static function &getEventQueue() {
	$data = GenericService::_getEventQueue(__CLASS__);
	return $data;
    }

	static function onStart() {
		$event = new SystemRequestEvent();
		$event->state = "start";
		SystemService::dispatchEvent ( $event );
	}

	static function onFinish() {
		$event = new SystemRequestEvent();
		$event->state = "finish";
		SystemService::dispatchEvent ( $event );
	}
}
