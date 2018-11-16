<?php
include_once("lib/phpElements/dbtable.php");
include_once("lib/phpElements/dbloop.php");
include_once("lib/phpElements/dbselect.php");
include_once("lib/phpElements/foreach.php");
include_once("lib/phpElements/sitescript.php");
include_once("lib/phpElements/sitecss.php");
include_once("lib/phpElements/switch.php");
include_once("lib/phpElements/xdbselect.php");

$elements->add( new DbTable($config, "dbtable", ""));
$elements->add( new DbLoop($config, "dbloop", ""));
$elements->add( new DbSelect($config, "dbselect", ""));
$elements->add( new MForEach($config, "foreach", ""));
$elements->add( new SiteScript($config, "sitescript", ""));
$elements->add( new SiteCSS($config, "sitecss", ""));
$elements->add( new MSwitch($config, "switch", ""));
$elements->add( new MCase($config, "case", ""));
$elements->add( new XDbSelect($config, "xdbselect", ""));

?>