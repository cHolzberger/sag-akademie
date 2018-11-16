
<?php
include_once ("generic/pdfdocument.php");

class SAG_PDF_RubrikTop extends Generic_PDFDocument {
	public $template = "rubriktop";

	function map($name) {
		return "SAG_PDF_RubrikTop";
	}
	
	function renderPdf() {
		return "Unkown";
	}
	
	function renderPdfForward($class_name, $namespace) {
		setHttpFilename("Top-".$this->next().".pdf");
		return $this->renderHtmlForward("");
	}
	
	function renderHtml() {
		return "Unkown";
	}
	
	function renderHtmlForward($class_name, $namespace="") {
		$this->initDatasource();

		$rubrikName = $this->next();

		$rubrikName = str_replace ("_u","ü",$rubrikName);
		$rubrikName = str_replace ("_a","ä",$rubrikName);
		$rubrikName = str_replace ("_o","ö",$rubrikName);
		$rubrikName = str_replace ("%20"," ",$rubrikName);

		$rubrik = Doctrine::getTable("SeminarArtRubrik")->findByDql("name=?", array($rubrikName))->getFirst();
		$this->dsDb->add("Rubrik", $rubrik->toArray());

		$seminarArten = Doctrine::getTable("ViewSeminarArtPreis")->findByDql("rubrik = ? AND status = ? ORDER BY id ASC",array( $rubrik->id ,1) );
		foreach ($seminarArten as $seminarArt) {
			$arr[$seminarArt->id]["SeminarArt"] = $seminarArt->toArray();
			$arr[$seminarArt->id]["RubrikName"] = $seminarArt->Rubrik->name;
			$arr[$seminarArt->id]['Seminar'] = $seminarArt->getFutureSeminare();
		}

		$this->dsDb->add("SeminarArten", $arr);

		return $this->generateHtml($this->template);
	}

}
