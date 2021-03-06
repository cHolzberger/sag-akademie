<?php

/**
 * Feiertag
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class Feiertag extends BaseFeiertag {
	public function setUp () {
		parent::setUp();
		/*$this->hasMany('XBundesland as Bundeslaender', array(
                'local' => 'feiertag_id',
                'foreign' => 'bundesland_id',
                'refClass' => 'RFeiertagBundesland'
            )
        );*/
		
		$this->actAs("ChangeCounted");
		
	}
	
	function getAllTableProxy() {
		$q = Doctrine_Query::create()
			->select("id, name, datum")
			->from('Feiertag')
			->orderBy("Feiertag.datum ASC");
			//->leftJoin("Feiertag.Bundeslaender bundesland");
		return $q;
	}

	
	
	function yearSelectTableProxy ( $year ) {
		$q = Doctrine_Query::create()
			->select("id, name, datum")
			->from('Feiertag')
			->where("YEAR(Feiertag.datum) = ? ", trim($year))
			->orderBy("Feiertag.datum ASC");
			//->leftJoin("Feiertag.Bundeslaender bundesland");
		return $q;
	}
}