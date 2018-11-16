<?php

/* * **************************************************************************************
 * Use without written License forbidden                                                *
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>     *
 * ************************************************************************************** */

/**
 * Dieser Handler kuemmert sich um alle Benachrichtigungen die nach aenderung oder neuanlegen einer buchung passieren muessen.
 * 
 *
 * @author molle
 */
include_once ("EventHandler/GenericEventHandler.php");

// FIXME: nur aufrufen wenn auch daten in der planung geaendert wurden
// losgeloest vom main thread aufrufen
class SystemUserHandler extends GenericEventHandler {

	var $event;

	function handle(&$event) {
		$this->event = &$event;

		switch ($event->name) {
			case "SystemRequestEvent":
				switch ($event->state) {
					case "start":
						$this->checkAuthTokens();
						break;
				}
				break;
		}
	}

	function checkAuthTokens() {
		qlog("==> Checking auth Tokens");
		$users = Doctrine::getTable("XUser")->findByauth_token("");
		foreach ($users as $user) {
			qlog("Setting new auth token: " . $user->name);
			$user->auth_token = md5(microtime());
			$user->save();
		}
	}

}

