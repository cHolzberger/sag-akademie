<?
class ChangeCountedListener extends Doctrine_Record_Listener {


	private function _clearCache ($event) {
		$table = get_class($event->getInvoker());
		$id = $event->getInvoker()->id;
		$data = $event->getInvoker()->toArray();

		clearCache($table, $data, $id);	
	}
	
	public function preInsert(Doctrine_Event $event) {
		
	
		Doctrine::getTable('XTableVersion')->increment(get_class($event->getInvoker()->getTable()));
		Doctrine::getTable('XTableVersion')->increment(get_class($event->getInvoker()));
	}

	public function preUpdate(Doctrine_Event $event) {
		$this->_clearCache($event);

		

		Doctrine::getTable('XTableVersion')->increment(get_class($event->getInvoker()->getTable()));
		Doctrine::getTable('XTableVersion')->increment(get_class($event->getInvoker()));
	}

	public function preDelete(Doctrine_Event $event) {
		$this->_clearCache($event);

		Doctrine::getTable('XTableVersion')->increment(get_class($event->getInvoker()->getTable()));
		Doctrine::getTable('XTableVersion')->increment(get_class($event->getInvoker()));
	}

}

class ChangeCounted extends Doctrine_Template {

	public function setUp() {
		//parent::setUp();
		$this->addListener(new ChangeCountedListener());

	}

}
