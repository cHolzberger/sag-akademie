
<?php
include_once ("generic/pdfdocument.php");

class SAG_PDF_Top extends Generic_PDFDocument {
	public $template = "top_neu";

	function map($name) {
		return "SAG_PDF_Top";
	}
	
	function renderPdf() {
		return "Unkown";
	}
	
	function renderPdfForward($class_name, $namespace) {
		$seminar = Doctrine::getTable("ViewSeminarArtPreis")->findByDql("id=?", $this->next() );

		setHttpFilename("Top-".$seminar->kursnr.".pdf");
		return $this->renderHtmlForward("");
	}
	
	function renderHtml() {
		return "Unkown";
	}
	
	function renderHtmlForward($class_name, $namespace="") {
		$this->initDatasource();

		$seminarArt = Doctrine::getTable("ViewSeminarArtPreis")->findByDql("id=?",array( $this->next()) )->getFirst();

		$this->dsDb->add("Seminar",$seminarArt->getFutureSeminare());
		$this->dsDb->add("RubrikName", $seminarArt->Rubrik->name);

		$this->dsDb->add("Farbe", str_replace("0x", "#",$seminarArt->farbe));
		$this->dsDb->add("Textfarbe", str_replace("0x", "#",$seminarArt->textfarbe));

		$this->dsDb->add("SeminarArt", $seminarArt->toArray());
		$this->dsDb->add("Heute", date("d.m.Y"));

		return $this->generateHtml($this->template);
	}

}
