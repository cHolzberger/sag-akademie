<?php
include_once ("generic/adminpdfdocument.php");

class ADM_PDF_Tischbeschilderung extends Generic_AdminPDFDocument {
	function map($name) {
		return "ADM_PDF_Tischbeschilderung";
	}
	
	function renderPdf() {
		$this->initDatasource();
		return $this->generateHtml("tischbeschilderung");
	}
	
	function renderPdfForward() {
		
		setHttpFilename("Tischbeschilderung-".$seminar->kursnr.".pdf");
		
		return $this->renderHtmlForward();
	}
	
	function renderHtml() {
		$this->initDatasource();
		return $this->generateHtml("teilnehmerliste");
	}
	
	function renderHtmlForward() {
		$this->initDatasource();
		$seminar = Doctrine::getTable("Seminar")->find( $this->next() );
		$teilnehmer = $seminar->teilnehmer()->fetchArray();
		// quickfix
		foreach ( $teilnehmer as $key=>$tn) {
			$teilnehmer[$key]['Seminar'] = $seminar->toArray();
		}
		$this->dsDb->add("Seminar", $seminar->toArray());
		$this->dsDb->add("Teilnehmer", $teilnehmer);
		return $this->generateHtml("tischbeschilderung");
	}

}
