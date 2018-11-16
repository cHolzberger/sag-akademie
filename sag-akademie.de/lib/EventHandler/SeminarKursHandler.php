<?php
/****************************************************************************************
 * Use without written License forbidden                                                *
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>     *
 ****************************************************************************************/

/**
 * Dieser Handler kuemmert sich um alle Benachrichtigungen die nach aenderung oder neuanlegen einer buchung passieren muessen.
 *
 *
 * @author CHolzberger
 */

include_once ("EventHandler/GenericEventHandler.php");

class BuchungNotificationHandler  extends GenericEventHandler {
    var $event;

    function handle ( &$event ) {
	if ( ! $event->sendMail ) return;

	$this->event = &$event;
	$uuid = $this->event->targetId;

	MosaikDebug::msg($event,"Catched event:");

	/*switch ( $event->name ) {
	    case "BuchungInfoEvent":
		$this->sendNewMail($uuid);
		break;
	    case "BuchungUmgebuchtEvent":
		$oldUuid = $this->event->oldTarget;
		$newUuid = $this->event->newTarget;
		$this->sendUmgebuchtMail($newUuid, $oldUuid);
		break;
	    case "BuchungEditEvent":
		$this->sendChangeMail($uuid);
		break;
	    case "BuchungStornoEvent":
		$this->sendStornoMail($uuid);
		break;
	    case "BuchungNewEvent":
		$this->sendNewMail($uuid);
		break;
	}*/
    }
}

