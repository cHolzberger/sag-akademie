<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
class JSON_Hotel extends JsonComponent {
	function getTable() {
	    return Doctrine::getTable("Hotel");
	}

	function map() {
	    return "JSON_Hotel";
	}
	
	function extend(&$data) {
		    $tid = $data['id'];
		    $obj = Doctrine::getTable("Hotel")->find($tid);
		    $data = array_merge ($data, $obj->HotelPreise[0]->toArray());
		    $data['id'] = $tid;

		    if ( isset ($_GET['convert'])) {
			$data['zimmerpreis_ez'] = euroPreis($data['zimmerpreis_ez']);
			$data['zimmerpreis_dz'] = euroPreis($data['zimmerpreis_dz']);
			$data['marge'] = euroPreis($data['marge']);
			$data['fruehstuecks_preis'] = euroPreis($data['fruehstuecks_preis']);
		    }
	    
	}

	// single hit requested
	function forward () {
	    $jr = new MosaikJsonResult();
		$jr->q = Doctrine::getTable("Hotel")->remoteObject( $this->next() );
		$jr->filter = $this;
		$jr->headers = $this->getHeaders();
		$jr->headline = "Hotels";
		return $jr->renderSingle();
	}

	function renderJson() {
		$jr = new MosaikJsonResult();
		$jr->q = Doctrine::getTable("Hotel")->remoteList();
		$jr->filter = $this;
		$jr->headers = $this->getHeaders();
		$jr->headline = "Hotels";
		return $jr->render();
	}
	
	function getHeaders() {
		global $_tableHeaders;
	
		return $_tableHeaders['Hotel'];
	}
	
}

?>
