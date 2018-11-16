<?php
include("adm/dbcontent.php");

class ADM_REDIRECTOR_Kontakt extends ADM_DBContent {
	var $prepareEdit = false;

	function map($name) {
		return "ADM_REDIRECTOR_Kontakt";	
	}
	
	function onEdit (&$pr, &$ds) {
		$name = ""; // variable vorher initialisieren

		if(substr($this->next(),0,2) == "ak") {
		  $name = "akquise";
		}else {
		  $name = "kontakte";
		}
		

		$id = substr($this->next(),2);
		MosaikDebug::msg($name . " " . $id, "ADM_REDIRECTOR_Kontakt::onEdit");
		MosaikDebug::msg("/admin/".$name."/".$id.";iframe?edit", "ADM_REDIRECTOR_Kontakt::onEdit");
		instantRedirect("/admin/".$name."/".$id.";iframe?edit");//
		
	}

	function delete() {
		MosaikDebug::msg($this->entryId, "delete");
		$id = substr($this->next(),2);
		if(substr($this->next(),0,2) == "ak") {
		  $name = "AkquiseKontakt";
		}else {
		  $name = "Kontakt";
		}
		$row = Doctrine::getTable($name)->find($id);
		if (is_object($row)) $row->delete();
	}
}
?>