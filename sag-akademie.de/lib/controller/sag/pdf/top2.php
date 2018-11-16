
<?php
include_once ("generic/pdfdocument.php");

class SAG_PDF_Top2 extends Generic_PDFDocument {
	public $template = "top_neu2";

	function map($name) {
		return "SAG_PDF_Top2";
	}
	
	function renderPdf() {
		return "Unkown";
	}
	
	function renderPdfForward($class_name, $namespace) {
        $id = $this->next();
        $id = str_replace("+", " ",$id);

		$seminar = Doctrine::getTable("ViewSeminarArtPreis")->findByDql("id=?", $id );

		setHttpFilename("Top-".$seminar->kursnr.".pdf");
		return $this->renderHtmlForward("");
	}
	
	function renderHtml() {
		return "Unkown";
	}
	
	function renderHtmlForward($class_name, $namespace="") {
        $id = $this->next();
        $id = str_replace("+", " ",$id);

		$this->initDatasource();

		$seminarArt = Doctrine::getTable("ViewSeminarArtPreis")->findByDql("id=?",array( $id ) )->getFirst();

		$this->dsDb->add("Seminar",$seminarArt->getFutureSeminare());
		$this->dsDb->add("RubrikName", $seminarArt->Rubrik->name);

		$this->dsDb->add("Farbe", str_replace("0x", "#",$seminarArt->farbe));
		$this->dsDb->add("Textfarbe", str_replace("0x", "#",$seminarArt->textfarbe));

		$this->dsDb->add("SeminarArt", $seminarArt->toArray());
		$this->dsDb->add("Heute", date("d.m.Y"));

		return $this->generateHtml($this->template);
	}

}
