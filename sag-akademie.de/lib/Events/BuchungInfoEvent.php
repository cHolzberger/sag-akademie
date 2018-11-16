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

class BuchungInfoEvent  extends GenericEvent{
    public $name = __CLASS__;
	public $targetId;
	public $targetField = "id";
	public $sendMail = false;
}
