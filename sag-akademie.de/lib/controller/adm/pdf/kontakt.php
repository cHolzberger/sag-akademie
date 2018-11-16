<?php
include_once ("generic/adminpdfdocument.php");

class ADM_PDF_Kontakt extends Generic_AdminPDFDocument {
	public $template = "kontakt";
	public $table = "Kontakt";
	
	function map($name) {
		return "ADM_PDF_Kontakt";
	}
	
	function renderPdf() {
		$this->initDatasource();
		return $this->generateHtml($this->template);
	}
	
	function renderPdfForward() {
		$this->initDatasource();
		$data = Doctrine::getTable($this->table)->detailed()
		->execute(array($this->next()))
		->getFirst();
		$this->dsDb->add("Kontakt", $data->toArray(true) );
		//qdir($data->toArray(true));
		
		setHttpFilename("Kunde-".$data->id.".pdf");
		return $this->generateHtml($this->template);
	}
	
	function renderHtml() {
		$this->initDatasource();
		return $this->generateHtml($this->template);
	}
	
	function renderHtmlForward() {
		return $this->renderPdfForward();
	}
	
}
