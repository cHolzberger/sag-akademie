
<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Standort extends BaseStandort
{
 public function setUp() {
    	parent::setUp();
		$this->hasMany("Hotel as Hotels",array( 
				'local' => "id",
				"foreign" => "standort_id"
			)
		);
        $this->hasOne('Ort', array(
                'local' => 'plz',
                'foreign' => 'plz',
            )
        );
        $this->hasOne('XUser as GeaendertVon', array (
			'local'=>'geaendert_von',
			'foreign'=>'id',
		)
		);


		$this->actAs("ChangeCounted");

    }
    /**
     * Standorte fuer die Planung laden
     */
    public function getPlanungTableProxy() {
	$q = Doctrine_Query::create();

	$q->from( "Standort standort" )
	->select ("standort.name, standort.id, standort.planung_aktiv")
	->where ("standort.sichtbar_planung = 1");
	return $q;
    }

	

}
?>