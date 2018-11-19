<?php

include_once ("lib/Mosaik/Config.php");
//globale settings
define("SAG_PRIVAT_KONTAKT", 1);

if (!defined("WEBROOT")) {
	define("WEBROOT", dirname(dirname(__FILE__)));
}
define("LOGDIR", WEBROOT . "/resources/log/");
define("SMTP_SENDER", "buchungen@sag-akademie.de");
define("SMTP_ADMIN_SENDER", "buchungen@sag-akademie.de");
define("SMTP_ADMIN_RECIVER", "buchungen@sag-akademie.de");
define("CONTROLLER_BASEPATH", "lib/controller/");
define("ENGINE_PAGE", "index.php");
define("ELEMENT_PATH", WEBROOT . "/resources/taglib/html/");
define("ELEMENT_BASE_PATH", WEBROOT . "/resources/taglib/");
// html2 pdf
define("HTML2PS_DIR", WEBROOT . "/lib/html2pdf/");
// defaults
// ALT!
MosaikConfig::setVar("dbUser", "root");
MosaikConfig::setVar("dbPassword", "root");
MosaikConfig::setVar("dbHost", "localhost");
MosaikConfig::setVar("dbDatabase", "sagakademie_stable_201004");
$host = file_get_contents(WEBROOT."/env");

// NEU
MosaikConfig::setVar("dbPoolPath", realpath(WEBROOT . "/../dbpool"));

MosaikConfig::setVar("srvImagePath", WEBROOT . "/img/");
MosaikConfig::setVar("webImagePath", "/img/");
MosaikConfig::setVar("webCachePath", "/resources/cache/");
MosaikConfig::setVar("srvCachePath", WEBROOT . "/resources/cache/");
MosaikConfig::setVar("qlog/file", LOGDIR . "/qlog.log");
MosaikConfig::setVar("qlog/errorfile", LOGDIR . "/qerror.log");


//MosaikConfig::setVar("enableApc", function_exists("apc_add"));
MosaikConfig::setVar("enableApc", false);

MosaikConfig::setVar("srvPagePath", WEBROOT . "/templates/pages/");
MosaikConfig::setVar("srvEmailPath", WEBROOT . "/templates/emails/");
MosaikConfig::setVar("htmlPurifyerCache", WEBROOT . "/resources/cache/htmlpurify");

MosaikConfig::setVar("digestRealm", "Bitte melden Sie sich mit Ihrem Benutzernamen und Ihrem Password am System an.");
MosaikConfig::setVar("version", trim(file_get_contents(WEBROOT . "/version.txt")));

MosaikConfig::setVar("dbversion", "?");
MosaikConfig::setVar("dbUseMemcache", false);

MosaikConfig::setVar("debugConfig", getenv("PHP_MCM_DEBUG_CONFIG"));

/** Konfigration des Notification Frameworks * */
MosaikConfig::setVar('sendNotifications', false);
MosaikConfig::setVar("smtpPort", 25);

//lets set the loclale
setlocale(LC_ALL, "de_DE.utf8");

error_reporting(E_ERROR | E_WARNING | E_PARSE & ~E_NOTICE & ~E_DEPRECATED);
//error_reporting(E_ALL);

