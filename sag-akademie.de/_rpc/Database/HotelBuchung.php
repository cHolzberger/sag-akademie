<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");
include_once("services/BuchungService.php");
include_once("Events/BuchungEditEvent.php");
include_once("Events/HotelBuchungStornoEvent.php");

class Database_HotelBuchung {
	var $_table="HotelBuchung";
	var $_view="ViewHotelBuchungPreis";

	function table() {
		return Doctrine::getTable($this->_table);
	}
	
	function view() {
		return Doctrine::getTable($this->_view);
	}

	function findObj($id) {
		try {
			$q= $this->table()->detailedByBuchungId( $id );
			$q->useResultCache(true, 3600, "rpc_{$this->_table}_{$id}");
			return $q->execute()->getFirst();
		
		} catch ( Exception $e ) {
			
		}
		return null;
	}

	function find($id) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": $id");
		
		$obj = $this->findObj($id);
		if ( is_object ( $obj )) {
			$result = array();
			$result = $obj->toArray(true);
	
			return $result;
		}
		return null;
	}

	function storno($hotelBuchungId) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": hotelBuchungId => $hotelBuchungId");

		$user = Identity::get();
		
		$buchung = $this->table()->find($hotelBuchungId);
		$buchung->storno_datum = currentMysqlDate();
		$buchung->geaendert_von = $user->getId();
		$buchung->geaendert = currentMysqlDatetime();
		$buchung->save();

				// ** send changed event **/
		try {
			$event = new HotelBuchungStornoEvent();
			$event->sendMail = true;
			$event->targetId = $hotelBuchungId;
			$event->setSourceInfo(__FILE__, __LINE__, __CLASS__, __FUNCTION__);
			
			BuchungService::getInstance()->dispatchEvent($event);
		} catch ( Exception $e ) {
			qlog ( "Exception:");
			qlog ($e->getMessage());
			}
	}

	function save($buchungId, $obj) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": buchungId => $buchungId");
		qdir($obj);
		
		$user = Identity::get();

		
		$result = array();

		$result = $this->table()->findByBuchung_id($buchungId)->getFirst();
		$mergeData = mergeFilter ( $this->_table, $obj);

		$result->merge($mergeData);
		$result->geaendert_von = $user->getId();
		$result->geaendert = currentMysqlDatetime();
		$result->save();
	
		// ** send changed event **/
		try {
			$event = new BuchungEditEvent();
			$event->sendMail = true;
			$event->targetId = $buchungId;
			$event->setSourceInfo(__FILE__, __LINE__, __CLASS__, __FUNCTION__);
			
			BuchungService::getInstance()->dispatchEvent($event);
		} catch ( Exception $e ) {
			qlog ( "Exception:");
			qlog ($e->getMessage());
		}
	}

	function create ( $buchungId , $hotelInfo )  {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": buchungId => $buchungId");
		qdir($hotelInfo);

		$user = Identity::get();
		
		$buchung = Doctrine::getTable("Buchung")->find($buchungId);
		$hotel = Doctrine::getTable("Hotel")->find($hotelInfo['hotel_id']);
		
		$hb = new HotelBuchung();
		
		// create Hotelbuchung with defaults from Hotel
		$mergeData = mergeFilter( "HotelBuchung", $hotel->toArray());
		$mergeData = mergeFilter( "HotelBuchung", $hotelInfo);
		
		$hb->merge($mergeData);
					
		$hb->buchung_id = $buchungId;
		$hb->save();
		$hb->refresh();
		
		// ** send changed event **/
		try {
			$event = new BuchungEditEvent();
			$event->sendMail = true;
			$event->targetId = $buchungId;
			$event->setSourceInfo(__FILE__, __LINE__, __CLASS__, __FUNCTION__);
			
			BuchungService::getInstance()->dispatchEvent($event);
		 } catch ( Exception $e ) {
			qlog ( "Exception:");
			qlog ($e->getMessage());
		}
	}
}