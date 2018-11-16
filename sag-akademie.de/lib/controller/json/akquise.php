<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");


class JSON_Akquise extends JsonComponent {

    function getTable() {
	return Doctrine::getTable("AkquiseKontakt");
    }

    function search($q) {
	$table = "ViewAkquise";
	$q = Doctrine::getTable($table)->detailSearch($q);

	return $q;
    }

    function searchStart($a) {
	$table = "ViewAkquise";
	$a = utf8_encode(urldecode($a));
	$q = Doctrine::getTable($table)->detailSearch($a . "%");
	return $q;
    }

    function advancedSearch($rules) {
	ksort($rules);
	$table = "ViewAkquise";
	return Doctrine::getTable($table)->advancedSearch($rules);
    }

    function renderJson() {
	$jr = new MosaikJsonResult();

	if (isset($_POST['advancedSearch'])) {
	    MosaikDebug::msg($_POST, "POST");
	    $jr->q = $this->advancedSearch($_POST['rules']);
	    $jr->headline = "Firmen - Erweiterte Suche";
	} else if (isset($_POST['q'])) {
	    $jr->q = $this->search($_POST['q']);
	    $jr->headline = "Firmen - Suche nach: " . MosaikConfig::getEnv("q");
	} else if (isset($_POST['a'])) {
	    $jr->q = $this->searchStart($_POST['a']);
	    $jr->headline = "Firmen - Suche nach: " . MosaikConfig::getEnv("a");
	} else {
	    $tbl = Doctrine::getTable("Kontakt");
	    $q = $tbl->findAll(Doctrine::HYDRATE_ARRAY);
	}

	$jr->headers = $this->getHeaders();
	$jr->headline .= " (Akquise Kontakte)";

	if ( MosaikConfig::getEnv('mode',"paged") == "all") {
	       return $jr->renderAll();
	} else {
	    return $jr->render();
	}
    }

    function getHeaders() {
		global $_tableHeaders;
    
		return $_tableHeaders['ViewAkquise'];
    }

    function filter() {
	
    }

}
?>
