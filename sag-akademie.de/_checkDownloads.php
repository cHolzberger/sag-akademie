<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 *
 * Scannt das Verzeichnis /download und prüft ob die Dateien noch aus der Datenbank verlinkt sind.
 *
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
 if ( !class_exists("Zend_Loader_Autoloader")) {
	include_once("Zend/Loader/Autoloader.php");
 }
include ("lib/config.php");
include ("lib/std.inc.php");

/* notifications laden */
include ("lib/notifications/NotificationQueue.php");

include ("lib/notifications/NotificationCheck.php");

include ("lib/notifications/GeburtstagsCheck.php");
include ("lib/notifications/TeilnehmerNichtErreichtCheck.php");
include ("lib/notifications/SeminareOhneReferent.php");

ob_start();


print "<html>";
print "<head></head>";
print "<body>";
print "<b>SAG-Akademie prüfen der Dateien in /download </b><br/>";
print "Datum:" . date("d.m.Y") . "<br/>";
print "Uhrzeit: " . date("h:m") . "<br/><br/> <hr/>";

$dirs = array("downloads", "pdf");
while ( null !== ($dirname = array_pop($dirs))) {
	print ("Verzeichnis: $dirname<br/>" );
	$fdir = dir($dirname);

	while ( false !== ($file = $fdir->read())) {
		if ( $file == "." || $file == ".." || $file == "_placeholder") continue;

		if ( Doctrine::getTable("SeminarArt")->hasFile($file)) {
			//echo "In Seminar Art<br/>";
		} else if ( Doctrine::getTable("Download")->hasFile($file)) {
			//echo "In Downloads<br/>";

		} else {
			print("Delete File: $dirname/$file<br/>");
		}
	}
}

print "</body></html>";
$content = ob_get_contents();
ob_end_clean();
print $content;
?>
