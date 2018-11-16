<?php
include_once ("generic/csvdocument.php");

class ADM_CSV_SeminarReferent extends Generic_CSVDocument {
	
	function map($name) {
		return "ADM_CSV_SeminarReferent";
	}

	function headerText($key) {
	    $headers = array ();
	    //$headers['ViewSeminarReferentExport:id'] = "ID";
	    $headers['ViewSeminarReferentExport:bezeichnung'] = "Bezeichnung";
	    $headers['ViewSeminarReferentExport:Datum'] = "Datum";
	    $headers['ViewSeminarReferentExport:kursnr'] = "Kurs Nr.";
	    $headers['ViewSeminarReferentExport:freigabe_status'] = "Freigabe";
	    $headers['ViewSeminarReferentExport:Standort'] = "Standort";
	    $headers['ViewSeminarReferentExport:beginn'] = "Beginn";
	    $headers['ViewSeminarReferentExport:ende'] = "Ende";
	    $headers['ViewSeminarReferentExport:dauer'] = "Dauer";
	    $headers['ViewSeminarReferentExport:grad'] = "Grad";
	    $headers['ViewSeminarReferentExport:vorname'] = "Vorname";
	    $headers['ViewSeminarReferentExport:name'] = "Name";
    	$headers['ViewSeminarReferentExport:firma'] = "Firma";
		$headers['ViewSeminarReferentExport:optional'] = "Optional";
		$headers['ViewSeminarReferentExport:praxis_start'] = "Praixs - Von";
		$headers['ViewSeminarReferentExport:praxis_ende'] = "Praxis - Bis";
		$headers['ViewSeminarReferentExport:theorie_start'] = "Theorie - Von";
		$headers['ViewSeminarReferentExport:theorie_ende'] = "Theorie - Bis";
		

	    if (array_key_exists($key, $headers)) return $headers[$key];
	    return $key;
	}

	function csvFieldOutput($array, $prefix) {
		$cache = "";
		foreach ($array as $key => $val) {
			if ( $key == "seminar_year") continue;
			if ( $key == "referent_id") continue;

			if(is_array($val)) {
				$cache .= $this->csvFieldOutput($val, $key);
			}else{
				$label = $prefix.":".$key;
				$cache .= "\"". $this->headerText($label ) . "\";";
			}
		}
		return $cache;
	}

	function csvEntryOutput($each) {
		$cache = "";
		foreach ( $each as $key=>$data ) {
			if ( $key == "seminar_year") continue;
			if ( $key == "referent_id") continue;

			if(is_array($data)) {
				$cache .= $this->csvEntryOutput($data);
			}else{
			 	$cache .= "\"". str_replace('"', '""', utf8_decode($data)) . "\";";
			}
		}
		return $cache;
	}
	
	function renderCsv() {
		$this->checkReferent();
		$person = null;

		if ( @ isset($_GET['year'] )) {
		    $year = $_GET['year'];
				if ( @ isset($_GET['referent_id'] )) {
		    	$referent = Doctrine::getTable("Referent")->find($_GET['referent_id']);
					setHttpFilename ($referent->name.  "-" . $referent->vorname . "-" . $year . ".csv");
				} else {
					setHttpFilename ("SeminarReferenten-" . $year . ".csv");
				}
		    $query = Doctrine_Query::create();
		    $query->from ("ViewSeminarReferentExport export");
		    $query->select("export.*");
		    $query->orderBy("export.Datum");
				if ( @ isset($_GET['referent_id'] )) {
			    $query->where("export.referent_id = ? AND export.seminar_year = ?", array($referent->id, $year));
				} else {
					$query->where("export.seminar_year = ?", array($year));
				}

		    $person = $query->fetchArray();
		} else {
		    $person = Doctrine::getTable("ViewSeminarReferentExport")->findAll(Doctrine::HYDRATE_ARRAY);
		    setHttpFilename("SeminarReferent.csv");
		}
		$cache = "";
		$cache .= $this->csvFieldOutput($person[0], 'ViewSeminarReferentExport');
		$cache .= "\n";
		foreach ( $person as $each) {
			$cache .=  $this->csvEntryOutput($each);
			$cache .= "\n";
		}
		return $cache;
	}
	
	function renderCsvForward() {
		//
	}

	function checkReferent () {
		$data = Doctrine::getTable("ViewSeminarOhneReferent")->findAll(Doctrine::HYDRATE_ARRAY);
		for ($i=0; $i< count($data); $i++) {
			for ($j=1; $j <= $data[$i]['dauer']; $j++) {
				$q = Doctrine_Query::create();
				$q->from ("SeminarReferent semref")->select("semref.id")->where("tag = ? AND seminar_id = ?", array($j, $data[$i]['id']));
				if ( $q->count() == 0) {
					$r = new SeminarReferent();
					$r->seminar_id = $data[$i]['id'];
					$r->tag = $j;
					$r->standort_id = $data[$i]['standort_id'];
					$r->referent_id = -1;
					$r->save();
				}
			}
		}
	}
}
