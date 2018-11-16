<?php
$GLOBALS['path'] = array();
include_once ("services/SystemService.php");
/**
 * Administrative main connector
 *
 * this class routes all kind of request to the appropriate controllers for the administrative components
 *
 * @author Christian Holzberger <ch@mosaik-software.de>
 * @package ADM
 * @license Use not permitted without written license
 *
 */
class ADM_Admin extends SAG_Admin_Component {
	public $map = Array (
		"kontakte" => "ADM_Kontakte",
		"stats" => "ADM_Stats",
		"personen" => "ADM_Personen",
		"seminare" => "ADM_Seminar",
		"termine" => "ADM_Termine",
		"buchungen" => "ADM_Buchungen",
		"standorte" => "ADM_Standort",
		"hotels" => "ADM_Hotels",
		"mailings" => "ADM_DBContent",
		"statistiken" => "ADM_DBContent",
		"rechnungen" => "ADM_Rechnungen",
		"planung" => "ADM_Planung",
		"akquise" => "ADM_Akquise",
		"referenten" => "ADM_Referenten",
		"neuigkeiten" => "ADM_Neuigkeiten",		
		"db" => "ADM_DB",
		"dbsetup" => "ADM_DB_Setup",
		"migrations" => "ADM_DB_Migrations",
		
		"widget" =>"GENERIC_Widget",
		"json" =>"JSON_Json",
		"pdf" => "ADM_Pdf",
		"csv" => "ADM_Csv",
		"settings" => "ADM_XSettings",
		"user" => "ADM_User",
		"todo" => "ADM_XTodo",
		"phpdoc" => "SAG_Staticpage",
		"phpdoc_read" => "SAG_Staticpage",
		"emaillog" => "SAG_Staticpage",
		"dokumente" => "ADM_Dokumente",
		"dokumente_test" => "SAG_Staticpage",
		
		"umkreissuche" => "ADM_Umkreissuche",
		
		"redirectorkontakt" => "ADM_REDIRECTOR_Kontakt",
		);
		
		function __construct() {
			MosaikConfig::setVar("srvImagePath",MosaikConfig::getVar("srvImagePath") . "admin/");
			MosaikConfig::setVar("webImagePath", "/img/admin/");
		}
		
		function map($name) {
			if ( $name == "admin" ) { // quickfix (/admin/admin/admin etc. )
				return "ADM_Admin";
			} else if (array_key_exists($name, $this->map)) {
				return $this->map[$name];
			} else {
				throw new MosaikPageReader_PageNotFound("");
			}
		}
		
		function POST() {
			//print_r($_POST);
			if ( getRequestType( ) == "json") {
				return $this->renderJson();
			} else {
				return $this->renderHtml();
			}
		}
		
		function getBuchungen() {
			$this->identity()->authenticate();
			//$info = Doctrine::getTable("ViewBuchungPreis")->important()->fetchArray();
			//$this->dsDb->add("Buchungen", $info);
			
			$table= Doctrine::getTable("SeminarArtRubrik");
			$info = $table->findAll();
			$info = $info->toArray();
			foreach ( $info as $key => $i ) {
				$info[$key]['linkname'] = str_replace("ü","_u", $info[$key]['name']);
				$info[$key]['linkname'] = str_replace("ä","_a", $info[$key]['name']);
				$info[$key]['linkname'] = str_replace("ö","_o", $info[$key]['name']);
			}
			$this->dsDb->add("SeminarArtRubrik", $info);
		}
		
