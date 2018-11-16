<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
class JSON_Referent extends JsonComponent {
      function POST() {
	return $this->renderJson();
    }

      function getTable () {
	return Doctrine::getTable("Referent");
    }

	function renderJson() {
		qlog(__CLASS__."::".__FUNCTION__);   
		$jr = new MosaikJsonResult();

		$jr->q = Doctrine::getTable( "Referent" )->getRefs();
		$jr->headers = $this->getHeaders();
		$jr->headline = "Referenten";
		
		if ( ($q = MosaikConfig::getEnv("q", false)) ) {
			$search = "%" . $q . "%";
			
			$jr->q->andWhere("referent.name LIKE ? OR referent.vorname LIKE ? or referent.firma LIKE ?", array( $search, $search, $search));
		} else if (MosaikConfig::getEnv("showInactive", false)) {
			
		} else {
			// show only status aktiv = 1
			// and status planung =4 
			$jr->q->andWhere("referent.status = 1 OR referent.status = 4 OR referent.status = 0");
		}
		
		
		if ( MosaikConfig::getEnv("all", false) ) {
		    return $jr->renderAll();
		} else {
		    return $jr->render();
		}
	}

	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['Referent'];
	}

	function filter() {

	}
}

?>
