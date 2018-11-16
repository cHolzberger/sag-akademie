<?php
class SAG_StaticSeminarPage extends SAG_Component {
	function map($name) {
		return "SAG_Staticpage";
	}

	function GET() {
		list($config, $content) = $this->createPageReader();

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