		function addStatistics (&$ds) {
			$startPM = date ("Y-m-01 00:00:00", mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
			$endPM = date ("Y-m-t 00:00:00", mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
			$startTM = date ("Y-m-01 00:00:00");
			$endTM = date ("Y-m-t 00:00:00");
			
			MosaikDebug::msg($startPM, "Start:");
			MosaikDebug::msg($endPM, "End:");
			
			$countBuchungen = Doctrine::getTable("Buchung")->countByDate($startTM, $endTM);
			$countBuchungenLastMonth = Doctrine::getTable("Buchung")->countByDate($startPM, $endPM);
			
			$countStorno = Doctrine::getTable("Buchung")->countStornoByDate($startTM, $endTM);
			$countStornoLastMonth = Doctrine::getTable("Buchung")->countStornoByDate($startPM, $endPM);
			
			$countUmbuchungen = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startTM, $endTM);
			$countUmbuchungenLastMonth = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startPM, $endPM);
			
			$ds->add ("BuchungenThisMonth", $countBuchungen );
			$ds->add ("BuchungenLastMonth", $countBuchungenLastMonth );
			$ds->add ("StornoThisMonth", $countStorno );
			$ds->add ("StornoLastMonth", $countStornoLastMonth );
			$ds->add ("UmbuchungenLastMonth", $countUmbuchungenLastMonth );
			$ds->add ("UmbuchungenThisMonth", $countUmbuchungen );
			
		}
		
		function renderHtml() {
			$this->identity()->authenticate("admin");
			$this->addBreadcrumb($this->url(), "Admin");
			// we start serving here
			$event = new SystemRequestEvent();
			$event->state = "begin";
			SystemService::dispatchEvent($event);
			
			list($config, $content) = $this->createPageReader();
			$this->dsDb = new MosaikDatasource ( "dbtable" );
			$content->addDatasource ( $this->dsDb );
			$this->dsDb->add ("Benutzer", Mosaik_ObjectStore::getPath("/current/identity")->toArray() );
			
			
			$content->loadPage("startseite.xml");
			$this->dataStore->add ("text", $content->output->get());
			$this->dataStore->add ("path", $GLOBALS['path']);
			
			MosaikDebug::msg(Mosaik_ObjectStore::getPath("/current/identity")->toArray(), "User:");
			
			
			$content->output->clear();
			$content->loadPage("__engine.xml");
			// and we stop serving here
			
			$event = new SystemRequestEvent();
			$event->state = "finish";
			SystemService::dispatchEvent($event);
			
			return $content->output->get($content->variable);
		}
		
		function renderIframe() {
			$this->identity()->authenticate("admin");
			$this->addBreadcrumb($this->url(), "Admin");
			list($config, $content) = $this->createPageReader();
			$this->dsDb = new MosaikDatasource ( "dbtable" );
			$this->dsDb->add ("Benutzer", Mosaik_ObjectStore::getPath("/current/identity")->toArray() );
			$this->addStatistics($this->dsDb);
			$content->addDatasource ( $this->dsDb );
			//$this->getBuchungen();
			//$this->dsDb->log();
			$content->loadPage("startseite.xml");
			$this->dataStore->add ("text", $content->output->get());
			$this->dataStore->add ("path", $GLOBALS['path']);
			
			$content->output->clear();
			$content->loadPage("__iframe.xml");
			return $content->output->get($content->variable);
		}
		
		function renderJson() {
			return '{"status": "ok"}';
		}
		
		function renderWidget() {
			$this->identity()->authenticate("admin");
			$this->addBreadcrumb($this->url(), "Admin");
			
			list($config, $content) = $this->createPageReader();
			$this->dsDb = new MosaikDatasource ( "dbtable" );
			$content->addDatasource ( $this->dsDb );
			$this->getBuchungen();
			//$this->dsDb->log();
			$content->loadPage("startseite.xml");
			$this->dataStore->add ("text", $content->output->get());
			$this->dataStore->add ("path", $GLOBALS['path']);
			
			$content->output->clear();
			$content->loadPage("__widget.xml");
			return $content->output->get($content->variable);
		}
		
		
		
