<?php

/**
 * ViewHotelBuchungPreis
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class ViewHotelBuchungPreis extends BaseViewHotelBuchungPreis
{
public function setUp() {
		parent::setUp();

		$this->hasOne('Hotel', array (
		'local'=>'hotel_id',
		'foreign'=>'id',
		));

		$this->hasOne('Buchung', array (
		'local'=>'buchung_id',
		'foreign'=>'id',
		));
	}
	
	function pinit($price) {
			$this->zimmerpreis_ez = $price['zimmerpreis_ez']; 
			$this->zimmerpreis_dz = $price['zimmerpreis_dz'];
			$this->fruehstuecks_preis = $price['fruehstuecks_preis'];
			$this->marge = $price['marge'];
	}
}