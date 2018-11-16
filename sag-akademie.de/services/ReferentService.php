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
class ReferentService extends DatabaseService {

    function findAll() {
	$seminare = Doctrine::getTable("Referent")->findAll();
	return $seminare->toArray(true);
    }
}
