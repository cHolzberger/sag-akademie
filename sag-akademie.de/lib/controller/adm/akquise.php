<?php

include("adm/dbcontent.php");

class ADM_Akquise extends ADM_DBContent {

	function map($name) {
		return "ADM_Akquise";
	}

	function fetchOne($id, $refresh=false) {
		$result = Doctrine::getTable("AkquiseKontakt")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
		return $result;
	}

	function onSearch(& $pr, & $ds) {

		switch (@$_GET['art']) {
			case 1:
				$table = 'ViewAkquise';
				$fn = "akquise/search.xml";
				break;
			case 2:
				$table = 'Kontakt';
				$fn = "kontakte/search.xml";
				break;
			case 3:
				$table = 'ViewAkquiseKontaktR';
				$fn = "akquise/search_full.xml";
				break;
			default:
				$table = 'ViewAkquise';
				$fn = $this->name() . "/search.xml";
				break;
		}
		//$this->dsDb->add("Results", $this->getData($table));
		$pr->loadPage($fn);
	}

	function getData($table) { /* callback for the dbtable module */
		MosaikDebug::msg($table, "getData");
		if (array_key_exists("by", $_GET)) {
			$fnkt = "findBy" . $this->query('by');
			$table = Doctrine::getTable($table);
			$result = $table->$fnkt($this->query('value'));
		} else if ($this->query("a", false)) {
			$hits = Doctrine::getTable($table)->search($this->query("a") . "%");
			$ids = array();

			if (count($hits) < 1) {
				return array();
			}

			foreach ($hits as $hit) {
				$ids[] = $hit['id'];
			}

			$q = Doctrine::getTable($table)->detailedIn($ids);
			$result = $q->fetchArray();
		} else if ($this->query("q", false)) {
			//MosaikDebug::msg($table, "Search in");
			//MosaikDebug::msg($this->query("q"), "Search For");
			$hits = Doctrine::getTable($table)->search(utf8_encode(urldecode($this->query("q"))));

			$ids = array();

			if (count($hits) < 1) {
				return array();
			}

			foreach ($hits as $hit) {
				$ids[] = $hit['id'];
			}

			$q = Doctrine::getTable($table)->detailedIn($ids);
			$result = $q->fetchArray();
		} else {
			$result = Doctrine::getTable($table)->findAll();
		}
		return $result;
	}

	function onKonvert() {
		$akquise = $this->fetchOne($this->entryId);
		if ($akquise['id'] == "") {
			instantRedirect("/admin/akquise;iframe");
		}
		MosaikDebug::msg($akquise, "Akquise");
		if (trim($akquise['firma']) != "") {
			$kontakt = new Kontakt();
			$kontakt->firma = $akquise['firma'];
			$kontakt->strasse = $akquise['strasse'];
			$kontakt->nr = $akquise['nr'];
			$kontakt->plz = $akquise['plz'];
			$kontakt->ort = $akquise['ort'];
			$kontakt->bundesland = $akquise['bundesland'];
			$kontakt->bundesland_id = $akquise['bundesland_id'];
			$kontakt->newsletter = $akquise['newsletter'];
			$kontakt->newsletter_abmeldedatum = $akquise['abmelde_datum'];
			$kontakt->newsletter_anmeldedatum = $akquise['anmelde_datum'];
			$kontakt->geaendert = $akquise['geaendert'];
			$kontakt->geaendert_von = $akquise['geaendert_von'];
			$kontakt->tel = $akquise['tel'];
			$kontakt->fax = $akquise['fax'];
			$kontakt->url = $akquise['url'];
			$kontakt->kontaktkategorie = $akquise['kontaktkategorie'];
			$kontakt->kontakt_quelle = $akquise['kontakt_quelle'];
			$kontakt->kontakt_quelle_stand = $akquise['kontakt_quelle_stand'];
			$kontakt->wiedervorlage = $akquise['wiedervorlage'];
			$kontakt->branche = $akquise['branche'];
			$kontakt->save();
			MosaikDebug::msg($kontakt, "Kontakt");
			$kontakt_id = $kontakt->id;
		} else {
			$kontakt_id = 1;
		}
		$ansprechpartner = new Person();
		$ansprechpartner->kontakt_id = $kontakt_id;
		$ansprechpartner->vorname = $akquise['vorname'];
		if ($akquise['name'] == "") {
			$ansprechpartner->name = "Unbekannt";
		} else {
			$ansprechpartner->name = $akquise['name'];
		}
		$ansprechpartner->tel = $akquise['tel_durchwahl'];
		$ansprechpartner->mobil = $akquise['mobil'];
		if ($akquise['ansprechpartner_email'] != "") {
			$ansprechpartner->email = $akquise['ansprechpartner_email'];
		} else {
			$ansprechpartner->email = $akquise['email'];
		}
		$geschlecht = 1;
		if (stripos($akquise['anrede'], "herr") !== false) {
			$geschlecht = 0;
		}
		if (stripos($akquise['anrede'], "damen und herren") !== false) {
			$geschlecht = 2;
		}
		$ansprechpartner->geschlecht = $geschlecht;
		$ansprechpartner->save();
		$result = Doctrine::getTable("AkquiseKontakt")->find($this->entryId);
		$result->kontakt_id = $kontakt_id;
		$result->save();
		$result->delete();
		MosaikDebug::msg($ansprechpartner, "Person");
		instantRedirect("/admin/kontakte/" . $kontakt_id . ";iframe?edit");
	}

