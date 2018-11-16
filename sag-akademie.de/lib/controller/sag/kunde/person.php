<?php

class SAG_Kunde_Person extends SAG_Component {
	var $entryTable = "Person";
	var $entryClass = "Person";

	function __construct() {
	}
	
	function map($name) {
		return "SAG_Kunde_Person";
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

		$data = $_POST['Person'];

		if ( @ $data['ausgeschieden'] != "1") {
			$data['ausgeschieden'] = 0;
		}
		
		if ( $this->entryId == "neu") {
			 $person = new Person();
			 $person->kontakt_id = $user->getKontakt()->id;
		} else {
			$person = $user->getPerson($this->entryId);
		}
		if ( @ $data['newsletter'] != "1" ) $data['newsletter'] =0;
		if ( @ $data['geschaeftsfuehrer'] != "1" ) $data['geschaeftsfuehrer'] =0;
		//if ( @ $data['ansprechpartner'] != "1" ) $data['ansprechpartner'] =0;
		
		if( $data['newsletter'] == "1" && $person->newsletter == 0 ) {
			$data['newsletter_anmeldedatum'] = currentMysqlDate();
		} else if ( $data['newsletter'] != "1" && $person->newsletter == 1) {
			$data['newsletter_abmeldedatum'] = currentMysqlDate();
		}
		
		$data['geburtstag'] = mysqlDateFromLocal( $data['geburtstag'] );

		
		$person->merge($data);
		$person->save();
		
		instantRedirect("/kunde/startseite");
	}
	
	function renderHtmlForward($class_name, $namespace = "") {
		$this->entryId = $this->next();
		$this->identity()->authenticate();
		$user = $this->identity(); 

		
		if ( isset ( $_POST['Person'] ) ) $this->onSave();

		list($config, $content) = $this->createPageReader();
		$this->dsDb = new MosaikDatasource ( );
		$this->dsDb->add("formaction", "/kunde/person/".$this->entryId."?save");		
		
		$this->dsDb->add("dbtable", $this->entryTable);
		$this->dsDb->add("dbclass", $this->entryClass);
		
		$content->addDatasource ( $this->dsDb );
		if ( $this->entryId == "neu") {
			$person = new Person();
			$person->kontakt_id = $user->getKontakt()->id;
		} else {
			$person = Doctrine::getTable("Person")->find($this->next());
		}
		$this->dsDb->add("Person",$person->toArray(true));
		$this->dsDb->add("Buchungen", $person->getBuchungen()->execute()->toArray(true));
		$this->dsDb->log();
		$content->loadPage("kunde/person/edit");
		return $content->output->get();
  	}
	
	function HEAD() {
		throw new k_http_Response(200);
	}
}
