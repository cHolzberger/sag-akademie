<?php

class PersonListener extends Doctrine_Record_Listener {
	public function preInsert(Doctrine_Event $event) {
			global $identity;


		if (is_object($identity)) {
			$event->getInvoker()->angelegt_user_id = $identity->uid();
			$event->getInvoker()->angelegt_datum = currentMysqlDatetime();
			
		}
			
			if ($event->getInvoker()->newsletter == 1) {
				$event->getInvoker()->newsletter_anmeldedatum = currentMysqlDate();
			} else {
				$event->getInvoker()->newsletter_abmeldedatum = currentMysqlDate();

		}
	}

	public function preUpdate(Doctrine_Event $event) {
		global $identity;
		//$event->getInvoker()->updated = date('Y-m-d', time());
		$mod = $event->getInvoker()->getModified();

		if (array_key_exists("newsletter", $mod)) {
			if ($event->getInvoker()->newsletter == 1) {
				$event->getInvoker()->newsletter_anmeldedatum = currentMysqlDate();
			} else {
				$event->getInvoker()->newsletter_abmeldedatum = currentMysqlDate();
			}
		}
	}

}

class PersonTemplate extends Doctrine_Template {

	public function setUp() {
		parent::setUp();

		$this->hasOne('Kontakt', array('local' => 'kontakt_id', 'foreign' => 'id', "owningSide" => FALSE));
		$this->hasOne('XUser as GeaendertVon', array('local' => 'geaendert_von', 'foreign' => 'id', ));
		$this->hasMany('ViewBuchungPreis as Buchungen', array('local' => 'id', 'foreign' => 'person_id', "owningSide" => true));

		$this->hasOne('XUser as AngelegtVon', array('local' => 'angelegt_user_id', 'foreign' => 'id', ));

		//$this->addListener(new PersonListener());
		$this->actAs("ChangeCounted");
		$this->actAs("ChangeLogged");

		$this->addListener(new PersonListener());

	}

	function detailedTableProxy() {
		$q = Doctrine_Query::create();
		$q->from('Person Person')->leftJoin('Person.Kontakt Kontakt')->where("id=?");
		return $q;
	}

	function getDN() {
		$invoker = $this->getInvoker();

		return $invoker->name . ", " . $invoker->vorname . " (" . $invoker->email . ")";
	}

}
?>
