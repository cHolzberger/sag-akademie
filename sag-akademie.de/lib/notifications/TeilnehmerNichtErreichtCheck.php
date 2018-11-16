<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class TeilnehmerNichtErreichtCheck extends NotificationCheck {

	var $view = "ViewSeminarTeilnehmerNichtErreicht";

	function run() {
		parent::run();
		print ("Filterkriterum: <b>Seminar in " . $this->_config['ago'] . " Tagen</b><br/>");
		print ("Datengruppe: " . $this->_config['template'] . "<br/>");
		print ("Betreff: " . $this->_config['subject'] . "<br/>");

		//TODO Hier wieder checken, ob schon verschickt
		$seminare = Doctrine::getTable($this->view)->findBySql('delta=?', array($this->_config['ago']));


		foreach ($seminare as $seminar) {
			print ("Seminar: <b>" . $seminar->getDN() . "</b><br/>");
			print ("Datum: " . mysqlDateToLocal($seminar->datum_begin) . "<br/>");
			$this->setMax($seminar->Teilnehmer->count());

			for ($i = 0; $i < $seminar->Teilnehmer->count(); $i++) {
				$this->setCurrent($i + 1);
				$person = $seminar->Teilnehmer[$i];

				if ($this->sendMail($person->toArray(true), $seminar->toArray(true))) {
					$this->addOk($person->getDN());
				} else {
					$this->addError($person->getDN());
				}
			}
			print "<br/>";
		}
		return TRUE;
	}

	function runTest() {

	}

	function sendMail($person, $seminar) {
		$email = new MosaikEmail();
		$email->setContainer("__admin");
		$email->setPage($this->_config['template']);
		
		$email->addData("Person", $person);
		
		$email->addData("Seminar", $seminar);

		if (!empty($person->email)) {
			if ($this->_config['sendMail']) {
				$log = new EmailLog();
				$log->data_id = $person->id;
				$log->email = $person->email;
				$log->gesendet = currentMysqlDate();
				$log->funktion = "TeilnehmerNichtErreicht";
				$log->save();
				$email->send($person->email, SMTP_SENDER, "", SMTP_ADMIN_RECIVER);
			}
			return true;
		} return false;
	}

}

