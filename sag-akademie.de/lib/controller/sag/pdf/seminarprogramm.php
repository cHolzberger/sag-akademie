
<?php
include_once ("generic/pdfdocument.php");

class SAG_PDF_Seminarprogramm extends Generic_PDFDocument {
	public $template = "seminarprogramm";

	function map($name) {
		return "SAG_PDF_Seminarprogramm";
	}
	

	function renderPdf() {
		setHttpFilename("Seminarprogramm.pdf");
		return $this->renderHtml();
	}

	function renderPdfForward($class_name, $namespace) {
		setHttpFilename("Seminarprogramm.pdf");
		return $this->renderHtmlForward("");
	}

	function renderHtml() {
		$this->initDatasource();


		$rubriken = Doctrine::getTable("SeminarArtRubrik")->findAll();


		foreach ( $rubriken as $rubrik) {
			$data = array();
			$data['Rubrik'] = $rubrik->toArray();

			$arr = array();

			$seminarArten = Doctrine::getTable("ViewSeminarArtPreis")
			->findByDql("rubrik = ? AND status = ? AND inhouse = ? ORDER BY id ASC ",array( $rubrik->id ,1,0) );

			foreach ($seminarArten as $seminarArt) {
				$arr[$seminarArt->id] = $seminarArt->toArray();

				$arr[$seminarArt->id]['Seminare'] = $seminarArt->getFutureSeminare();
			}

			$data['SeminarArten'] = $arr;
			$this->dsDb->add($rubrik->name, $data);
			//$this->dsDb->add("Farbe", str_replace("0x", "#",$seminarArt->farbe));
			//$this->dsDb->add("Textfarbe", str_replace("0x", "#",$seminarArt->textfarbe));

			//$this->dsDb->add("SeminarArt", $seminarArt->toArray());
		}


		$this->dsDb->add("Heute", date("d.m.Y"));
		return $this->generateHtml($this->template);
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

		$arr = array();

		$seminarArten = Doctrine::getTable("ViewSeminarArtPreis")->findByDql("rubrik = ? AND status = ? AND inhouse= ? ORDER BY id ASC",array( $rubrik->id ,1, 0) );
		foreach ($seminarArten as $seminarArt) {
			$arr[$seminarArt->id] = $seminarArt->toArray();

			$arr[$seminarArt->id]['Seminare'] = $seminarArt->getFutureSeminare();
		}

		$this->dsDb->add("SeminarArten", $arr);
			//$this->dsDb->add("Farbe", str_replace("0x", "#",$seminarArt->farbe));
			//$this->dsDb->add("Textfarbe", str_replace("0x", "#",$seminarArt->textfarbe));

			//$this->dsDb->add("SeminarArt", $seminarArt->toArray());


		$this->dsDb->add("Heute", date("d.m.Y"));
		return $this->generateHtml($this->template);
	}

}
