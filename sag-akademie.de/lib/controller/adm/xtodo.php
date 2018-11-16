<?php
include("adm/dbcontent.php");

class ADM_XTodo extends ADM_DBContent {
	var $entryTable = "x_todo";
	var $entryClass = "XTodo";

	function map($name) {
		return "ADM_XTodo";
	}

	function onIndex(&$pr, &$ds) {
		$pr->loadPage( $this->name() . ".xml");
	}

	function onEdit(&$pr, &$ds) {
		$todo = Doctrine::getTable("XTodo")->find ( $this->entryId );
		$data = $todo->toArray(true);
		//$user = Doctrine::getTable("XUser")->find ( $this->erstellt_von_id);
		$data['ErstelltUser'] = $todo->ErstelltUser->toArray();
		MosaikDebug::msg($data,"Todo Info");
		$ds->add("XTodo", $data);
		
		$pr->loadPage($this->name() . "/edit.xml" );
	}

	function onSave( & $pr, & $ds) {
		$result = $this->getOneClass($this->entryId);



		$result->merge($_POST[$this->entryTable]);

		if ($result->id==0) {
			$result->erstellt_von_id = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
			$result->erstellt =  currentMysqlDatetime();
		}

		if ( $result->status_id == 3) {
		    $result->erledigt = currentMysqlDate();
		}

		$result->save();
		$this->entryId = $result->id;
		// no need for it
		//$fn =  $this->name() . "/saved.xml";
		//$pr->loadPage( $fn );
		//return ""; // seite wird im framework neu geladen!
		instantRedirect("/admin/".$this->name()."/". $this->entryId .";iframe?edit"); // FIXME: iframe nur wenn auch nen iframe vorhanden ist
	}
	
}