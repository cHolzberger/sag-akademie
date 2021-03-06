<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SeminarArtRubrikTable extends Doctrine_Table
{
	function overview () {
	    $q = Doctrine_Query::create()
	    ->from("SeminarArtRubrik rubrik")
	    ->select ("rubrik.name, seminarArt.id")
	    ->leftJoin("rubrik.SeminarArten seminarArt");
	    return $q;
	}

	static function detailed() {
		$q = Doctrine_Query::create()
	    ->from('SeminarArtRubrik rubrik')
		->leftJoin("rubrik.SeminarArten a");
	   
		return $q;
	}
	
	static function findByName ( $name ) { 
		return Doctrine_Query::create()
    	->from('SeminarArtRubrik r')
    	->leftJoin('r.SeminarArten a')    	
	    ->where('r.name = ?', $name)
		->execute();
	}
}
?>