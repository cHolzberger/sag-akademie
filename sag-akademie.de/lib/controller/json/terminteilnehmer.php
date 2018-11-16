<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
require_once("services/BuchungService.php");

class JSON_TerminTeilnehmer extends JsonComponent {

    function map() {
	return "JSON_TerminTeilnehmer";
    }

    function getTable() {
	return Doctrine::getTable("Buchung");
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

	$q = Doctrine::getTable("ViewBuchungTeilnehmerEmail")->getListBySeminarId( $this->next() );
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

	$head[] = array("field" => 'status', "label" => "Status", "format" => "status", "group"=>"Status");
	$head[] = array("field" => 'email', "label" => "E-Mail", "format" => "default", "group"=>"Person");

	return $head;
    }

    function filter() {
	
    }

}

?>
