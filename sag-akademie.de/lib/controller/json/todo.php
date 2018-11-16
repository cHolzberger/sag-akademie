<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class JSON_Todo extends k_Component {
	function renderJson() {
		$tbl = Doctrine::getTable("XTodo");
		
		$data = $tbl->findAll()->toArray();

	
		return json_encode( array( 
			"headers" => $this->getHeaders(), 
			"data"=> $data  
		));
	}
	  function POST() {
	return $this->renderJson();
    }
	function getHeaders() {
		global $_tableHeaders;

		

		return $_tableHeaders['Todo'];
	}
	
	function filter() {
		
	}
}

?>
