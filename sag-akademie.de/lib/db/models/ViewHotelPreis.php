<?php

/**
 * ViewHotelPreis
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class ViewHotelPreis extends BaseViewHotelPreis
{
	  public function setUp() {
        parent::setUp();
        $this->hasOne('XUser as GeaendertVon', array (
            'local'=>'geaendert_von',
            'foreign'=>'id',
            )
        );
        $this->hasMany('HotelBuchung as Buchungen', array (
            'local'=>'id',
            'foreign'=>'hotel_id',
            )
        );

        $this->hasMany("HotelPreis as HotelPreise", array (
            'local' => "id",
            'foreign' => "hotel_id"
        ));

    }

}