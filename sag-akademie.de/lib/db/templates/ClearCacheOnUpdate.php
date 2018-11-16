<?php
include_once ("lib/Events/TerminAbgesagtEvent.php");
include_once ("services/TerminService.php");

class ClearCacheOnUpdate extends Doctrine_Record_Listener {
	private function _cleanup ($invoker) {
		$cache = DBPool::$cacheDriver;

		if ( is_array($invoker->ccou )) {
			foreach($invoker->ccou as $i) {
				$cache_key = str_replace("%ID%", $invoker->id, $i);
				$cache->delete($cache_key);
				qlog("Cleaning cache key: $cache_key");
			}
		}
	}

	public function preDelete(Doctrine_Event $event) {
		$this->_cleanup($event->getInvoker());
	}

	public function preInsert(Doctrine_Event $event) {
		$this->_cleanup($event->getInvoker());

	}

	public function preUpdate(Doctrine_Event $event) {
		$this->_cleanup($event->getInvoker());
	}
}
