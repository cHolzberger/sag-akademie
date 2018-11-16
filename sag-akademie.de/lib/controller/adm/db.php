<?php
class ADM_DB extends SAG_Admin_Component {
	function construct() {
		$this->dsDb = new MosaikDatasource("dbtable");

	}

	function map($name) {
		if ( $name == "duplicateCheck" ) return "ADM_DB_DuplicateCheck";
		else if ( $name == "backup" ) return "ADM_DB_Backup";
		else if ( $name == "sql") return "SAG_Staticpage";
		else if ( $name == "phpinfo") return "SAG_Staticpage";
		else if ( $name == "apcinfo") return "SAG_Staticpage";
		else if ( $name == "labels") return "ADM_DB_Labels";
		else if ( $name == "setup") return "ADM_DB_Setup";
		else return "ADM_DB";
	}

	function forwardIframe($class, $namespace ) {
		$this->forward($class, $namespace);
	}

	function forward($class, $namespace="") {
		$GLOBALS['path'][] = array('name'=> $this->name(), 'url' => $this->url());

		if ( $this->next() == "duplicateCheck" || 
		    $this->next() == "backup" ||
		    $this->next() == "sql" ||
		    $this->next() == "labels" ||
		     $this->next() == "apcinfo" ||
		      $this->next() == "phpinfo" ||
		    $this->next() == "setup") {
			$next = $this->createComponent($class, $namespace);
			return $next->dispatch();
		}
		$GLOBALS['path'][] = array('name'=> $this->next(), 'url' => $this->url($this->next()));


		$name = $this->name();

		$this->entryId = $this->next();

		list($config, $content) = $this->createPageReader();
		$content->addDatasource ( $this->dataStore );
		$content->addDatasource ( $this->dsDb );

		$this->dsDb->add ("formaction", "/admin/" .$this->name() . "/".$this->entryId."?save");

		if ( $this->entryId == "upload") {
			$this->onUpload($content, $this->dsDb);
		} else if ( $this->entryId == "import") {
			$this->onImport($content, $this->dsDb);
		} else if ( $this->entryId == "objectstore") {
			$this->onObjectstore($content, $this->dsDb);
		} else if ( $this->entryId == "export") {
			$this->onExport($content);
		}

		//$content->datasourceList->log();
		return $content->output->get();
	}

	/****
	 * http events
	 */

	function GET() {
		$GLOBALS['path'][] = array('name'=> $this->name(), 'url' => $this->url());

		list($config, $content) = $this->createPageReader();
		$content->addDatasource ( $this->dsDb );

		$this->onPanel($content, $this->dsDb);

		return $content->output->get();
	}

	function HEAD() {
		throw new k_http_Response(200);
	}

	function onObjectstore ($pr, $ds) {
		$pr->loadPage("db/objectstore.xml");
	}
	/****
	 * event
	 */
	function onExport($pr) {
		$pr->loadPage("db/export.xml");
	}
	/****
	 * event
	 */
	function onImport($pr, $ds) {
		$this->dsDb->add ("formaction", "/admin/db/upload");
		$pr->loadPage("db/import.xml");
	}
/**
 * Konvertiert einen CSV String in ein Array
 * nach dem format:
 *		h1 ; h2:h2.1 ; h3:h3.1
 *		d1		   ; d2			; d3
 * in den array
 *		{
 *			"h1": "d1",
 *
 *			"h2": { "h2.1": "d2" }
 *			"h3": { "h3.1": "d3" }
 * @param File $csv
 */
	function csvToArray($csv) {
		$firstline = true;
		$pData = array();
		$pArray = array();
		$pKeys = array();

		while ( ($data = fgetcsv ($csv, 0, ";")) !== FALSE) {
			if ($firstline) {
				for( $i=0; $i<count($data); $i++ ) {
					$pKeys[$i] = $data[$i];
				}
				$firstline = false;

			} else {
				for ( $i= 0; $i < count($pKeys); $i++ ) {
					$_keys = explode ( ":",$pKeys[$i]);

					$ref = &$pData;
					foreach ( $_keys as $key ) {
						$ref = &$ref[$key];
					}
					$val = "";
					if ( is_numeric(str_replace(",",".", $data[$i])) ) {
						$val = floatval(str_replace(",",".",$data[$i]));
					} else {
						$val = $data[$i];
					}

					$ref = mb_convert_encoding( $val, "UTF-8","ISO-8859-1");
				}
				$pArray[] = $pData;
				$firstline = false;
			}
		}
		return $pArray;

	}

