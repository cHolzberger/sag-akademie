<?php

/**
 * Kooperationspartner
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Kooperationspartner extends BaseKooperationspartner
{
	public function setUp() {
		parent::setUp();
		$this->actAs("ChangeCounted");
		
		$this->hasMany("SeminarArtZuKooperationspartner as Seminare", array( 
			"local" => "id",
			"foreign" => "kooperationspartner_id"
		));
	}
}