<?

class HotelBuchungListener extends Doctrine_Record_Listener {
	public function preInsert(Doctrine_Event $event) {
		// Initialisierung des Hotelpreises
		$preis = Doctrine::getTable("Hotel")->getStandardPreis($event->getInvoker()->hotel_id)->execute()->getFirst()->toArray();
		$event->getInvoker()->pinit($preis);	
	}

	public function preUpdate(Doctrine_Event $event) {
	}

}