<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Wird beim Umbuchen einer Buchung aufgerufen
 *
 * @author molle
 */

include_once ("Events/GenericEvent.php");

class BuchungUmgebuchtEvent  extends GenericEvent {
    var $name = __CLASS__;
    var $newTargetId;
    var $oldTargetId;
    var $sendMail = false;
}
