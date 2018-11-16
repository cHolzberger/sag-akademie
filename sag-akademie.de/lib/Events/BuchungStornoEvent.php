<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Wird aufgerufen wenn eine Buchung storniert wurde
 *
 * @author molle
 */
include_once ("lib/Events/GenericEvent.php");

class BuchungStornoEvent  extends GenericEvent {
    var $name =__CLASS__;
    var $personId;
    var $targetId;
    var $sendMail = false;
}
