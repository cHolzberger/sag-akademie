<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Kontakt extends BaseKontakt
{
	public function setUp()
    {
    	parent::setUp();
    	$this->actAs("KontaktTemplate");
		
		$this->actAs('Versionable', array(
		 'versionColumn' => 'version',
		 'className' => '%CLASS%Version',
		 'auditLog' => true
		 )
		);
		
			$this->actAs("ChangeCounted");
	}
	
	public function getBuchungen() {
		return Doctrine::getTable("ViewBuchungKontakt")->findByKontakt_id($this->id);
	}
	
	

	public function autocompleteTableProxy($firma) {
		$q = Doctrine_Query::create()
		->select ("k.firma, k.ort, k.plz")
		->from("Kontakt k")
		->where("k.kontext=? AND k.firma LIKE ? ", array( "Kunde", $firma . "%"));
		
		return $q;
	}
	
	function getAnsprechpartner() {
		$person = Doctrine::getTable("Person")->findByDql("kontakt_id = ? AND ansprechpartner = ?" , array($this->id, 1))->getFirst();
		if ( is_object($person)) {
			return $person;
		}
		return null;
	}
	
	function hasInhouse() {
		return $this->getInhouseSeminare()->count() > 0;
	}
	
	function getInhouseSeminare() {
		return Doctrine::getTable("Seminar")->findByDql("inhouse_kunde = ? AND inhouse = ? ", array($this->id,1));
	}
	
	function getInhouseSeminareAsArray() {
		return $this->getInhouseSeminare()->toArray();
	}
}
?>