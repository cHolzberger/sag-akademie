<?php

class SAG_Kunde_InhouseTermin extends SAG_Component {
	var $entryTable = "Seminar";
	var $entryClass = "Seminar";

	function __construct() {
	}
	
	function map($name) {
		return "SAG_Kunde_InhouseTermin";
	}
	
	function POST() {
		$this->onSave();
		return $this->renderHtml();
	}

	function renderHtml() {
		return "";
	}
	
	function onSave() {
		$this->identity()->authenticate();
		$user = $this->identity(); 
		$this->entryId = $this->next();
		
		if ( @isset($_POST['add_person'])) {
			$person_id = $_POST['person_id'];
			
			$b = new Buchung();
			$b->seminar_id = $this->entryId;
			$b->person_id = $person_id;
			
			$b->save();
			
		} else {
		$buchungen = $_POST['Buchung'];
		
		foreach ( $buchungen as $buchung) {
			if ( empty ($buchung['id']) && !empty($buchung['Person']['vorname'])) {
				// neue Buchung
				$b = new Buchung();
				$b->seminar_id = $this->entryId;
				
				if ( empty($buchung['person_id'])) {
					// neue Person
					$p = new Person();
					$buchung['Person']['geburtstag'] = mysqlDateFromLocal($buchung['Person']['geburtstag']);
					$p->kontakt_id = $user->getKontakt()->id;
					$p->merge($buchung['Person']);
					$p->nur_inhouse = 1;
					$p->save();
					$p->refresh();
					
					$b->person_id = $p->id;
				} else {
					$b->person_id = $buchung['person_id'];
					$p = Doctrine::getTable("Person")->find($b->person_id);
					$buchung['Person']['geburtstag'] = mysqlDateFromLocal($buchung['Person']['geburtstag']);
					$p->merge($buchung['Person']);
					$p->save();
				}
				// fixme (sync?)
				$b->save();	
			} else if ( !empty ($buchung['id'])) { // buchung existiert
				if ( !empty ($buchung['delete'])) {
					$b = Doctrine::getTable("Buchung")->find($buchung['id']);
					$b->delete();
				} else {
					$p = Doctrine::getTable("Person")->find($buchung['person_id']);
					$buchung['Person']['geburtstag'] = mysqlDateFromLocal($buchung['Person']['geburtstag']);
					$p->merge($buchung['Person']);
					$p->save();
				}
			}
		}
		}
		instantRedirect("/kunde/inhouse_termin/" . $this->entryId."?edit");
	}
	
	function renderHtmlForward($class_name, $namespace = "") {
		$this->entryId = $this->next();
		$this->identity()->authenticate();
		$user = $this->identity(); 

		$seminar = Doctrine::getTable("Seminar")->find($this->entryId)->toArray(true);
		$seminarArt = Doctrine::getTable("SeminarArt")->find($seminar['seminar_art_id'])->toArray(true);
		$inhouseOrt = Doctrine::getTable("InhouseOrt")->findByDql("seminar_id = ?", $seminar['id'])->getFirst()->toArray(true);
		if ( isset ( $_POST['Buchung'] ) || isset($_POST['add_person'])) $this->onSave();

		list($config, $content) = $this->createPageReader();
		$this->dsDb = new MosaikDatasource ( );
		$this->dsDb->add("formaction", "/kunde/inhouse_termin/".$this->entryId."?save");		
		
		$this->dsDb->add("dbtable", $this->entryTable);
		$this->dsDb->add("dbclass", $this->entryClass);
		
		$this->dsDb->add("Seminar", $seminar);
		$this->dsDb->add("SeminarArt", $seminarArt);
		$this->dsDb->add("InhouseOrt", $inhouseOrt);
		$personen = $user->getMitarbeiter();
		
		if ( $personen != NULL ) {
			$this->dsDb->add ("Personen", $personen);
		}	
		
		$q = Doctrine_Query::create()->from("Buchung buchung")->leftJoin("buchung.Person person")->where("seminar_id=? AND deleted_at = ?",array($this->entryId, "0000-00-00") );
		$buchungen = $q->execute();
		$teilnehmer = array();
		
		foreach ( $buchungen as $buchung ) {
			array_push($teilnehmer, $buchung->toArray(true));
		}
		for ( $i= $buchungen->count(); $i< 25; $i++) {
			array_push($teilnehmer,array(
			"id"=>"",
			"Person" => array(
				"name"=>"",
				"vorname" => ""
			)
			));
		}
		
		$this->dsDb->add("Teilnehmer", $teilnehmer);
		$content->addDatasource ( $this->dsDb );
		
		$content->loadPage("kunde/inhouse_termin");
		return $content->output->get();
  	}
	
	function HEAD() {
		throw new k_http_Response(200);
	}
}