switch ($host) {
	case "dev":
		define("RPC_PROTO", "http");
		define('APPLICATION_ENV',"dev");
		define("SMTP_SERVER", "smtp.worldserver.net");
		MosaikConfig::setVar("smtpUser", "www@sag-akademie.de");
		MosaikConfig::setVar("smtpPassword", "Akademie_SAG-Akademie");

		MosaikConfig::setVar("dbUser", "dev");
		MosaikConfig::setVar("dbPassword", "54g-4k4d3m13");
		MosaikConfig::setVar("dbHost", "mysqldb");
		MosaikConfig::setVar("dbDatabase", "sagakademie");
		// memcache fuer doctrine verwenden
		MosaikConfig::setVar("dbUseMemcache", true);
		MosaikConfig::setVar("dbMemcacheServer", "memcache");
		MosaikConfig::setVar("dbMemcachePort", "11211");

		MosaikConfig::setVar("overrideEmail", "ch@mosaiksoftware.de");
		MosaikConfig::setVar("firephp/authkey", "58CE51FA2704E7295B0A6B2EDFE01925");

		MosaikConfig::setDebug("firephp");
		MosaikConfig::setDebug("firephp-c");
		MosaikConfig::setDebug("sitescript");

		MosaikConfig::setDebug("firephp-info");

		MosaikConfig::setDebug("firephp-error");
		//MosaikConfig::setDebug("konstrukt");
		MosaikConfig::setDebug("sitecss");
		MosaikConfig::setDebug("sitescript");

		//MosaikConfig::setDebug("doctrine");
		//MosaikConfig::setDebug("doctrine-profile");
		MosaikConfig::setVar("hostname", "sag-akademie.localhost");
		MosaikConfig::setVar("enableApc", false);
		MosaikConfig::setVar('sendNotifications', true);

		error_reporting(E_ALL & ~E_DEPRECATED);
//		define('APPLICATION_ENV', getenv("PHP_MCM_DEBUG_CONFIG"));
		break;
	
	case "beta":
		define("RPC_PROTO", "https");
		define('APPLICATION_ENV', "beta");
		MosaikConfig::setVar("debugConfig", "Beta-Server");
		define("SMTP_SERVER", "smtp.worldserver.net");
		MosaikConfig::setVar("smtpUser", "www@sag-akademie.de");
		MosaikConfig::setVar("smtpPassword", "Akademie-www");
		MosaikConfig::setVar("overrideEmail", "info@samirschwenker.de");
		//MosaikConfig::setVar("overrideEmail", "ch@mosaik-software.de");
		//MosaikConfig::setVar("overrideEmail", "info@sag-akademie.de");
		//MosaikConfig::setDebug("");
		MosaikConfig::setDebug("doktrine");
		MosaikConfig::setVar("enableApc", false);
		MosaikConfig::setVar("enableMemcache", true);

		//MosaikConfig::setDebug("konstrukt");
		MosaikConfig::setDebug("firephp");
		MosaikConfig::setDebug("firephp-c");
		MosaikConfig::setDebug("firephp-info");
		MosaikConfig::setDebug("firephp-error");
		//define("SMTP_SERVER", "mosaik-software.de");

		MosaikConfig::setVar("dbUser", "sagakademie");
		MosaikConfig::setVar("dbPassword", "54g-4k4d3m13");
		MosaikConfig::setVar("dbHost", "mysqldb");
		MosaikConfig::setVar("dbDatabase", "sagakademie_stable");
		MosaikConfig::setVar("hostname", "beta.mosaik-software.de");
		MosaikConfig::setVar('sendNotifications', true);
		// memcache fuer doctrine verwenden
		MosaikConfig::setVar("dbUseMemcache", true);
		MosaikConfig::setVar("dbMemcacheServer", "memcache");
		MosaikConfig::setVar("dbMemcachePort", "11211");
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
		break;
	case "prod":
		define("RPC_PROTO", "https");
		define('APPLICATION_ENV', "prod");
		//MosaikConfig::setVar("overrideEmail", "info@samirschwenker.de");
		define("SMTP_SERVER", "smtp.worldserver.net");
		MosaikConfig::setVar("smtpUser", "www@sag-akademie.de");
		MosaikConfig::setVar("smtpPassword", "Akademie_SAG-Akademie");
		MosaikConfig::setVar("dbUser", "sagakademie");
		MosaikConfig::setVar("dbPassword", "54g-4k4d3m13");
		MosaikConfig::setVar("dbHost", "mysqldb");
		MosaikConfig::setVar("dbDatabase", "sagakademie_stable");
		MosaikConfig::setVar("hostname", "www.sag-akademie.de");
		MosaikConfig::setVar("enableApc", false);
		MosaikConfig::setVar('sendNotifications', true);
// memcache fuer doctrine verwenden
		MosaikConfig::setVar("dbUseMemcache", true);
		MosaikConfig::setVar("dbMemcacheServer", "memcache");
		MosaikConfig::setVar("dbMemcachePort", "11211");
		define("UTF8_HACK", true);
		MosaikConfig::setDebug("firephp");
		#MosaikConfig::setDebug("firephp-c");
		MosaikConfig::setDebug("firephp-info");
		MosaikConfig::setDebug("firephp-error");
		#MosaikConfig::setDebug("doctrine");
		error_reporting(E_ERROR | E_WARNING | E_PARSE & ~E_NOTICE & ~E_DEPRECATED);
		break;
}

if ( ! defined ("UTF8_HACK")) {
	define("UTF8_HACK", false);
}


// CONSTANTS
define("STATUS_AUSGEBUCHT", 4);
define("STATUS_STORNO", 5);
define("STATUS_FREIGEGEBEN", 2);



MosaikConfig::setVar("webUrl", "http://" . MosaikConfig::getVar("hostname") . "/");
//MosaikConfig::getVar("dbUser") . ":" . MosaikConfig::getVar("dbPassword")
$dburl = "mysql:dbname=" . MosaikConfig::getVar("dbDatabase") . ";host=" . MosaikConfig::getVar("dbHost");

MosaikConfig::setVar("dburl", $dburl);
