<?php
include_once ("generic/pdfdocument.php");

class ADM_PDF_Termin extends Generic_AdminPDFDocument {
	public $template = "termin";
	public $table = "Seminar";
	
	function map($name) {
		return "ADM_PDF_Termin";
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
		$this->dsDb->add("Seminar", $data->toArray(true));
		
		setHttpFilename("Termin-".$data->id.".pdf");
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
