<?php
$GLOBALS['path'] = array();

class SAG_Kunde_Startseite extends SAG_Component {
	public $map = Array (
	);
	
	function __construct() {
	}
	
	function map($name) {
		if (array_key_exists($name, $this->map)) {
			return $this->map[$name];
		} else {
			throw new MosaikPageReader_PageNotFound("");
		} 
	}
	
	function POST() {
		return $this->renderHtml();
	}

	function renderHtml() {
		$this->identity()->authenticate("admin");
		
		$user = Mosaik_ObjectStore::getPath("/current/identity"); 
		
		$this->addBreadcrumb($this->url(), "Admin");
		
		list($config, $content) = $this->createPageReader();
		$this->dsDb = new MosaikDatasource ( "dbtable" );
		$content->addDatasource ( $this->dsDb );
		$this->dsDb->add ("Benutzer", $user->toArray(true) );
		$this->dsDb->add ("Ansprechpartner", $user->toArray(true) );
		

		$this->dsDb->add ("Person", $user->toArray(true) );
	
		$kontakt = $user->getKontakt();
		$hasInhouse = $kontakt->hasInhouse();
		$this->dsDb->add ("hasInhouse",  $hasInhouse);
		$inhouseSeminare = array();
		if ( $hasInhouse ) {
			$inhouseSeminare = $kontakt->getInhouseSeminareAsArray();
		}
		
		$this->dsDb->add ("InhouseTermine",  $inhouseSeminare);
		
		
		$this->dsDb->add("Bundesland", $kontakt->Bundesland->toArray() );
		$this->dsDb->add("Land", $kontakt->Land->toArray() );
		$this->dsDb->add("Kontakt", $user->getKontakt()->toArray(true));
		
		$personen = $user->getMitarbeiter();
		
		if ( $personen != NULL ) {
			$this->dsDb->add ("Personen", $personen);
		}	

		$buchungen = $user->getBuchungen()->toArray(true);

		for ( $i=0; $i < count ( $buchungen ); $i++) {
		    if ( $buchungen[$i]['umbuchungs_datum'] != "0000-00-00") {
			$buchungen[$i]['status_str'] = "Umgebucht";
		    } else if ( $buchungen[$i]['storno_datum'] != '0000-00-00') {
			$buchungen[$i]['status_str'] = "Storniert";
		    } else {
			$buchungen[$i]['status_str']= "";
		    }
		    MosaikDebug::msg($buchungen[$i], "Buchung:");
		}

		$this->dsDb->add ("Buchungen", $buchungen);
	
		if ( $user->getKontakt()->id == 1 ) {
			$content->loadPage("kunde/startseite_privatkunde");
		} else {
			$content->loadPage("kunde/startseite_firmenkunde");	
		}
		
		return $content->output->get();
	}
	
		
	function renderHtmlForward($class_name, $namespace = "") {
//		$this->identity()->authenticate("admin");
		
		list($config, $content) = $this->createPageReader();
	
		if (is_array($class_name)) {
      		$namespace = $class_name[1];
      		$class_name = $class_name[0];
    	}
		
		$next = $this->createComponent($class_name, $namespace);
		$content = "";

		
		return $next->dispatch();
		
  	}
	
	function HEAD() {
		throw new k_http_Response(200);
	}
}
