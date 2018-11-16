<?php
MosaikDebug::infoDebug("pagereader.php","include_once");
include_once ("lib/mosaikTaglib/mosaikPageReader.class.php");

include_once("Mosaik/Config.php");
include_once("Mosaik/Email.php");
include_once("Mosaik/ObjectStore.php");
include_once("Mosaik/Report.php");
include_once("Mosaik/FileStore.php");
//include_once("Mosaik/Email.php");

// init mosaik object store
$store = Mosaik_ObjectStore::init();

// store setup should not stay here! 
$store->get("/")->add("current", "Mosaik_DummyNode", "Dynamic Node to add Data to");
$store->get("/")->add("persistent", "Mosaik_PersistentNode","APC Persistent Node");
