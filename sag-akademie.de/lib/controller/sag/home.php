<?php

class SAG_Ueberuns extends SAG_Component {
	public $map = Array (
		"philosphie" => "SAG_Staticpage",
		"partner" => "SAG_Ueberuns_Partner",
		"referenten" => "SAG_Referenten",
	);

	function map($name) {
		return $this->map[$name];
	}

	function GET() {
		list($config, $content) = $this->createPageReader();

		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");

		$content->loadPage($this->name() . ".xml");
		return $content->output->get();
	}
}

class SAG_Seminar extends SAG_Component {
	public $map = Array (
		"termin" => "SAG_SeminarTermin",
		"info" => "SAG_SeminarInfo",
		"buchen" => "SAG_SeminarBuchen",
		"allgemein" => "SAG_SeminarAllgemeineInfosPage",
	);

	function map($name) {
		return $this->map[$name];
	}
	 
	function GET() {
		list($config, $content) = $this->createPageReader();

		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");

		$content->loadPage($this->name() . ".xml");
		return $content->output->get();
	}
}

class SAG_Kunde extends SAG_Component {
	public $map = Array (
		"buchung" => "SAG_KundeBuchung" );
		

	function map($name) {
		return $this->map[$name];
	}
}
/**
 * Public Web Site
 *
 * this class routes the request to the appropriate controllers for the public domain
 *
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package SAG
 * @license Use not permitted without written license
 *
 */
class SAG_Home extends SAG_Component {
	public $map = Array (
		"startseite" => "SAG_Staticpage",
		"newsletter" => "SAG_Newsletter",
		"neuigkeiten" => "SAG_Neuigkeit",
		"ueberuns" => "SAG_Ueberuns",
		"seminar" => "SAG_Seminar",
		"buchung" => "SAG_Staticpage",
		"schulungszentrum" => "SAG_Staticpage",
		"download" => "SAG_Download",
		"stellenangebote" => "SAG_Staticpage",
		"kontakt" => "SAG_Staticpage",
		"impressum" => "SAG_Staticpage",
		"datenschutz" => "SAG_Staticpage",
		"cookies" => "SAG_Staticpage",
		"sitemap" => "SAG_Staticpage",
		"bildungscheck" => "SAG_Staticpage",
		"zfkd" => "SAG_Staticpage",
		"sachkunde" => "SAG_Staticpage",
		"seminaruebersicht" => "SAG_Staticpage",
		"kunde" => "SAG_Kunde_Login",
		"captcha" => "SAG_Captcha",
		"pdf" => "SAG_Pdf",
		"form" => "SAG_Form",

		"admin" => "ADM_Admin"
	);

	function map($name) {
		if ( array_key_exists ($name, $this->map)) {
			return $this->map[$name];
		} else {
			return FALSE;
		}
	}
	
	function renderHtml() {
		
		$this->dsDb = new MosaikDatasource("dbtable");
		
		$this->identity();

		
		list($config, $content) = $this->createPageReader();
		$neuigkeiten = Doctrine::getTable("Neuigkeit")->getLastNews()->execute()->toArray(true);
		
		$this->dsDb->add("Neuigkeiten", $neuigkeiten);
		
		$content->addDatasource($this->dsDb);
		
		
	
		$content->loadPage("startseite.xml");
		$this->dataStore->add ("text",$content->output->get());
	
		$content->output->clear();
		$this->pageReader->loadPage("__engine.xml");
		
		return $this->pageReader->output->get($content->variable);
	}

	function renderHtmlForward($class_name, $namespace = "") {
		$this->identity();
		if (is_array($class_name)) {
      		$namespace = $class_name[1];
      		$class_name = $class_name[0];
		}

		$next = $this->createComponent($class_name, $namespace);
		
		if ( $this->next() == "admin" ||getRequestType()  == "pdf" || getRequestType() == "csv" || getRequestType() == "download") { // this sucks!!!
			return $next->dispatch();
		}
		
		$this->createPageReader();

		$content = "";
		
		try {
			$content = $next->dispatch();
			
			$this->dataStore->add ("text",$content);
			$this->pageReader->loadPage("__engine.xml");
  			return mb_convert_encoding( $this->pageReader->output->get(), "UTF-8","auto");
		} catch (MosaikPageReader_PageNotFound $e) {
 		   	$content = $e->content();
 		   	$this->dataStore->add ("text",$content);
		   	$this->pageReader->loadPage("__engine.xml");
  		 	throw new k_HttpResponse("404", mb_convert_encoding( $this->pageReader->output->get(), "UTF-8","auto"));
		} catch (Exception $e) {
			throw ($e);
		}
  	}

	function renderIframe() {
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

	function renderIframeForward($class_name, $namespace = "") {
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


	function HEAD() {
		throw new k_http_Response(200);
	}
}
