<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Ort extends BaseOrt
{
 public function setUp()
    {
    	parent::setUp();

        $this->hasOne('standort', array(
                'local' => 'plz',
                'foreign' => 'plz',
            )
        );


		$this->actAs("ChangeCounted");

    }
}
?>