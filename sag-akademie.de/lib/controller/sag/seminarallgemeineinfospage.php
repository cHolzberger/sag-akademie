<?php
class SAG_SeminarAllgemeineInfosPage extends SAG_Component {
	function map($name) {
		return "SAG_SeminarAllgemeineInfosPage";
	}

	function GET() {		
		list($config, $content) = $this->createPageReader();
		$this->dsDb = new MosaikDatasource("dbtable");
		
				// hier contets auslesen und einer variablen zuweisen
		$info = Doctrine::getTable("WebText")->find("allgemeineInfos");
		
		$this->dsDb->add ("webtext",$info->text);
		$content->addDatasource($this->dsDb);

		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");

		$content->loadPage("seminar/". $this->name() . ".xml");
		return $content->output->get();
	}

	function forward ($class, $namespace="") {
		list($config, $content) = $this->createPageReader();

		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");
		$content->loadPage("seminar/" . $this->name() . "/". $this->next(). ".xml");
		return $content->output->get();
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
}
?>