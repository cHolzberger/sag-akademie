<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

ini_set('include_path',
	ini_get('include_path')
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/PEAR"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/controller"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/templates"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/konstrukt"
	. PATH_SEPARATOR . dirname(__FILE__) . "/lib/db/models"
);
include_once("Zend/Loader/Autoloader.php");
include ("lib/config.php");
include ("lib/std.inc.php");

$sourceFile = "resources/import-12-2011/00_nach_import.csv";

$fields = array(
	"lfdNr1",
	"lfdNr2",
	"group_nr",
	"id",
	"neueID",
	"niederlassung",
	"doublette",
	"doublette_berechnung",
	"privat",
	"kontext",
	"xx2",
	"xx3",
	"xx32",
	"xx4",
	"firma",
	"zusatz",
	"xx121",
	"alias",
	"kontaktkategorie",
	"branche_id",
	"taetigkeitsbereich_id",
	"doublette",
	"doublette_berechnung",
	"strasse",
	"plz",
	"ort",
	"ansprechpartner_geschlecht",
	"ansprechpartner_grad",
	"ansprechpartner_vorname",
	"ansprechpartner_name",
	"ansprechpartner_funktion",
	"ansprechpartner_geschaeftsfuehrer",
	"bundesland_id",
	"land_id",
	"ansprechpartner_email",
	"xx5",
	"xx6",
	"email",
	"email2",
	"url",
	"tel",
	"fax",
	"vdrk_mitglied",
	"vdrk_mitglied_nr",
	"kundenstatus",
	"kontakt_quelle",
	"kontakt_quelle_stand",
	"newsletter",
	"kundennr",
	"blz",
	"kto",
	"bank",
	"zahlart",
	"bemerkung",
	"notiz",
	"mobil",
	"newsletter_abmeldedatum",
	"newsletter_anmeldedatum",
	"geaendert",
	"wiedervorlage",
	"dwa_mitglied",
	"dwa_mitglied_nr",
	"rsv_mitglied",
	"rsv_mitglied_nr",
	"regierungsbezirk",
	"kreis",
	"angelegt_datum",
	"bereits_in_verteiler",
	"vergleich",
	"umkreis",
	"qualifiziert",
	"qualifiziert_datum",
	"qualifiziert_notiz",
	"deleted_at",
	"import_ssm",
	"ansprechpartner_kontakt_id",
	"ansprechpartner_titel",
	"ansprechpartner_anrede",
	"ansprechpartner_strasse",
	"ansprechpartner_plz",
	"ansprechpartner_geburtstag",
	"ansprechpartner_tel",
	"ansprechpartner_fax",
	"ansprechpartner_mobil",
	"ansprechpartner_abteilung",
	"ansprechpartner_geschaeftsfuehrer",
	"ansprechpartner_newsletter",
	"ansprechpartner_ort",
	"ansprechpartner_bundesland_id",
	"ansprechpartner_land_id",
	"ansprechpartner_notiz",
	"ansprechpartner_newsletter_anmeldedatum",
	"ansprechpartner_newsletter_abmeldedatum",
	"ansprechpartner_login_name",
	"ansprechpartner_login_password",
	"ansprechpartner_aktiv",
	"ansprechpartner_gesperrt",
	"ansprechpartner_geaendert",
	"ansprechpartner_wiedervorlage",
	"ansprechpartner_ausgeschieden",
	"ansprechpartner_zusatz",
	"xx7",
	"xx8",
	"xx9",
	"xx10",
	"xx11"
);
$numFields = count($fields);
$row = 1;

function createPerson($data,$ap=1) {
	global $row;
	$person = new Person();
	$data['angelegt_user_id'] = -1;
	$person->merge(mergeFilter("Person",$data));
	
	$person->ansprechpartner=$ap;
	$person->save();
	print "[$row] ===> Creating Person: {$person->name}\n";
}

function updatePerson($data) {
	global $row;
	$person = Doctrine::getTable("Person")->findByDql("kontakt_id = ? AND ansprechpartner=? AND name=?", array(
		$data['kontakt_id'],
		1,
		$data['name']
	))->getFirst();
	
	if ( !is_object($person)) {
		createPerson($data);
		return;
	}
	print "[$row] ===> Updating Person: {$person->name}\n";
	$data['geaendert'] = currentMysqlDate();
	$data['geaendert_von'] = -1;
	$person->merge(mergeFilter("Person", $data));
	
	$person->save();
	
}

