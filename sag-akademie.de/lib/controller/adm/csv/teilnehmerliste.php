<?php
include_once ("generic/csvdocument.php");

class ADM_CSV_Teilnehmerliste extends Generic_CSVDocument {
	public $aliasTable = array ( 
		'id' => "SeminarID",
		'kursnr' => "Kurs-Nr.",
		'firma' => "Firma",
		'strasse' => "Straße",
		'plz' => "PLZ",
		'ort' => "Ort",
		'bundesland' => "Bundesland",
		'land' => "Land",
		'telefon' => "Telefon",
		'fax' => "Fax",
		'mobil' => "Mobil",
		'email' => "Email",
		'url' => "Website",
		'name' => "Name",
		'vorname' => "Vorname",
		'abteilung' => "Abteilung",
		'funktion' => "Funktion",
		'person_strasse' => "Straße privat",
		'person_nr' => "Nr. privat",
		'person_plz' => "PLZ privat",
		'person_ort' => "Ort privat",
		'person_bundesland' => "Bundesland privat",
		'person_land' => "Land privat",
		'person_telefon' => "Telefon privat",
		'person_fax' => "Fax privat",
		'person_mobil' => "Mobil privat",
		'person_email' => "Email privat"
		
	);
	
	function map($name) {
		return "ADM_CSV_Teilnehmerliste";
	}
	
	function renderCsv() {
		setHttpFilename("Teilnehmerliste-".$seminar->kursnr.".csv");
		$this->initDatasource();
		return "";
	}
	
	function renderCsvForward() {
		$cache = "";
		
		$seminar = Doctrine::getTable("Seminar")->find( $this->next() );
		$teilnehmer = $seminar->teilnehmer()->fetchArray();
		
		setHttpFilename("Teilnehmerliste-".$seminar->kursnr.".csv");

		foreach ( array_keys ( $teilnehmer[0] ) as $key) {
			if ( array_key_exists($key, $this->aliasTable)) {
				$cache .= '"'.utf8_decode($this->aliasTable[$key] .'";');
			} else {
				$cache .= $key . ";";
			}
		}
		$cache .= "\n";
		
		foreach ( $teilnehmer as $each) {
			foreach ( $each as $data ) {
				$cache .= '"'.utf8_decode($data). '";';
			}
			$cache .= "\n";
		}
		
		return $cache;
	}
}
