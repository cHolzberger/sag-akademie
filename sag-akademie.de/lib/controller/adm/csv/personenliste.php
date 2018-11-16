<?php
include_once ("generic/csvdocument.php");

class ADM_CSV_Personenliste extends Generic_CSVDocument {
	
	function map($name) {
		return "ADM_CSV_Personenliste";
	}
	function csvFieldOutput($array, $prefix)
	{
		$cache = "";
		foreach ($array as $key => $val) {
			if(is_array($val)) {
				$cache .= $this->csvFieldOutput($val, $key);
			}else{
				$cache .= "\"".$prefix.':'.$key . "\";";
			}
		}
		return $cache;
	}
	function csvEntryOutput($each)
	{
		$cache = "";
		foreach ( $each as $data ) {
			if(is_array($data)) {
				$cache .= $this->csvEntryOutput($data);
			}else{
			 	$cache .= "\"". str_replace('"', '""', utf8_decode($data)) . "\";";
			}
		}
		return $cache;
	}
	function renderCsv() {
		$person = Doctrine::getTable("Person")->personKontaktQ()->fetchArray();
		
		setHttpFilename("Personenliste.csv");
		$cache = "";
		$cache .= $this->csvFieldOutput($person[0], 'Person');
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
}