		function renderIframeForward($class_name, $namespace = "") {
			$this->identity()->authenticate("admin");
			$this->addBreadcrumb($this->url(), "Admin");
			
			list($config, $content) = $this->createPageReader();
			
			if (is_array($class_name)) {
				$namespace = $class_name[1];
				$class_name = $class_name[0];
			}
			
			$next = $this->createComponent($class_name, $namespace);
			$content = "";
			
			try {
				$content = $next->dispatch();
				$this->dataStore->add ("text",$content);
				$this->dataStore->add ("version",MosaikConfig::getVar("version"));
				
				$this->dataStore->add ("path",$GLOBALS['path']);
				
				$this->pageReader->loadPage("__iframe.xml");
				return $this->pageReader->output->get();
				//return mb_convert_encoding($this->pageReader->output->get() , "UTF-8","auto");
			} catch (MosaikPageReader_PageNotFound $e) {
				$content = $e->content();
				$this->dataStore->add ("text",$content);
				$this->dataStore->add ("path",$GLOBALS['path']);
				$this->dataStore->add ("version",MosaikConfig::getVar("version"));
				$this->pageReader->loadPage("__iframe.xml");
				throw new k_HttpResponse("404", $this->pageReader->output->get());
				//throw new k_HttpResponse("404", mb_convert_encoding( $this->pageReader->output->get(), "UTF-8","auto"));
			}
		}
		
		function renderWidgetForward($class_name, $namespace = "") {
			$this->identity()->authenticate("admin");
			$this->addBreadcrumb($this->url(), "Admin");
			
			list($config, $content) = $this->createPageReader();
			
			if (is_array($class_name)) {
				$namespace = $class_name[1];
				$class_name = $class_name[0];
			}
			
			$next = $this->createComponent($class_name, $namespace);
			$content = "";
			
			try {
				$content = $next->dispatch();
				$this->dataStore->add ("text",$content);
				$this->dataStore->add ("version",MosaikConfig::getVar("version"));
				$this->dataStore->add ("path",$GLOBALS['path']);
				$this->pageReader->loadPage("__widget.xml");
				
				return $this->pageReader->output->get();
				//return mb_convert_encoding($this->pageReader->output->get() , "UTF-8","auto");
			} catch (MosaikPageReader_PageNotFound $e) {
				$content = $e->content();
				$this->dataStore->add ("text",$content);
				$this->dataStore->add ("path",$GLOBALS['path']);
				$this->dataStore->add ("version",MosaikConfig::getVar("version"));
				$this->pageReader->loadPage("__widget.xml");
				
				throw new k_HttpResponse("404", $this->pageReader->output->get());
			}
		}
		
		function renderJsonForward($class_name, $namespace = "") {
			//$this->identity()->authenticate("admin");
			
			if (is_array($class_name)) {
				$namespace = $class_name[1];
				$class_name = $class_name[0];
			}
			
			$next = $this->createComponent($class_name, $namespace);
			$content = "";
			
			return $next->dispatch();
		}
		
		function renderHtmlForward($class_name, $namespace = "") {
			$this->identity()->authenticate("admin");
			$this->addBreadcrumb($this->url(), "Admin");
			
			list($config, $content) = $this->createPageReader();
			
			if (is_array($class_name)) {
				$namespace = $class_name[1];
				$class_name = $class_name[0];
			}
			
			$next = $this->createComponent($class_name, $namespace);
			$content = "";
			
			try {
				$content = $next->dispatch();
				$this->dataStore->add ("text",$content);
				$this->dataStore->add ("version",MosaikConfig::getVar("version"));
				$this->dataStore->add ("path",$GLOBALS['path']);
				
				$this->pageReader->loadPage("__engine.xml");
				return $this->pageReader->output->get();
				//return mb_convert_encoding($this->pageReader->output->get() , "UTF-8","auto");
			} catch (MosaikPageReader_PageNotFound $e) {
				$content = $e->content();
				$this->dataStore->add ("text",$content);
				$this->dataStore->add ("path",$GLOBALS['path']);
				$this->dataStore->add ("version",MosaikConfig::getVar("version"));
				$this->pageReader->loadPage("__engine.xml");
				throw new k_HttpResponse("404", $this->pageReader->output->get());
			}
		}
		
		function renderPdfForward($class_name, $namespace = "") {
			$this->identity();
			
			if (is_array($class_name)) {
				$namespace = $class_name[1];
				$class_name = $class_name[0];
			}
			
			$next = $this->createComponent($class_name, $namespace);
			
			return $next->dispatch();
		}
		
		function HEAD() {
			throw new k_http_Response(200);
		}
}
?>