function createKontakt($data) {
	$q = Doctrine::getTable("Kontakt")->findByDql("email=?", array(
	$data['email']));
	$k = $q->getFirst();
	$ap = 1;
	if ($q->count() == 0) {	
	
		$k = new Kontakt();
		$data['angelegt_user_id'] = -1;
	
		$k->merge(mergeFilter("Kontakt",$data));
		$k->newsletter=1;
		$k->newsletter_anmeldedatum = currentMysqlDate();
		$k->kontakt_quelle_stand = currentMysqlDate();
		$k->save();
		$k->refresh();
	} else {
		echo "Reusing... {$k->id}";
		$ap =0;
		return;
	}
	
	$data['ansprechpartner_kontakt_id'] = $k->id;
	if ( !empty ( $data['ansprechpartner_name'])) {
			createPerson(extractPersonData($data), $ap);
	}
}

function updateKontakt($data) {
	if ( !empty ( $data['ansprechpartner_name'])) {
		$data['ansprechpartner_kontakt_id'] = $data['id'];	
		updatePerson(extractPersonData($data));
	}
	$data['geaendert'] = currentMysqlDate();
	$data['geaendert_von'] = -1;
	
	if ( $data['neueID']) {
		$q = Doctrine_Query::create();
		$q->update("Person");
		$q->set("kontakt_id","?",$data['neueID']);
		$q->set("ansprechpartner","?",0);
		$q->where("kontakt_id = ?" , $data['id']);
		$q->execute();
		
		print("deleting...");
		$q = Doctrine_Query::create();
		$q->delete("Kontakt");
		$q->where("id = ?" , $data['id']);
		$q->execute();
	} else {
		$k = Doctrine::getTable("Kontakt")->find( $data['id'] );
		if ( !is_object($k) ) {
			$k = new Kontakt();
		}
			$k->merge(mergeFilter("Kontakt", $data));
			$k->save();
			
	}
}

function deleteKontakt($data) {
		$q = Doctrine_Query::create();
		$q->delete("Person");
		$q->where("kontakt_id = ?" , $data['id']);
		$q->execute();
		
		$q = Doctrine_Query::create();
		$q->delete("Kontakt");
		$q->where("id = ?" , $data['id']);
		$q->execute();
}

function extractPersonData($data) {
	global $fields;
	$newdata = array();
	foreach ( $fields as $key) {
		if ( substr_compare($key,"ansprechpartner_", 0,strlen("ansprechpartner_")) == 0) {
			$newKey = str_replace("ansprechpartner_", "", $key);
			if ( !empty ( $data[$key])) {
				$newdata[$newKey] = $data[$key];
			} 
		}
	}
	return $newdata;
}

if (($handle = fopen($sourceFile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
    	$newdata = array();
        $num = count($data);

        $row++;
		if ( $row <= 3) {continue;}
		if ( $row %1000 == 0) {
			$memoryUsage = memory_get_usage() / (1024 * 1024);
			
			echo "\nMemory Usage: ${memoryUsage}\n\n"; 
		}
		
        for ($c=0; $c < $num; $c++) {
        	if ( $numFields <= $c) break;
        	$newdata[$fields[$c]] = $data[$c];
        }
		
		if ( empty ( $newdata['kontext'] )) $newdata['kontext']="Unbekannt";
		
		if ( $newdata['ansprechpartner_geschlecht'] == "Herr" ) {
			$newdata['ansprechpartner_geschlecht'] = 0;
		} else {
			$newdata['ansprechpartner_geschlecht'] = 1;
		}
		$newdata['strasse'] = preg_replace("/([a-z])Str/","$1 Str", $newdata['strasse']);
		$newdata['ansprechpartner_strasse'] = preg_replace("/([a-z])Str/","\\0 Str", $newdata['ansprechpartner_strasse']);
		if ( strlen($newdata['plz']) == 4) {
			$newdata['plz'] = "0" . $newdata['plz'];
		}
		
		if ( strlen($newdata['group_nr']) > 1 && $newdata['group_nr'][0] == "l" && (empty ( $newdata['id'] ) || $newdata['id'] == "MN")) {
			// nicht vorhandene zum löschen markierte nicht importieren
			print("[{$row}] Ignoring {$newdata['kontext']} {$newdata['firma']} Strasse: {$newdata['strasse']}\n");
			
		} else if ( strlen($newdata['group_nr']) > 1 && $newdata['group_nr'][0] == "l") {
        	deleteKontakt($newdata);
        	print("[{$row}] Delete {$newdata['kontext']} {$newdata['firma']} Strasse: {$newdata['strasse']}\n");
		} else if ( empty ( $newdata['id'] ) || $newdata['id'] == "MN") {
        	print("[{$row}] Create {$newdata['kontext']} {$newdata['firma']} Strasse: {$newdata['strasse']}\n");
			$newdata['id'] = 0;
        	createKontakt($newdata); 
        } else {
        	print("[{$row}] Update {$newdata['kontext']} {$newdata['firma']} Strasse: {$newdata['strasse']}\n");
        	updateKontakt($newdata);
        }
    }
    fclose($handle);
}
