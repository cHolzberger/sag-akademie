<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once ("templates/sag/JsonComponent.php");
require_once ("Mosaik/JsonResult.php");
require_once ("services/BuchungService.php");

class JSON_TerminBuchungen extends JsonComponent {

	function map() {
		return "JSON_TerminBuchungen";
	}

	function getTable() {
		return Doctrine::getTable("Buchung");
	}

	function forward() {
		if (array_key_exists("data", $_POST)) {
			$this->POST();
		}

		return $this->renderJson();
	}

	function renderJson() {
		qlog(__CLASS__ . "::" . __FUNCTION__);
		qdir($_POST);
		$seminar_id = $this->next();
		$jr = new MosaikJsonResult( );
		$headline = "";
		$q = Doctrine_Query::create()->from("ViewBuchungPreis")
			->where("deleted_at = ?", "0000-00-00 00:00:00")
			->andWhere("seminar_id = ?",$seminar_id)
			->orderBy("datum");
		$headline = "Buchungen - " . $seminar_id;

		$jr->headers = $this->getHeaders();

		// add the statement to the json response
		$jr->headline = $headline;
		$jr->q = $q;
		$jr->setCacheId("terminbuchungen_" . $seminar_id);
		//$jr->filter = $this;

		return $jr->renderAll("ViewBuchungPreis");
	}

	function extend(&$data) {
		$data['dnTarget'] = "/admin/personen/" . $data['person_id'] . "?edit";
		$data['dn'] = $data['name'] . ", " . $data['vorname'];

		$data['firmaTarget'] = "/admin/kontakte/" . $data['kontakt_id'] . "?edit";
		//$data['UmgebuchtSeminar:kursnrTarget'] = "xxx";
		$data['umgebucht_auf_kursnrTarget'] = "/admin/termine/" . $data['umgebucht_auf_id'] . "?edit";
	}

	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['Buchung'];
	}
}
?>