	function importBuchungen ( &$report ) {
		$importMarker = 2; // import version used
		$GLOBALS['firephp']->log($_FILES, "importBuchungen");
		$csv = fopen ($_FILES['csv']['tmp_name'], "r");

		$data = $this->csvToArray($csv);
		$tStandort = Doctrine::getTable("Standort");
		$tSeminar = Doctrine::getTable("Seminar");

		foreach ( $data as $entry ) {
			if ( ! empty ($entry['buchung']['nicht_importierfaehig'] )) continue;
			// MosaikDebug::infoDebug($entry);

			$kursnr = $entry['seminar']['kursnr'];
			
			// Buchung anlegen
				$buchung = new Buchung();
				$buchung->import = $importMarker;
				$hotelBuchung = new HotelBuchung();
				$hotelBuchung->import = $importMarker;

			// Seminar Suchen
			$kurs = Doctrine::getTable("Seminar")->findOneByKursnr($kursnr);
			if ( ! is_object($kurs)) {
				$report->add("Kurs nicht vorhanden: kursNr=" . $kursnr);
				$report->status(false);
				continue;
			}

			// id aus dem seminar laden
			// bestaetigt setzen
			$entry['buchung']['seminar_id'] = $kurs->id;
			if ( $entry['buchung']['bestaetigungs_datum'] == "-") {
				$entry['buchung']['bestaetigt']=0;
			} else {
				$entry['buchung']['bestaetigt']=1;
			}

			// Person Suchen
			// und ggf. buchung verfollstaendigen
			$person = Doctrine::getTable("Person")->detailSearch($entry['person']['name'], $entry['person']['vorname'], $entry['kontakt']['firma'])->execute();

			if ( $person->count() == 0 ) {
				$report->add("Person nicht gefunden: Firma=" . $entry['kontakt']['firma'] . " Person= " . $entry['person']['name'] . ", " . $entry['person']['vorname'] . "<br/>Seminar: " . $kursnr . "<br/>HotelBuchung>:" . $entry['hotel_buchung']['buchungs_datum']);
				$report->status(false);
				continue;
			}
			$person = $person[0];
			$entry['buchung']['person_id'] = $person->id;
			$entry['buchung']['storno_datum'] = mysqlDateFromLocal($entry['buchung']['storno_datum']);
			$entry['buchung']['bestaetigungs_datum'] = mysqlDateFromLocal($entry['buchung']['bestaetigungs_datum']);
			$entry['buchung']['umbuchungs_datum'] = mysqlDateFromLocal($entry['buchung']['umbuchungs_datum']);
			$entry['buchung']['zahlungseinang_datum'] = mysqlDateFromLocal($entry['buchung']['zahlungseingang_datum']);
			$entry['buchung']['umgebucht_auf'] = mysqlDateFromLocal($entry['buchung']['umgebucht_auf']);
			$entry['buchung']['rechnunggestellt'] = mysqlDateFromLocal($entry['buchung']['rechnunggestellt']);
			$entry['buchung']['datum'] = mysqlDateFromLocal($entry['buchung']['datum']);

			// die eigentliche buchung anlegen
			$buchung->merge( $entry['buchung'] );

			$buchung->kursgebuehr == "-" ? $buchung->kursgebuehr = $entry['buchung']['nettoep'] : false;
			if ( $this->saveToDb ) $buchung->save();

			// hotel buchung anlegen
			if ( !empty ($entry["hotel"]['name'])) {
				// FIXME: hotel feldname?
				$hotel = Doctrine::getTable("Hotel")->findOneByName ( $entry['hotel']['name'] );

				if ( !is_object ( $hotel )) {
					$report->add("Hotel nicht vorhanden: Name=" . $entry['hotel']['name']);
					$report->status(false);
					continue;
				}
				
				$hotelBuchung->merge ( $entry ['hotel_buchung'] );
				$hotelBuchung->storno_datum = mysqlDateFromLocal ($entry['buchung']['storno_datum']);
				$hotelBuchung->buchungs_datum = $entry['buchung']['datum'];
			//	$hotelBuchung->storno_datum = mysqlDateFromLocal ($entry['buchung']['storno_datum']);
				$hotelBuchung->buchung_id = $buchung->id;
				$hotelBuchung->hotel_id = $hotel->id;
				$hotelBuchung->import = $importMarker;

				if ( $this->saveToDb ) $hotelBuchung->save();
			}
		}
	}

