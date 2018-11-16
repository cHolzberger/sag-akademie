<?
class CreationLoggedListener extends Doctrine_Record_Listener {
	public function preInsert(Doctrine_Event $event) {
		global $identity;

		//$event->getInvoker()->created = date('Y-m-d', time());
		//$event->getInvoker()->updated = date('Y-m-d', time());

		if (is_object($identity) && $identity->isValid()) {
			$event->getInvoker()->angelegt_user_id = $identity->uid();

		
		}
		$event->getInvoker()->angelegt_datum = currentMysqlDatetime();

	}
}

class CreationLogged extends Doctrine_Template {

	public function setUp() {
		//parent::setUp();
		$this->addListener(new CreationLoggedListener());

	}

}
