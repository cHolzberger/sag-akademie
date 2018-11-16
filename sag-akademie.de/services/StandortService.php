<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
include("DatabaseService.php");

/**
 * Description of SeminarService
 *
 * @author Christian Holzberger <ch@mosaik-software.de>
 */
class StandortService extends DatabaseService {
    function findAll() {
	return Doctrine::getTable("Standort")->findByPlanung_aktiv(1)->toArray(true);
    }
    
}
