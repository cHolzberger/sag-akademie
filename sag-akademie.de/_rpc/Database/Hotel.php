<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Hotel {
	private $_table = "Hotel";
	
	function findObj($id) {
		return Doctrine::getTable($this->_table)->find($id);
	}

	function find($id) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": id => {$id}" );
		
		$result = array();
		$obj = $this->findObj($id);
		$result = $obj->toArray(true);

		return $result;
	}

	function save($id, $data) {
		$result = array();
		$user = Identity::get();
		// get old and new info to compare
		$newData = Doctrine::getTable("Hotel")->find($id);
		$oldData = (object) ($newData->toArray());
		
		// merge in new data
		$data['geaendert'] = currentMysqlDatetime();
		$data['geaendert_von'] = $user->getId();

		$newData->merge( mergeFilter("Hotel", $data) );
		
		// save new data
		$newData->save();
		Doctrine::getTable('XTableVersion')->increment("Hotel");

		//
		return (object)$newData->toArray();
	}

	function search($text, $options) {
		qlog("Hotel::Search:");
		qlog($options);
		
		$data = array();
		if ( array_key_exists ( "seminarId",$options)) {
			$standortId = $options['standortId'];
			
			$data = Doctrine::getTable("Hotel")->findByDql("Hotel.standort_id = ? AND aktiv = 1", $standortId)->toArray();
		}
		
		return $data;
	}
	
	function create($name, $strasse, $nr, $plz, $ort, $standort_id) {
		qlog (__CLASS__ . "::". __FUNCTION__);
		try {
			$_h = new Hotel();
			$user = Identity::get();
		
			$_h->name = $name;
			$_h->strasse = $strasse;
			$_h->nr = $nr;
			$_h->ort = $ort;
			$_h->plz = $plz;
			$_h->standort_id = $standort_id;
		
			$_h->geaendert = currentMysqlDatetime();
			$_h->geaendert_von = $user->getId();
			
			$_h->save();
			$_h->refresh();
			Doctrine::getTable('XTableVersion')->increment("Hotel");

			return $_h->toArray(true);
		} catch ( Exception $e) {
			qlog("Exception: " . $e->getMessage());
		}
		return null;
	}

	function getGrundpreis($hotelId) {
		$hotel = Doctrine::getTable("Hotel")->getStandardPreis($hotelId)->execute()->getFirst();
		if ( is_object ( $hotel) ) {
			return $hotel->toArray(true);
			
		}
		$ret = new HotelPreis();
		$ret->hotel_id = $hotelId;
		$ret->zimmerpreis_mb46 = 0;
		$ret->zimmerpreis_dz=0;
		
		$ret->zimmerpreis_ez=0;
		$ret->fruehstuecks_preis=0;
		$ret->marge=0;
		return $ret->toArray();
	}

	function getPreisliste($hotelId) {
		$hotelPreise = Doctrine::getTable("Hotel")->getPreise($hotelId);
		if ( $hotelPreise->count() == 0 ) {
			return array();
		} else {
			return $hotelPreise->execute()->toArray(true);
		}
	}

	function setPreisliste($hotelId,$data) {
		$ret = array();
		
		foreach ( $data as $row ) {
			$hotel = null;
			if ($row['id'] == 0) {
				$hotel = new HotelPreis();
				$hotel->hotel_id = $hotelId;
			} else {
				$hotel = Doctrine::getTable("HotelPreis")->find($row['id']);

				if ( $row['deleteRow'] ) {
					$hotel->delete();
				}
			}


			if ( ! $row['deleteRow'] ) {
				$hotel->merge(mergeFilter("HotelPreis", $row));
				$hotel->save();
				$ret[] = $hotel->toArray(true);
			}
		}
		return $ret;
	}

	function setGrundpreis($hotelId, $ez = 0 , $dz =0, $mz =0, $fruehstueck=0, $marge=0) {
		$hotel = Doctrine::getTable("Hotel")->getStandardPreis($hotelId);
		
		if ( $hotel->count() == 0 ) {
			$hotel = new HotelPreis();
			$hotel->hotel_id = $hotelId;
			$hotel->datum_start = "0000-00-00";
			$hotel->datum_ende = "0000-00-00";
		} else {
			$hotel = $hotel->execute()->getFirst();
		}

		$hotel->zimmerpreis_ez = $ez;
		$hotel->zimmerpreis_dz = $dz;
		$hotel->zimmerpreis_mb46 = $mz;
		$hotel->fruehstuecks_preis = $fruehstueck;
		$hotel->marge=  $marge;

		Doctrine::getTable('XTableVersion')->increment("Hotel");
		$hotel->save();
		$hotel->refresh();
		return $hotel->toArray(true);
	}

}