	function onSave() {
		$result = $this->getOneClass($this->entryId);
		$data = $_POST[$this->entryTable];

		if ( $this->entryId == "new") {
			$result->angelegt_user_id = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
			$result->angelegt_datum = currentMysqlDatetime();
			$result->kontakt_quelle_stand = currentMysqlDatetime();

		}

		$data["geaendert_von"] = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
		$data["geaendert"] = currentMysqlDatetime();

		if (@ $data['newsletter'] != "1") {
			$data['newsletter'] = 0;
		}

		if (@ $data['qualifiziert'] != "1") {
			$data['qualifiziert'] = 0;
		}

		if (@ $data['vdrk_mitglied'] != "1") {
			$data['vdrk_mitglied'] = 0;
		}

		if (@ $data['dwa_mitglied'] != "1") {
			$data['dwa_mitglied'] = 0;
		}

		if (@ $data['rsv_mitglied'] != "1") {
			$data['rsv_mitglied'] = 0;
		}

		$data['anmelde_datum'] = mysqlDateFromLocal($data['anmelde_datum']);
		$data['abmelde_datum'] = mysqlDateFromLocal($data['abmelde_datum']);
		$data['qualifiziert_datum'] = mysqlDateFromLocal($data['qualifiziert_datum']);
		
		
		$data['kontakt_quelle_stand'] = mysqlDateFromLocal($data['kontakt_quelle_stand']);

		if ($data['qualifiziert'] == "1" && $result->qualifiziert == 0) {
			$data['qualifiziert_datum'] = currentMysqlDate();
		}

		if ($data['newsletter'] == "1" && $result->newsletter == 0) {
			$data['anmelde_datum'] = currentMysqlDate();
		} else if ($data['newsletter'] != "1" && $result->newsletter == 1) {
			$data['newsletter'] = 0;
			$data['abmelde_datum'] = currentMysqlDate();
		}

		MosaikDebug::msg($data, "data");

		$result->merge($data);
		$result->abmelde_datum = $data['abmelde_datum'];
		$result->anmelde_datum = $data['anmelde_datum'];
		$result->qualifiziert_datum = $data['qualifiziert_datum'];
		MosaikDebug::msg($result->toArray(), "d");

		$result->save();
		$this->entryId = $result->id;
		instantRedirect("/admin/akquise/" . $result->id . ";iframe?edit");
	}

}

?>