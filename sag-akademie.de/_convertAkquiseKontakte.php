<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . "/lib/PEAR" . PATH_SEPARATOR . dirname(__FILE__) . "/lib/controller" . PATH_SEPARATOR . dirname(__FILE__) . "/lib" . PATH_SEPARATOR . dirname(__FILE__) . "/lib/templates" . PATH_SEPARATOR . dirname(__FILE__) . "/lib/konstrukt" . PATH_SEPARATOR . dirname(__FILE__) . "/lib/db/models");
include_once ("Zend/Loader/Autoloader.php");
include ("lib/config.php");
include ("lib/std.inc.php");

$blockCount = 100;
$q = Doctrine_Query::create()->from("AkquiseKontakt")->where("angelegt_datum > ? ", "2012-01-01");

$count = $q->count();

echo "\nLade AkquiseKontakte -> Gesamt: " . $count . "\n\n";

$steps = ceil($count / $blockCount);
$start = 0;
$now = currentMysqlDatetime();

echo "Importiere " . $q->count() . " Datensätze";

for ($i = 0; $i < $steps; $i++) {
	$start = $i * $blockCount;
	$end = $i * $blockCount + $blockCount;
	$memoryUsage = memory_get_usage() / (1024 * 1024);
	echo "\nDatensätze von {$start} bis {$end}\nMemory Usage: ${memoryUsage}\n\n";
	$q = $q->limit($blockCount)->offset($start);
	$aKontakte = $q->execute();

	$j = 0;
	foreach ($aKontakte as $aKontakt) {
		$j++;
		$current = $start + $j;
		$kontakt = new Kontakt();

		$kontakt->merge(mergeFilter("Kontakt", $aKontakt->toArray()));
		$kontakt->akquise_kontakt_id = $aKontakt->id;
		$kontakt->firma = trim($kontakt->firma);
		if (empty($kontakt->firma)) {
			$kontakt->firma = "E-Mail: {$aKontakt->email}";
		}
		$kontakt->alias = $kontakt->firma;
		$kontakt->id = 0;
		$kontakt->kontext = "Akquise";
		$kontakt->geaendert_von = -1;
		$kontakt->geaendert = $now;
		$kontakt->newsletter_abmeldedatum = $aKontakt->abmelde_datum;
		$kontakt->newsletter_anmeldedatum = $aKontakt->anmelde_datum;
		//print_r($kontakt->toArray());
		$kontakt->save();
		$kontakt->refresh();

		echo "($current / $count) Kontakt erstellt: {$kontakt->firma} id: {$kontakt->id}\n";
		$name = trim($aKontakt->name);
		$vorname = trim($aKontakt->vorname);
		if (!empty($name) || !empty($vorname)) {
			$ap = new Person();
			$ap->merge(mergeFilter("Person", $aKontakt->toArray()));
			$ap->id = 0;
			$ap->kontakt_id = $kontakt->id;
			$ap->bundesland = "";
			$ap->bundesland_id = -1;
			$ap->name = $aKontakt->name;
			$ap->vorname = $aKontakt->vorname;
			$ap->email = $aKontakt->ansprechpartner_email;
			$ap->tel = $aKontakt->tel_durchwahl;

			/** Adressdaten zuruecksetzen **/
			$ap->strasse = "";
			$ap->nr = "";
			$ap->plz = 0;
			$ap->ort = "";
			$ap->ansprechpartner = 1;
			$ap->geaendert_von = -1;
			$ap->geaendert = $now;

			//print_r($ap->toArray());
			$ap->save();

			echo "Person erstellt id: {$ap->name}\n";
			$ap->free(true);
			unset($ap);
		}
		$aKontakt->free(true);
		unset($aKontakt);
		$kontakt->free(true);
		unset($kontakt);
	}

}
?>