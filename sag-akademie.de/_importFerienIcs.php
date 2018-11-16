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
include ("_lib/ICalParser.php");

$sourceDir = "resources/import-ferien/";
$d=dir($sourceDir);
while (false !== ($sourceFile = $d->read())) {
if ( substr($sourceFile, strlen($sourceFile)-3,3) != "ics" ) continue;
    echo "Importing: $sourceFile";
    $parser = new ICalParser(new SplFileInfo($sourceDir.$sourceFile));
    foreach ($parser->getEvents() as $data) {
   		$start = sprintf("%s.%s.%s", substr($data->DTSTART,6,2), substr($data->DTSTART,4,2),substr($data->DTSTART,0,4));
        $ende = sprintf("%s.%s.%s", substr($data->DTEND,6,2), substr($data->DTEND,4,2),substr($data->DTEND,0,4));

        $text = str_replace("2015", "", $data->SUMMARY);
        $text = str_replace("Nordrhein-Westfalen","NRW",$text);
        $text =    str_replace("Bayern","B",$text);
        $text =   str_replace("Schleswig-Holstein","SH",$text);
        $text =   str_replace("Hessen","H",$text);
        $text =   str_replace("Hessen","H",$text);

        $text =   str_replace("Winterferien","WF",$text);
        $text =   str_replace("Weihnachtsferien","WF",$text);

        $text =   str_replace("Osterferien","OF",$text);
        $text =   str_replace("Pfingstferien","PF",$text);
        $text =   str_replace("Sommerferien","SF",$text);
        $text =   str_replace("Herbstferien","HF",$text);

        print_r ($start);
        print_r($ende);
        //print_r ($ende);
        echo $text;

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

                $f = Doctrine::getTable("Feiertag")->findByDql("datum = ?", array(date("Y-m-d", $from)))->getFirst();
                if (is_object($f)) {
                    if ( !substr_count($f->name , $text)) {
                        $f->name = "{$f->name} / $text";
                    }
                } else {
				    $f = new Feiertag();
			    	$f->name = $text;
				    $f->uid = $uid;
			    	$f->datum = date("Y-m-d", $from);
                }
			    $f->save();
				$from += 86400;
			}
			
			
			
		}
    }
}