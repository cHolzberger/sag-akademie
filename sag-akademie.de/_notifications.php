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

/**
 * Nachrichtenfilter fuer das SAG-Admin-Interface
 * der Filter arbeitet eng mit den Views aus
 * resources/Doctrine/mysql/notification.sql zusammen
 * er filtert bestehende datensaetze und verschickt aufgrund verschiedener kriterien mails
 * die letzte versandte mail zu einem datensatz wird in einer extra tabelle gespeichert:
 * tabelle: gesendete_benachrichtigung
 * id : src_table : datum
 * id ist die eindeutige id des datensatzes zu dem die nachricht versickt wurde
 * src_table ist die quelltabelle und bildet zusammen mit id den primaerschluessel
 * datum ist das datum an dem die nachricht versandt wurde
 * aus dieser view und einer kreuzabfrage mit der betroffenen tabelle laesst sich herausfinden an
 * welche datensaetze noch nicht benachrichtigt sind
 * der php code aktualisiert nur das datum der benachrichtigung und macht eintrage in die tabelle
 * gesendete_benachrichtigungen
 * die logik welche datensaetze eine benachrichtigung erhalten sollen wird in SQL erstellt und als view gespeichert
 * */
print "<html>";
print "<head></head>";
print "<body>";
print "<b>SAG-Akademie Nachrichtenversand </b><br/>";
print "Datum:" . date("d.m.Y") . "<br/>";
print "Uhrzeit: " . date("h:m") . "<br/><br/> <hr/>";
/* * *
 * Erzeugen der Notification Queue
 * und anschliessendes versenden der Nachrichten
 * Die Konfiguration alle erzeugten Objekte erfolgt durch
 * das $checkQ objekt
 */
$checkQ = new NotificationQueue();

$checkQ->setConfig("sendMail", MosaikConfig::getVar('sendNotifications'));
print "<b>Aktivierter Filter:</b><br/><br/>";
$checkQ->addCheck("GeburtstagsCheck", array(
    "subject" => MosaikConfig::getPersistent("geburtstagsCheck", "subject", "Herzlichen Glückwunsch zum Geburtstag")));

/*
$checkQ->addCheck("TeilnehmerNichtErreichtCheck", array(
    "ago" => MosaikConfig::getPersistent("teilnehmerNichtErreicht", "check1", '2'),
    "template" => "notification_teilnehmerzahl1.xml",
    "subject" => MosaikConfig::getPersistent("teilnehmerCheck", "subject1", "Minimale Teilnehmerzahl nicht erreicht.")));

$checkQ->addCheck("TeilnehmerNichtErreichtCheck", array(
    "ago" => MosaikConfig::getPersistent("teilnehmerNichtErreicht", "check2", '7'),
    "template" => "notification_teilnehmerzahl2.xml",
    "subject" => MosaikConfig::getPersistent("teilnehmerCheck", "subject2", "Minimale Teilnehmerzahl nicht erreicht.")));

$checkQ->addCheck("TeilnehmerNichtErreichtCheck", array(
    "ago" => MosaikConfig::getPersistent("teilnehmerNichtErreicht", "check3", '14'),
    "template" => "notification_teilnehmerzahl3.xml",
    "subject" => MosaikConfig::getPersistent("teilnehmerCheck", "subject3", "Minimale Teilnehmerzahl nicht erreicht.")));
*/
//$checkQ->addCheck("SeminareOhneReferent", array());
echo "<hr/>";

if (isset($_GET['test'])) {
    $checkQ->runTest($_GET['test']);
} else {
    $checkQ->run();
}

print "</body></html>";
$content = ob_get_contents();
ob_end_clean();
print $content;
?>
