<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
include_once ( "lib/notifications/NotificationCheck.php" );

class GeburtstagsCheck extends NotificationCheck {

	var $view = "ViewPersonHeuteGeburtstag";
	var $_template = null;

	/**
	 * email template laden
	 */
	function loadTemplate() {

		MosaikDebug::msg("notification_geburtstag", "Loading Template:");

		$this->_template = new MosaikEmail();
		$this->_template->setContainer("__admin");
		$this->_template->setPage("notification_geburtstag");
		$this->_template->setSubject($this->_config['subject']);
	}

	/**
	 * mail senden
	 * @param Array $result
	 */
	function sendMail($result) {
		MosaikDebug::msg($result, "SendingMail:");
		$this->_template->addData("Person", $result->toArray(true));
		if ($this->_config['sendMail']) {
			$log = new EmailLog();
			$log->id = null;
			$log->data_id = $result->id;
			$log->email = $result->email;
			$log->gesendet = currentMysqlDate();
			$log->funktion = "Geburtstag";
			$log->save();
			$this->_template->send($result['email'], SMTP_SENDER, "", SMTP_ADMIN_RECIVER);
		}
	}

	// ********** OVERRIDES ***********

	/**
	 * Override for NotificationCheck
	 *
	 * @return Boolean Status
	 */
	function run() {
		parent::run();
		// alle personen die heute geburtstag haben finden
		$results = Doctrine::getTable("ViewPersonHeuteGeburtstag")->findAll();
		$this->setMax($results->count());



		for ($i = 0; $i < count($results); $i++) {

			// template laden
			$this->loadTemplate();

			$result = $results[$i];
			// ausgabe 1/5 2/5...
			$this->setCurrent($i + 1);

			// wenn mail vorhanden ist
			if (!empty($result['email'])) {
				$this->sendMail($result);
				$this->addOk($result['anredestr'] . " " . $result->getDN());
			} else {
				$this->addError($result->getDN());
			}
		}
		return TRUE;
	}

	function runTest() {
		parent::runTest();
		// person finden
		$personen = Doctrine::getTable("Person");
		$person = $personen->find("3123");
		// temolate laden
		$this->loadTemplate();
		//mail senden
		$this->sendMail($person);

		// fuer richtige ausgabe sorgen
		$this->setMax(1);
		$this->setCurrent(1);
		$this->addOk($person->getDN());


		return True;
	}

}

?>
