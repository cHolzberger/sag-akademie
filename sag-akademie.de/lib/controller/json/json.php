<?php

$GLOBALS['path'] = array();

class JSON_Json extends SAG_Admin_Component {

	public $map = Array(
	 "startseite" => "JSON_Startseite",
	 "akquise" => "JSON_Akquise",

	 "kontaktfull" => "JSON_KontaktFull",
	 "kontakt" => "JSON_Kontakt",
	 "person" => "JSON_Person",
	 "termin" => "JSON_Seminar",
	 "terminBuchungen" => "JSON_TerminBuchungen",
	 "terminTeilnehmer" => "JSON_TerminTeilnehmer",
	 "terminReferenten" => "JSON_TerminReferenten",
	 "terminVorschau" => "JSON_TerminVorschau",
	 "seminar" => "JSON_SeminarArt",
	 // watch out! its twisted a bit
 	 "inhouseTermin" => "JSON_InhouseSeminar",
	 "inhouseSeminar" => "JSON_InhouseSeminarArt",
	 "hotel" => "JSON_Hotel",
	 "todo" => "JSON_Todo",
	 "referent" => "JSON_Referent",
	 "referenten" => "JSON_Referenten",
	 "rechnung" => "JSON_Rechnung",
	 "buchung" => "JSON_Buchung",
	 "autocomplete" => "JSON_Autocomplete",
	 "dropdown" => "JSON_Dropdown",
	 "neuigkeit" => "JSON_Neuigkeit",
	 "referentcollision" => "JSON_SeminarReferentCollision",
	 "user" => "JSON_User",
	 "standort" => "JSON_Standort",
	 "seminarArtReferent" => "JSON_SeminarArtReferent",
	 "seminarReferent" => "JSON_SeminarReferent",
	 "jahresplanung" => "JSON_Jahresplanung",
	 "feiertage" => "JSON_Feiertage",
 	 "feiertag" => "JSON_Feiertag",

	 "umkreissuche" => "JSON_Umkreissuche",
	 "planungNotiz" => "JSON_PlanungNotiz",
 	 "kooperationspartner" => "JSON_Kooperationspartner",
	 "stellenangebot" => "JSON_Stellenangebot",
	 "download" => "JSON_Download"
	);

	function map($name) {
		qlog("JSon => Open " . $name);

		if (array_key_exists($name, $this->map)) {
			return $this->map[$name];
		} else {
			throw new MosaikPageReader_PageNotFound("");
		}
	}

	function POST() {
		qlog("=== JSON REQUEST ===");
		return $this->renderJson();
	}

	function renderJson() {
		qlog("JSON Auth: " );
		qdir($_POST);
		$data = array();

		$userinfo = array();
		$data['namespace'] = MosaikConfig::getEnv("namespace", "global");

		if ($this->identity()->anonymous()) {
			$data['success'] = false;
		} else {
			$userinfo = $this->identity()->toArray();
			$userinfo['dn'] = $this->identity()->user();
			$data['userinfo'] = $userinfo;

			$data['success'] = true;
		}
		$data['post'] = $_POST;
		return json_encode($data);
	}

	function renderJsonForward($class_name, $namespace = "") {
		if (is_array($class_name)) {
			$namespace = $class_name[1];
			$class_name = $class_name[0];
		}

		$next = $this->createComponent($class_name, $namespace);
		$content = "";
		$ret = null; 
		try {
			$ret = $next->dispatch();
		} catch (Exception $e) {
			qlog("EXCEPTION");
			qlog($e->getMessage());
		}
		return $ret;
	}

	function HEAD() {
		throw new k_http_Response(200);
	}

}
