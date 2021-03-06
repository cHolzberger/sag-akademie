<?php
include("adm/dbcontent.php");

class ADM_Standort extends ADM_DBContent {
	function map($name) {
		return "ADM_Standort";
	}
	function fetchOne($id, $refresh=false) {
	    MosaikDebug::msg($id, "fetchOne");
	    $result = Doctrine::getTable("Standort")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
	    return $result;
	}
	function onSave(&$pr, &$ds) {
		$result = $this->getOneClass($this->entryId);
		$data = $_POST[$this->entryTable];

		if ( ! isset ($data['planung_aktiv'])) $data['planung_aktiv'] = 0;
		if ( ! isset ($data['sichtbar_planung'])) $data['sichtbar_planung'] = 0;
                $data["geaendert_von"] = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
                $data["geaendert"] = currentMysqlDatetime();
		$result->merge($data);
		$result->save();

		$fn =  $this->name() . "/saved.xml";
		$pr->loadPage( $fn );
		
		instantRedirect("/admin/".$this->name()."/". $this->entryId . ";iframe?edit");
	}


}
