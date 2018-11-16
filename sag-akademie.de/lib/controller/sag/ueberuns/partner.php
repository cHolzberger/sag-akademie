<?php
class SAG_Ueberuns_Partner extends SAG_Component {
    var $entryTable = "kooperationspartner";
    var $entryClass = "Kooperationspartner";
    function construct() {
		list($config, $content) = $this->createPageReader();

		global $ormMap;

		$this->dTable = Doctrine::getTable($this->entryClass);
		$this->dsDb = new MosaikDatasource ("dbtable");
    }
	
	function renderHtml() {
		list($config, $content) = $this->createPageReader();
		//FIXME: set Partner in dsDb
		$q = Doctrine_Query::create()->from("Kooperationspartner");
		$kop = $q->fetchArray();
		
		$this->pageReader->output->addReplacement("page_background" , "/img/header_bg_gross.jpg");
		$this->pageReader->addDatasource($this->dsDb);
		$this->dsDb->add("Partner", $kop);
				
		$content->loadPage( "ueberuns/partner" );
		return $content->output->get();
		
	}
	
    
}
?>
