<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class HotelTable extends Doctrine_Table {
	function detailed($id = false) {
		$q = Doctrine_Query::create()
		->from('Hotel hotel')
                ->leftJoin('hotel.GeaendertVon user')
		->leftJoin("hotel.Buchungen buchungen")
		->leftJoin("buchungen.Buchung terminBuchung")
		->leftJoin("terminBuchung.Person person");
		if($id != false) {
		    MosaikDebug::msg("detailed($id) is deprecated","Hotel");
		    $q->where('hotel.id = ?', $id);
		}else{
		    $q->where('hotel.id = ?');
		}
		//->andWhere('buchungen.storno_datum = ?', '0000-00-00');

		return $q;
	}
	
	function remoteList() {
		$q = Doctrine_Query::create()
		->from('Hotel hotel')
		->leftJoin("hotel.Standort standort");
		return $q;
	}

	
	
	// es darf nur einen eintrag mit datum_start = 0000-00-00 und datum_ende = 0000-00-00 geben!
	function getStandardPreis($hotel_id, $table="HotelPreis") {
		$q = Doctrine_Query::create()
		->from($table.' preis')
		->where("preis.hotel_id = ?", $hotel_id)
		->andWhere('preis.datum_start = 0000-00-00')
		->andWhere('preis.datum_ende = 0000-00-00')
		;

		return $q;
	}
	
	function getPreis ( $hotel_id, $sqlDate, $table="HotelPreis") {
		$q = Doctrine_Query::create()
		->from($table . ' preis')
		->orderBy("datum_start DESC")
		->where("preis.hotel_id = ?", $hotel_id)
		->andWhere('preis.datum_start <= ?', $sqlDate)
		->andWhere('preis.datum_ende >= ?', $sqlDate)
		;
		
		if ( $q->count() == 0 ) {
			return $this->getStandardPreis($hotel_id, $table);
		} else { 
			return $q;
		}
		

	}
	
	function getPreise ( $hotel_id) {
		$q = Doctrine_Query::create()
		->from('HotelPreis preis')
		->orderBy("datum_start DESC")
		->where("preis.hotel_id = ?", $hotel_id)
		->andWhere('preis.datum_start <> 0000-00-00')
		->andWhere('preis.datum_ende <> 0000-00-00')
		;

		return $q;
	}
	
	function detailSearch ( $name ) {
		$q = Doctrine_Query::create()
		->from("Hotel h")
		->select ("h.name, h.id")
		->where ("h.name = ?", $name);
		
		return $q;
	}
}
?>