<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SeminarArtRubrik extends BaseSeminarArtRubrik
{
	public function setUp()
    {
    	parent::setUp();
 		$this->hasMany('SeminarArt as SeminarArten', array(
                'foreign' => 'rubrik',
		    'local' => 'id'
            )
        );


		$this->actAs("ChangeCounted");

    }
	
	public  function getSeminarArtenFromView() {
		$q = Doctrine_Query::create()
		->from ("ViewSeminarArtPreis seminar")
			->leftJoin("seminar.Rubrik r")
		->where("seminar.rubrik=? OR seminar.rubrik2=? OR seminar.rubrik3=? OR seminar.rubrik4 = ? OR seminar.rubrik5 = ?", array( $this->id, $this->id, $this->id, $this->id, $this->id))
		->andWhere("seminar.inhouse=?",0)
		->orderBy("seminar.bezeichnung");
		return $q;
	}
}
?>