<?php

class SAG_Staticpage extends SAG_Component {
	function map($name) {
		return "SAG_Staticpage";
	}

	function renderIframe( ) {
		return $this->renderHtml();
	}

	function renderHtml() {
		$this->dsDb = new MosaikDatasource("dbtable");
		
		
		list($config, $content) = $this->createPageReader();
		$neuigkeiten = Doctrine::getTable("Neuigkeit")->getLastNews()->execute()->toArray(true);
		$this->dsDb->add("Neuigkeiten", $neuigkeiten);
		$content->addDatasource($this->dsDb);
		
		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");

		$content->loadPage( $this->url());
		return $content->output->get();
	}

	function forwardIframe($class, $namespace) {
		return $this->forward($class, $namespace);
	}

	function forward ($class, $namespace="") {
		list($config, $content) = $this->createPageReader();

		$this->pageReader->output->addReplacement("page_background" , "/img/header_bg_gross.jpg");
		
		$content->loadPage( $this->url() ."/" . $this->next());
		return $content->output->get();
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
}


