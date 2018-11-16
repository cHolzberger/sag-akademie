<?php
/**
 */
class ViewBuchungKontaktTable extends Doctrine_Table
{

	public function setUp()
    {
    	parent::setUp();
			
		$this->hasMany('Buchung as Buchungen', array(
                'local' => 'id',
                'foreign' => 'uuid'
            )
        );
	}
}