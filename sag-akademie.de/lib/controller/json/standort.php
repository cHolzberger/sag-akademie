<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
*/
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
class JSON_Standort extends JsonComponent {
    function map() {
	return "JSON_Standort";
    }

    function forward($class_name, $namespace = "") {
	$jr = new MosaikJsonResult();
	$standort_id = $this->next();
	$q = Doctrine_Query::create();
	$q->from("Standort");
	$q->where("id=?", $standort_id );
	$jr->headline = "Standort " . $q;
	$jr->q = $q;
	$jr->headers = $this->getHeaders();

	return $jr->renderSingle();
    }

    function renderJson() {

	$jr = new MosaikJsonResult();
	$jr->q = Doctrine::getTable( "Standort" )->getAll();
	$jr->headers = $this->getHeaders();
	$jr->headline = "Standorte";

	return $jr->render();
    }

    function getHeaders() {
	global $_tableHeaders;
	return $_tableHeaders['Standort'];
    }

    function filter() {

    }
}


?>
