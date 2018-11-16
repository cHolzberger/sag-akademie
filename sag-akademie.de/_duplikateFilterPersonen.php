<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

ini_set('include_path',
	ini_get('include_path')
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/PEAR"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/controller"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/templates"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/konstrukt"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/db/models"
);
include_once("Zend/Loader/Autoloader.php");
include ("lib/config.php");
include ("lib/std.inc.php");

$duplikate = Doctrine::getTable("ViewPersonDuplikate")->findAll();

foreach ( $duplikate as $dup) {
	$doubles = explode(";",$dup->duplikate);
	foreach ( $doubles as $oldId ) {
		if ( $oldId != $dup->id ) {
			print ("Merge {$oldId} in {$dup->id}\n");
			
			$q = Doctrine_Query::create()
			->update("Buchung")
			->set("person_id","?", $dup->id)
			->where("person_id = ?", $oldId)
			->execute();
			
			$q = Doctrine_Query::create()
			->delete("Person")
			->where("id = ?", $oldId)
			->execute();
		}
	} 
}