	function importBoth ( &$report ) {
		$GLOBALS['firephp']->log($_FILES, "importBoth");

		$csv = fopen ($_FILES['csv']['tmp_name'], "r");
		$firstline = true;
		$found = 0;
		$personKeys = array();
		$kontaktKeys = array();
		$i = 1;

		while ( ($data = fgetcsv ($csv, 0, ";")) !== FALSE) {

			if ($firstline) {
				for( $i=0; $i<count($data); $i++ ) {
					if ( $data[$i] == "firma" ) $found = $i;

					if ($found != 0) {
						$kontaktKeys[] = $data[$i];
					} else {
						$personKeys[] = $data[$i];
					}
				}

			} else {
				$pData = array_slice( $data, 0, $found);
				$kData = array_slice( $data, $found);

				$pData = array_combine($personKeys, $pData);
				$kData = array_combine($kontaktKeys, $kData);

				$person = new Person();
				$kontakt = new Kontakt();

				// fix encoding of data
				foreach ($pData as $key=>$data) {
					$pData[$key] = mb_convert_encoding( $data, "UTF-8","ISO-8859-1");
				}

				foreach ($kData as $key=> $data) {
					$kData[$key] = mb_convert_encoding( $data, "UTF-8","ISO-8859-1");
				}

				$report->add("Importiere Person: Vorname=" . $pData['vorname'] . " Name=" . $pData['name']);
				$report->add("Importiere Kontakt: Firma=" . $kData['firma']);

				$person->merge($pData);
				$kontakt->merge($kData);

				$report->status(true);
				if ( $this->saveToDb) $person->save();
				$report->status(true);
				if ( $this->saveToDb)  $kontakt->save();
			}

			$firstline = false;
		}

		// personen importieren

		// kontakte importieren
		fclose($csv);
	}

	function importTermine (&$report) {

		$csv = fopen ($_FILES['csv']['tmp_name'], "r");
		$data = $this->csvToArray($csv);
		$tStandort = Doctrine::getTable("Standort");
		$tSeminar = Doctrine::getTable("Seminar");

		foreach ( $data as $entry ) {
			$report->add("Importiere KursNr: " . $entry['seminar']['kursnr'] . "&nbsp;&nbsp;&nbsp;Seminar:" . $entry['seminar']['seminar_art_id'] . "&nbsp;&nbsp;&nbsp;Standort: ". $entry['standort']['name']);

			$seminare = $tSeminar->findByKursnr($entry['seminar']['kursnr']);
			if ( $seminare->count() > 0 ) {
				$report->status(false);
				continue;
			}

			$standort = $tStandort->findByName($entry['standort']['name']);

			if ( $standort->count() > 0 ) {
				$standort = $standort[0];

				$entry['seminar']['datum_begin'] = mysqlDateFromLocal($entry['seminar']['datum_begin']);
				$entry['seminar']['datum_ende'] = mysqlDateFromLocal($entry['seminar']['datum_ende']);
				$entry['seminar']['storno_datum'] = mysqlDateFromLocal($entry['seminar']['storno_datum']);

				$entry['seminar']['standort_id'] = $standort->id;
			
				if ( ! array_key_exists ( "kursgebuehr", $entry['seminar'])) $entry['seminar']['kursgebuehr'] = $entry['seminar']['nettoep'];

				//$GLOBALS['firephp']->log($entry, "importTermine");
				$nTermin = new Seminar();
				$nTermin->merge($entry['seminar']);
				if ( $this->saveToDb)  $nTermin->save();
				$report->status(true);
			} else {
				$report->status(false);
			}
		}
		
		fclose($csv);
	}

	function importKontakte (&$report) {
		$GLOBALS['firephp']->log($_FILES, "importKontakte");

		$csv = fopen ($_FILES['csv']['tmp_name'], "r");
		$firstline = true;
		$found = 0;
		$kontaktKeys = array();
		$i = 1;
		$table = Doctrine::getTable("Kontakt");

		while ( ($data = fgetcsv ($csv, 0, ";")) !== FALSE) {
			if ($firstline) {
				for( $i=0; $i<count($data); $i++ ) {
					$kontaktKeys[] = $data[$i];
				}
			} else {
				$kData = array_combine($kontaktKeys, $data);
				foreach ($kData as $key => $data) {
					$kData[$key] = mb_convert_encoding( $data, "UTF-8","ISO-8859-1");
				}

				if ( $table->findByFirma($kData['firma'])->count() == 0 && !empty ($kData['firma']) ) {
					$kontakt = new Kontakt();

					// fix encoding of data
					$report->add("Importiere Kontakt: Firma=" . $kData['firma']);

					$kontakt->merge($kData);
					$report->status(true);
					if ( $this->saveToDb)  $kontakt->save();
				} else if ( empty ( $kData['firma']) ){
					$report->add("Feld Firma leer");
					$report->status(false);
				} else {
					$report->add("Kontakt schon vorhanden: Firma=" . $kData['firma']);
					$report->status(false);
				}
			}

			$firstline = false;
		}

		// personen importieren

		// kontakte importieren
		fclose($csv);
	}

