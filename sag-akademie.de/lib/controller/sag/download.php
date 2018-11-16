<?php

/*
 * copyright 2010 by MOSAIK Software - www.mosaik-software.de
 * use without written license not allowed!
*/
class SAG_Download extends SAG_Component {

    var $entryTable = "x_download";
    var $entryClass = "XDownload";
    
    function map($name) {
	if ($name == "kursinformationen") {
	    return "SAG_Download_Kursinformationen";
	} else {
	    return "SAG_Download";
	}
    }
    
    function construct() {
	list($config, $content) = $this->createPageReader();

	global $ormMap;

	$this->dTable = Doctrine::getTable($this->entryClass);
	$this->dsDb = new MosaikDatasource ("dbtable");
    }
    
    function renderHtml() {
		$this->pageReader->addDatasource($this->dsDb);
		
    
		$this->dsDb->add("Kategorien", Doctrine::getTable("DownloadKategorie")->findAll()->toArray(true) );
		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");

		$this->pageReader->loadPage($this->url());
	return $this->pageReader->output->get();
    }
    
    function renderHtmlForward($class_name, $namespace = "") {
		
		$this->pageReader->addDatasource($this->dsDb);
		$name = urldecode($this->next());
		MosaikDebug::msg($name,"next");
		$kategorien = Doctrine::getTable("DownloadKategorie")->findAll()->toArray(true);

		$this->dsDb->add("kategorieName", $name);
		
		$this->dsDb->add("Kategorien", $kategorien);
		$kategorieId = 0;
		foreach ( $kategorien as $kategorie ) {
			if ( $kategorie['name'] == $name ) {
				$kategorieId = $kategorie['id'];
			}
		}

		$this->dsDb->add("Downloads", Doctrine::getTable("Download")->findByDql("kategorie=?",$kategorieId)->toArray(true) );

		if ($name == "kursinformationen" ) {
			$next = $this->createComponent($class_name, $namespace);
			return $next->dispatch();
		}
		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");

		//$this->pageReader->loadPage($this->url() . "/" . $this->next());
		$this->pageReader->loadPage($this->url() . "/item.xml");
		return $this->pageReader->output->get();
    }
    
    function renderDownload() {
	instantRedirect("/downloads");
    }
    
    function renderDownloadForward($class, $namespace = "") {
	$name = $this->next();
	$download = Doctrine::getTable("XDownload")->find($name);
	setHttpFilename($download->download_name);
	setHttpContentType("application/x-download");
	return file_get_contents(WEBROOT . $download->store . $download->file_path);
    }
}

?>