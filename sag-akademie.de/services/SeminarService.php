<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
include("DatabaseService.php");
/**
 * Description of SeminarService
 * Verwaltet die Tabelle Seminar -> Termine fuer Seminare
 *
 * @author Christian Holzberger <ch@mosaik-software.de>
 */
class SeminarService extends DatabaseService {
    
    function findAll() {
	$seminare = Doctrine::getTable("SeminarArt")->findBySichtbar_planung(1);
	return $seminare->toArray(true);
    }

    /**
     * erstellt fuer alle Seminare die Referenten aus der Vorlage
     */
    function createReferentenFromTemplate() {
	// SIEHE NOTIFICATIONS Seminar ohne Referent
    }
}
