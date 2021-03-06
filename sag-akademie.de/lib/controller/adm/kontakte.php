<?php
include("adm/dbcontent.php");

class ADM_Kontakte extends ADM_DBContent {
	function map($name) {
		return "ADM_Kontakte";
	}

	function fetchOne($id, $refresh=false) {
		$result = Doctrine::getTable("Kontakt")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
		return $result;
	}
	function onSearch( & $pr, & $ds) {
                $fn = $this->name()."/search.xml";
                $pr->loadPage($fn);
        }
	function onEdit( & $pr, & $ds) {
		$kontakt = Doctrine::getTable("Kontakt")->find($this->entryId);
		$ansprechpartner = $kontakt->getAnsprechpartner();
		
		
		if ( is_object($ansprechpartner)) {
			$ds->add("Ansprechpartner", $ansprechpartner->toArray() );
		}
		
		$fn = $this->name()."/edit.xml";
		$pr->loadPage($fn);
	}
	
	function onSave() {
		$result = $this->getOneClass($this->entryId);
		$data = $_POST[$this->entryTable];
		
		if ( $this->entryId == "new") {
			$result->angelegt_user_id = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
			$result->angelegt_datum = currentMysqlDatetime();
			$result->kontakt_quelle_stand = currentMysqlDatetime();
			$result->kontext = "Kunde";
		}
		
		if ( @ $data['newsletter'] != "1") {
			$data['newsletter'] = 0;
		}

		if ( @ $data['wiedervorlage'] != "1") {
			$data['wiedervorlage'] = 0;
		}

		if ( @ $data['vdrk_mitglied'] != "1") {
			$data['vdrk_mitglied'] = 0;
		}

		if ( @ $data['dwa_mitglied'] != "1") {
			$data['dwa_mitglied'] = 0;
		}

		if ( @ $data['rsv_mitglied'] != "1") {
			$data['rsv_mitglied'] = 0;
		}
		
		$data['newsletter_anmeldedatum'] = mysqlDateFromLocal($data['newsletter_anmeldedatum']);
		$data['newsletter_abmeldedatum'] = mysqlDateFromLocal($data['newsletter_abmeldedatum']);
		
		if ( @$data['newsletter']=="1" && $result->newsletter == 0) {
			$data['newsletter_anmeldedatum'] = currentMysqlDate();
		} else if ( @ $data['newsletter'] != "1" && $result->newsletter==1 ){ 
			$data['newsletter']=0;
			$data['newsletter_abmeldedatum'] = currentMysqlDate();
		}
		$data["geaendert_von"] = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
                $data["geaendert"] = currentMysqlDatetime();
		$result->merge($data);
		$result->save();
		$this->entryId = $result->id;
		instantRedirect ("/admin/kontakte/".$result->id.";iframe?edit");
	}
}
