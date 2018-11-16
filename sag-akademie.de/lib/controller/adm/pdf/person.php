<?php
include_once ("generic/adminpdfdocument.php");

class ADM_PDF_Person extends Generic_AdminPDFDocument {
	public $template = "person";
	public $table = "Person";
	
	function map($name) {
		return "ADM_PDF_Person";
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
		$this->dsDb->add($this->table, $data->toArray(true));

		setHttpFilename($this->table."-". $data->id .".pdf");
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
