<?php
include("adm/dbcontent.php");

class ADM_Planung extends SAG_Admin_Component {
	function map($name) {
		return "ADM_Planung";
	}
	
	function renderHtml() {
		$ds = new MosaikDatasource();
		
		list($config, $content) = $this->createPageReader();
		$content->addDatasource($ds);

		$this->addBreadcrumb($this->url(), "Planung");

		$ds->add ("Year", $this->getYears());
				
		$this->pageReader->loadPage("planung");
		return $this->pageReader->output->get();
  	}


	function getYears () {
		$year = date("Y") - 1;
		$selected = date("Y");
		
		if ( isset ( $_GET['year'])) {
			$selected = $_GET['year'];
		}

		$years = array();
		for ( $i=$year; $i < $year + 5; $i++) {
			if ( $i == $selected) {
				$years[] = array("label"=>"<font size='+1'>"+$i +'</font>', "year" => $i);
			} else {
				$years[] = array("label"=>""+$i, "year" => $i);
			}
		}
		return $years;
	}

	function renderHtmlForward($class_name, $namespace = "") {
		$this->addBreadcrumb($this->url(), "Planung");
		$ds = new MosaikDatasource();
		$standorte = Doctrine::getTable("Standort")->findAll(Doctrine::HYDRATE_ARRAY);

		foreach ( $standorte as $key=>$x) {
			if ( isset ( $_GET['seminar_art_id'] ) ) {
				$standorte[$key]['seminar_art_id'] = $_GET['seminar_art_id']; // quickbugfix
			}

			if ( isset ( $_GET['seminar_id'] ) ) {
				$standorte[$key]['seminar_id'] = $_GET['seminar_id']; // quickbugfix
			}
		}

		$ds->add ("Standort", $standorte);
		$ds->add ("Year", $this->getYears());
		$ds->add ("GET", $_GET);
		
		list($config, $content) = $this->createPageReader();
		$content->addDatasource($ds);
	
		if (is_array($class_name)) {
      		$namespace = $class_name[1];
      		$class_name = $class_name[0];
    	}
		
		$this->pageReader->loadPage( "planung/".$this->next() );
		return $this->pageReader->output->get();
  	}
}
