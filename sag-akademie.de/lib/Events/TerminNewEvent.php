<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Wird bei neuanlegen einer Buchung aufgerufen
 *
 * @author molle
 */

include_once ("lib/Events/GenericEvent.php");

class TerminNewEvent  extends GenericEvent {
    var $name = __CLASS__;
    var $targetId;
    var $sendMail = false;
}