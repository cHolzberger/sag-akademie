<?php

class SAG_Kunde_Kontakt extends SAG_Component {
	var $entryTable = "Kontakt";
	var $entryClass = "Kontakt";

	function __construct() {
	}
	
	function map($name) {
		return "SAG_Kunde_Person";
	}
	
	function POST() {
		return $this->onSave();
		
	}

	function onSave() {
		$this->identity()->authenticate();
		$user = $this->identity(); 

		$data = $_POST['Kontakt'];
		$kontakt = $user->getKontakt();
		
		if ( @ $data['newsletter'] != "1" ) $data['newsletter'] =0;
		
		if ( @ $data['vdrk_mitglied'] != "1" ) $data['vdrk_mitglied'] =0;

		if( $data['newsletter'] == "1" && $kontakt->newsletter == 0 ) {
			$data['newsletter_anmeldedatum'] = currentMysqlDate();
		} else if ( $data['newsletter'] != "1" && $kontakt->newsletter == 1) {
			$data['newsletter_abmeldedatum'] = currentMysqlDate();
		}
		
		
		$kontakt->merge($data);
		$kontakt->save();
		
		instantRedirect("/kunde/kontakt");
	}

	function renderHtml() {
		$this->identity()->authenticate();
		$user = $this->identity(); 

		list($config, $content) = $this->createPageReader();


		$this->dsDb = new MosaikDatasource ( );

		$this->dsDb->add("formaction", "/kunde/kontakt?save");		
		$this->dsDb->add("dbtable", $this->entryTable);
		$this->dsDb->add("dbclass", $this->entryClass);
		
		$content->addDatasource ( $this->dsDb );
		
		$kontakt = $user->getKontakt();
		$this->dsDb->add("Kontakt",$kontakt->toArray(true));
		$this->dsDb->log();
		
		$content->loadPage("kunde/kontakt/edit");
		return $content->output->get();	
	}
	
	function HEAD() {
		throw new k_http_Response(200);
	}
}
