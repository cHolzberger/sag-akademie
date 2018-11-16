<?php

include_once ("generic/adminpdfdocument.php");
include_once("services/BuchungService.php");

class ADM_PDF_Buchung extends Generic_AdminPDFDocument {
	function map($name) {
		return "ADM_PDF_Buchung";
	}
	
	function renderPdf() {
		$this->initDatasource();
		return $this->generateHtml("buchung");
	}

	function renderPdfForward() {
		$this->entryId=$this->next();

		qlog(__CLASS__ . "::" . __FUNCTION__ . ": entryId: {$this->entryId}");
		try {
			$this->initDatasource();

			$buchung = $this->fetchOne($this->entryId);

			$this->dsDb->add("Buchung", $buchung);

			$arr = Doctrine::getTable("HotelBuchung")->detailedByBuchungId($this->entryId)->execute();

			if ($arr->count() > 0 && $arr[0]->hotel_id != 0) {
				$this->dsDb->add("HotelGebucht", "1");
				$this->dsDb->add("HotelBuchung", $arr->getFirst()->toArray());
			} else {
				$this->dsDb->add("HotelGebucht", "0");
			}

			$seminare = Doctrine::getTable("Seminar")->findByseminar_art_id($buchung['Seminar']['seminar_art_id']);

			foreach ($seminare as $seminar) {
				$seminar['datum_begin'] = mysqlDateToLocal($seminar['datum_begin']);
				$seminar['datum_ende'] = mysqlDateToLocal($seminar['datum_ende']);
			}

			$this->dsDb->add("SeminarTermine", $seminare->toArray());
			$this->dsDb->add("SeminarTerminID", $buchung['Seminar']['seminar_art_id']);
			setHttpFilename("Buchung.pdf");

			return $this->generateHtml("buchung");
		} catch (Exception $e) {
			qlog("Exception:");
			qlog($e->getMessage());
		}
	}

	function renderHtml() {
		$this->initDatasource();
		return $this->generateHtml("buchung");
	}

	function renderHtmlForward() {
		return $this->renderPdfForward();
	}
	
	/** Data fetcher function * */
	function fetchOne($id, $res=false) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": id: {$id}");

		$info = Doctrine::getTable("ViewBuchungPreis")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
		return $info;
	}

}

?>