	function importPersonen (&$report) {
		$GLOBALS['firephp']->log($_FILES, "importPersonen");

		$csv = fopen ($_FILES['csv']['tmp_name'], "r");
		$firstline = true;
		$found = 0;
		$personKeys = array();
		$i = 1;
		$table = Doctrine::getTable("Kontakt");
		$personTable = Doctrine::getTable("Person");

		$detectSex = isset ( $_POST['detectSex']);
		$splitNames = isset ( $_POST['splitNames']);
		$detectDegree = isset ( $_POST['detectDegree']);
		$degrees = array( "Dipl.-Ing.", "Dipl.Ing.", "Dipl-Ing.", "Dr.", "Inh.");
		$ansprechpartner = isset($_POST['ansprechpartner']);

		while ( ($data = fgetcsv ($csv, 0, ";")) !== FALSE) {
			if ($firstline) {
				for( $i=0; $i<count($data); $i++ ) {
					$personKeys[] = $data[$i];
				}
			} else {
				$kData = array();
				$kData = array_combine($personKeys, $data);

				foreach ($kData as $key => $data) {
					$kData[$key] = mb_convert_encoding( $data, "UTF-8","ISO-8859-1" );
				}

				if ( empty ($kData['firma']) ) {
					$report->add( "Feld Firma ist leer" );
					$report->status(false);


				} else {

					if ( $ansprechpartner ) {
						$kData['ansprechpartner'] = "1";
					}

					if ( $detectSex ) {
						if ( strpos($kData['name'], "Herr") == -1 ) {
							$kData['geschlecht'] = 1;
						}

						$kData['name'] = trim(str_replace("Herr","",$kData['name']));
						$kData['name'] = trim(str_replace("Frau","",$kData['name']));
					}


					if ( $detectDegree) {
						foreach ( $degrees as $degree) {
							if ( strpos($kData['name'], $degree) !== FALSE ) {
								$kData['grad'] = $degree;
								$kData['name'] = trim(str_replace ( $degree, "", $kData['name']));
							}
						}
					}

					if ( $splitNames ) {
						$aName = explode(" ", $kData['name']);
						if ( strpos ( $kData['name'], "," ) !== FALSE) {
							$kData['name'] = str_replace ( ",","",array_shift($aName));
							$kData['vorname'] = str_replace ( ",","",implode(" ", $aName));
						} else {
							$kData['name'] = array_pop($aName);
							$kData['vorname'] = implode(" ", $aName);
						}
					}

					if ( $personTable->findByDql( "name = ? AND vorname = ? AND Kontakt.firma = ?", array($kData['name'], $kData['vorname'],$kData['firma']))->count() > 0 ) {
						$report->add("Person schon vorhanden:<br/> Firma=" . $kData['firma'] . "<br/>Name: " . $kData['name'] . " Vorname: ". $kData['vorname']);
						$report->status(false);
					} else {
						$person = new Person();
						$kontakt = null;

						if ( $table->findByFirma($kData['firma'])->count() >= 1  ) {
							$kontakt = $table->findByFirma($kData['firma']);
							$kontakt = $kontakt[0];
							$report->add("Importiere Person:<br/> Firma=" . $kData['firma'] . "<br/>Name: " . $kData['name'] . " Vorname: ". $kData['vorname']);
						} else {
							$kontakt = new Kontakt();
							$kontakt->firma = $kData['firma'];
							if ( $this->saveToDb)  $kontakt->save();
							$report->add("Importiere Person:<br/> Firma(neu)=" . $kData['firma'] . "<br/>Name: " . $kData['name'] . " Vorname: ". $kData['vorname']);
						}

						// fix encoding of data

						$kData['kontakt_id'] = $kontakt->id;

						$person->merge( $kData );
						$report->status( true );
						if ( $this->saveToDb)  $person->save();
					}
				}
			}

			$firstline = false;
		}

		// personen importieren

		// kontakte importieren
		fclose($csv);

	}

	function onUpload(&$pr, &$ds) {
		$line_endings = ini_get("auto_detect_line_endings");
		ini_set('auto_detect_line_endings',TRUE);

		if ( ! is_uploaded_file($_FILES['csv']['tmp_name']) ) {
			$GLOBALS['firephp']->log($_FILES, "Error reading uploaded file");
			$ds->add("text", "Fehler beim Upload.");
			$pr->loadPage("db/upload.xml");
			return;
		}

		$report = new Mosaik_Report();
		$mode = $_POST['import'];
		$this->saveToDb = ! isset ( $_POST['nichtSpeichern']  );

		switch ( $mode ) {
			case "beides":
				$this->importBoth($report);
				break;
			case "kontakte":
				$this->importKontakte($report);
				break;
			case "personen":
				$this->importPersonen($report);
				break;
			case "buchungen":
				$this->importBuchungen($report);
				break;
			case "termine":
				$this->importTermine($report);
				break;
		}


		$ds->add("text", $report->output());
		$pr->loadPage("db/upload.xml");
		ini_set('auto_detect_line_endings',$line_endings);
	}

	function onPanel($pr, $ds) {
		$pr->loadPage("db.xml");
	}
}

?>