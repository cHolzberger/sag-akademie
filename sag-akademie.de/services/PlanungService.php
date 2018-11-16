<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

include_once("services/DatabaseService.php");

/**
 * Der PlanungService verarbeitet alle Anfragen an und änderungen durch die Planungskomponente
 *
 * @author Christian Holzberger <ch@mosaik-software.de>
 */
class PlanungService extends DatabaseService {
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
	$data = GenericService::_dispatchEvent(__CLASS__, $eventInfo);
	return $data;
    }
     static function &getEventQueue() {
	$data = GenericService::_getEventQueue(__CLASS__);
	return $data;
    }

    function findAll() {
	$seminare = Doctrine::getTable("SeminarArt")->findBySichtbar_planung(1);
	return $seminare->toArray(true);
    }

}