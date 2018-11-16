<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Wird aufgerufen wenn eine Buchung bearbeitet wurde
 *
 * @author molle
 */
include_once ("lib/Events/GenericEvent.php");

class SystemRequestEvent  extends GenericEvent{
    var $name = __CLASS__;
    // kann den wert begin und finish haben
    var $state;
}