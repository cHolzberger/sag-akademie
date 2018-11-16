<?php
/****************************************************************************************
 * Use without written License forbidden                                                *
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>     *
 ****************************************************************************************/

/**
 * Dieser Handler kuemmert sich um alle Benachrichtigungen die nach aenderung oder neuanlegen einer buchung passieren muessen.
 * 
 *
 * @author molle
 */

include_once ("EventHandler/GenericEventHandler.php");
include_once ("Events/SystemRequestEvent.php");
include_once("config.php");
include_once("dbconnection.php");
// FIXME: nur aufrufen wenn auch daten in der planung geaendert wurden
// losgeloest vom main thread aufrufen
class SystemPlanungHandler  extends GenericEventHandler {
    var $event;
    var $view = "ViewSeminarOhneReferent";

    function handle ( &$event ) {
	$this->event = &$event;

	switch ( $event->name ) {
	    case "SystemRequestEvent":
		switch ( $event->state ) {
		    case "finish":
			//    $this->updateReferenten();
			    break;
		}
		break;
	    case "TerminNewEvent":
		$this->updateReferenten();
		break;

	     case "KeepAliveEvent":
		$this->updateReferenten();
		break;
	    case "SeminarUpdateEvent":
		$this->updateReferenten();
		break;
	}
    }

    function updateReferenten () {
    	return;
	MosaikDebug::msg("Referenten", "Update");
	$seminare = Doctrine::getTable($this->view)->findAll();

	for ( $i=0, $count = count($seminare) ; $i<$count; $i++ ) {
	    $seminar = $seminare[$i];
	    if ( $seminar->createReferentenFromTemplate() ) {
		//echo "Erstelle Referenten f&uuml;r: " . $seminar->kursnr  .  "<br/>";
	    } else {
		//echo "Keine Referenten f&uuml;r: " . $seminar->kursnr  .  "<br/>";
	    }
	}
    }
}
