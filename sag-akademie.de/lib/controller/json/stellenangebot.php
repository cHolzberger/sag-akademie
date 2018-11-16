<?php
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");
class JSON_Stellenangebot extends JsonComponent {
	function getTable() {
	    return Doctrine::getTable("Stellenangebot");
	}
	function renderJson() {
		$jr = new MosaikJsonResult();
		$jr->q = Doctrine_Query::create()->from("Stellenangebot");
		if ( ( $kategorie = MosaikConfig::getEnv("kategorie", false )) ) {
			$jr->q->where("kategorie = ?", $kategorie);
		}
		$jr->headers = $this->getHeaders();
		$jr->headline = "Stellenangebot";

		return $jr->render();
	}
	function getHeaders() {
		$head = array();
		$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
		$head[] = array ("field"=> 'name', "label"=>"Name", "format"=>"text", "editable"=>"true", "group"=>"Stellenangebote");
		$head[] = array ("field"=> 'text', "label"=>"Text", "format"=>"text", "editable"=>"true", "group"=>"Stellenangebote");
		$head[] = array ("field"=> 'link', "label"=>"Link", "format"=>"text", "editable"=>"true", "group"=>"Stellenangebote");
		$head[] = array ("field"=> 'kategorie', "label"=>"Kategorie", "format"=>"text", "editable"=>"true", "group"=>"Stellenangebote");

		$head[] = array ("field"=> 'deleted_at', "label"=>"Deleted", "format"=>"text" , "hide"=>true);
		return $head;
	}
}
?>
