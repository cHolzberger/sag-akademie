<?php

class SAG_SeminarInfo extends SAG_Component {
	var $entryTable = "seminarArt";
	var $entryClass = "SeminarArt";

	function construct() {
		list($config, $content) = $this->createPageReader();

		global $ormMap;

		$this->dTable = Doctrine::getTable($this->entryClass);
		$this->dsDb = new MosaikDatasource ("dbtable");
	}

	function map($name) {
		return "SAG_SeminarInfo";
	}

	function GET() {
		return "";
	}

	function forward($class, $namespace="") {
		// big page reader
		$key = urldecode ( $this->next() );
		list($config, $content) = $this->createPageReader();
		$content->addDatasource ( $this->dsDb );
		/*
		 * rubriken
		 */

		 $tab = Doctrine::getTable("ViewSeminarArtPreis");
		 $result = $tab->find($key);

	 	 $rubrik = $result->Rubrik->name;
		 $rubrik = str_replace ("ü","_u",$rubrik);
		 $rubrik = str_replace ("ä","_a",$rubrik);
		 $rubrik = str_replace ("ö","_o",$rubrik);
 		 $rubrik = str_replace (" ","%20",$rubrik);

		 $rubrik = utf8_decode ($rubrik);
		 $this->dataStore->add("image", "/img/title_$rubrik.png");
		 $add[]=array();
		 $add['name'] = $key;
		 $add['rubrik_name'] = $result->Rubrik->name;
		 $add['SeminarArten'] = array();
		/* named qurey */
		if ( is_object($result) && $result->status_name != "Aktiv") {
			$content->loadPage("seminar/info_inaktiv.xml");
		} else if ( is_object($result) ) {
			$seminarArt = $result;
			$i=0;
			//$r->refreshRelated();

			$add = $seminarArt->toArray(true);
			$add['Seminare'] = $seminarArt->getFutureSeminare();

			$i++;

			$this->dsDb->set ($add);
			$variable = array("page_background" => "/img/header_bg_gross.jpg");
			$content->loadPage("seminar/".$this->name() . ".xml");
		} else {
			// kein seminar gefunden
			$content->loadPage("seminar/info_empty.xml");
		}

		return $content->output->get();
	}


	function delete() {
		$row = $this->dTable->find($this->entryId);
		$row->delete();
	}

	function getEntry() {
		$cl = $this->entryClass;
		return new $cl (); // FIXME
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
}
?>