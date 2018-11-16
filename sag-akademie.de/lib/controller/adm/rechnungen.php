<?php
include("adm/dbcontent.php");

class ADM_Rechnungen extends ADM_DBContent {
	function map($name) {
		return "ADM_Rechnungen";
	}
	
	function onShowList(&$pr, &$ds) {
		$GLOBALS['firephp']->log("onShowList");
		$GLOBALS['dbtableDataFetch'] = $this;
		
		//$result['Buchungen'] = Doctrine::getTable("ViewBuchungPreis")->unbezahlt()->fetchArray();
		
		//$GLOBALS['firephp']->log($result, "result");
		
		//$ds->set($result);
		$pr->loadPage( $this->name() . ".xml");
	}
	
	function onSave(&$pr, &$ds) {
		// wird nicht genutzt
		$result = $this->getOneClass($this->entryId);
		
		$result->merge($_POST[$this->entryTable]);
		$result->rechnungGestellt = mysqlDateFromLocal( $_POST['rechnungGestellt']);
		$result->bezahlt = mysqlDateFromLocal( $_POST['bezahlt']);
		$result->storno_datum = mysqlDateFromLocal( $_POST['storno_datum']);
        $result->save();
		$this->entryId = $result->id;
		$fn =  $this->name() . "/saved.xml";
		$pr->loadPage( $fn );

		instantRedirect("/admin/".$this->name()."/". $this->entryId . ";iframe?edit");
	}
}
