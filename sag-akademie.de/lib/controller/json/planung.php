<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class JSON_Person extends k_Component {
      function POST() {
	return $this->renderJson();
    }

	function renderJson() {
		$tbl = Doctrine::getTable("Person");
		$data = $tbl->findAll(Doctrine::HYDRATE_ARRAY);
		
		//foreach ( $data as $key=>$d) {
		//	$data[$key]['kontaktQuelleStand'] = mysqlDateToLocal($d['kontaktQuelleStand']);
		//}
		
		return json_encode( array( 
			"headers" => $this->getHeaders(), 
			"data"=> $data  
		));
	}
	
	function getHeaders() {
		$head = array();
		$head[] = array ( "field"=> 'firma', "label"=>"Firma", "format"=>"default");
		$head[] = array ( "field"=> 'alias',"label"=>"Alias", "format"=>"default");
		$head[] = array ( "field"=> 'aktiv',"label"=>"Aktiv", "format"=>"default");
		$head[] = array ("field"=> 'email', "label"=>"EMail", "format"=>"email");
		$head[] = array ( "field"=> 'strasse',"label"=>"Straße", "format"=>"default");
		$head[] = array ("field"=> 'nr', "label"=>"Nr", "format"=>"default");
		$head[] = array ("field"=> 'plz', "label"=>"Plz", "format"=>"default");
		$head[] = array ( "field"=> 'tel',"label"=>"Tel.", "format"=>"default");
		$head[] = array ("field"=> 'fax', "label"=>"Fax", "format"=>"default");
		$head[] = array ("field"=> 'url', "label"=>"Homepage", "format"=>"web");
		$head[] = array ("field"=> 'bundesland', "label"=>"Bundesland", "format"=>"default");
		$head[] = array ("field"=> 'ort', "label"=>"Ort", "format"=>"default");
		$head[] = array ("field"=> 'kontaktQuelleStand', "label"=>"Datensatz-Stand", "format"=>"date");
		$head[] = array ("field"=> 'kontaktQuelle', "label"=>"Datensatz-Quelle", "format"=>"default");

		return $head;
	}
	
	function filter() {
		
	}
}

?>
