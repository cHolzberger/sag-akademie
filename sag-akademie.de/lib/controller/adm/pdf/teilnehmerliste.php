<?php

include_once ("generic/adminpdfdocument.php");

class ADM_PDF_Teilnehmerliste extends Generic_AdminPDFDocument {

	function map($name) {
		return "ADM_PDF_Teilnehmerliste";
	}

	function renderPdf() {
		$this->initDatasource();
		return $this->generateHtml("teilnehmerliste");
	}

	function renderPdfForward() {
		qlog(__CLASS__ . "::" . __FUNCTION__);
		try {
			$this->initDatasource();
			$seminar = Doctrine::getTable("Seminar")->find($this->next());
			$teilnehmer = $seminar->getTeilnehmer()->fetchArray();
			$splitTeilnehmer = array();
			for ($i = 0; $i < 2; $i++) {
				$splitTeilnehmer[$i] = array();
			}
			for ($i = 0; $i < count($teilnehmer); $i++) {
				
				if ($i < 13) {
					$splitTeilnehmer[0][] = $teilnehmer[$i];
				} else {
					$splitTeilnehmer[1][] = $teilnehmer[$i];
				}
			}

			$this->dsDb->add("Seminar", $seminar->toArray());
			$this->dsDb->add("Standort", $seminar->Standort->toArray());
			$this->dsDb->add("SeminarArt", $seminar->SeminarArt->toArray());
			$this->dsDb->add("Teilnehmer", $teilnehmer);
			$this->dsDb->add("Heute", date("d.m.Y"));

			for ($i = 0; $i <= 1; $i++) {
				$this->dsDb->add("Teilnehmer$i", $splitTeilnehmer[$i]);
			}
			setHttpFilename("Anwesenheitsliste-" . $seminar->kursnr . ".pdf");
			return $this->generateHtml("teilnehmerliste");
		} catch (Exception $e) {
			qlog("Exception:");
			qlog($e->getMessage());
		}
	}

	function renderHtml() {
		$this->initDatasource();
		return $this->generateHtml("teilnehmerliste");
	}

	function renderHtmlForward() {
		return $this->renderPdfForward();
		/*$this->initDatasource();
		$seminar = Doctrine::getTable("Seminar")->find($this->next());
		$teilnehmer = $seminar->teilnehmer()->fetchArray();
		$this->dsDb->add("Seminar", $seminar->toArray());
		$this->dsDb->add("Standort", $seminar->Standort->toArray());
		$this->dsDb->add("SeminarArt", $seminar->SeminarArt->toArray());
		$this->dsDb->add("Teilnehmer", $teilnehmer);
		return $this->generateHtml("teilnehmerliste");*/
	}

}
