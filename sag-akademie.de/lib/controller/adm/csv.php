<?php

class ADM_Csv extends SAG_Admin_Component {
	
	 public $map = array (
			'teilnehmerliste' => "ADM_CSV_Teilnehmerliste",
			'personenliste' => "ADM_CSV_Personenliste",
			'seminarreferent' => "ADM_CSV_SeminarReferent"
		);
	
	function map($name) {
		return $this->map[$name];		
	}
	
	function renderCsvForward($class_name, $namespace) {
		list($config, $content) = $this->createPageReader();
	
		if (is_array($class_name)) {
      		$namespace = $class_name[1];
      		$class_name = $class_name[0];
    	}
		
		$next = $this->createComponent($class_name, $namespace);
		$content = "";

		return $next->dispatch();
	}
	
	function renderHtml() {
		return "";
	}
	
	function HEAD() {
		throw new k_http_Response(200);
	}
	
	function GET() {
		
	}
}

