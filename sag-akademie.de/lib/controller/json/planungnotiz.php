<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class JSON_PlanungNotiz extends k_Component {
      function POST() {
	return $this->renderJson();
    }

	function map() {
		return "JSON_Seminar";
	}

	function renderJson() {
		$tbl = Doctrine::getTable("ViewPlanungNotiz");
		$data = $tbl->findAll(Doctrine::HYDRATE_ARRAY);
		$entrys = array();


		foreach ( $data as $tag) {
		    $entrys [$tag['monat']][$tag['tag']] = $tag;
		}
		
		return json_encode( array(
			"mode" => "multi",
			"headers" => $this->getHeaders(),
			"data"=> $entrys
		));
	}

	function forward () {
		$tbl = Doctrine::getTable("ViewPlanungNotiz");
		$data = $tbl->find($this->next(), Doctrine::HYDRATE_ARRAY);
		$entrys = array();

		foreach ( $data as $tag) {
		    $entrys [$tag['monat']][$tag['tag']] = $tag;
		}
		
		return json_encode( array(
			"mode" => "single",
			"headers" => $this->getHeaders(),
			"data"=> $entrys
		));
	}
	
	function getHeaders() {
		$head = array();
		return $head;
	}
	
	function filter() {
		
	}
}

?>
