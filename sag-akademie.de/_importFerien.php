<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 * 
 * importiert eine csv datei in die tabelle feiertag
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

$sourceFile = "resources/import-ferien/2014.csv";


$firstline = true;
if (($handle = fopen($sourceFile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
    	if ( $firstline ) {
    		$firstline = false;
			continue;
    	}
  		$start = $data[0];
		$ende = $data[1];
		$text = $data[2];
		
		if ( $start == $ende ) {
			// eintragen
			$fromArr = explode(".", $start);
			$from = mktime (1,0,0, $fromArr[1], $fromArr[0], $fromArr[2]);
			$uid = md5(microtime());
			
			echo date("d.m.Y", $from) . " : ".  $text . " uid: " .$uid."\n";
			
			$f = new Feiertag();
			$f->name = $text;
			$f->uid = $uid;
			$f->datum = date("Y-m-d", $from);
			$f->save();
		} else {
			$uid = md5(microtime());
			
			$fromArr = explode(".", $start);
			$from = mktime (1,0,0, $fromArr[1], $fromArr[0], $fromArr[2]);
			
			$toArr = explode(".", $ende);
			$to = mktime (1,0,0, $toArr[1], $toArr[0], $toArr[2]);
			
			while ( $from <= $to) {
				echo date("d.m.Y", $from) . " : ".  $text . " uid: " .$uid."\n";
				
				$f = new Feiertag();
				$f->name = $text;
				$f->uid = $uid;
				$f->datum = date("Y-m-d", $from);
				$f->save();
				$from += 86400;
			}
			
			
			
		}
    }
    fclose($handle);
}
