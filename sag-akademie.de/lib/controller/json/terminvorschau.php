<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
require_once("services/BuchungService.php");

class JSON_TerminVorschau extends JsonComponent {

    function map() {
	return "JSON_TerminVorschau";
    }

    function getTable() {
	return Doctrine::getTable("seminar");
    }

    function forward () {
	if ( array_key_exists("data", $_POST) ) {
	    $this->POST();
	}

	return $this->renderJson();
    }

    function renderJson() {
	$jr = new MosaikJsonResult();
	$headline = "";

	$count = MosaikConfig::getEnv("count", 4);

	$q = Doctrine::getTable("Seminar")->getNext( $this->next(),  $count);
	$headline = "Teilnehmer - " .$this->next();

	$jr->headers = $this->getHeaders();


	// add the statement to the json response
	$jr->headline = $headline;
	$jr->q = $q;
	$jr->filter = $this;

	return $jr->renderAll();
    }


    function extend ( &$data ) {

    }


    function getHeaders() {
	$head = array();
	$head[] = array("field" => 'id', "label" => "ID", "format" => "hidden");

	$head[] = array("field" => 'datum_begin', "label" => "Datum", "format" => "status", "group"=>"Seminar");
	$head[] = array("field" => 'seminar_art_id', "label" => "SeminarArt", "format" => "default", "group"=>"Seminar");

	return $head;
    }

    function filter() {

    }

}

?>
