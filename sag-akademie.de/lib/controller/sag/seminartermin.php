<?php
include_once("mosaikTaglib/dlog.php");

class SAG_SeminarTermin extends SAG_Component {
	var $entryTable = "seminarArt";
	var $entryClass = "SeminarArt";

	function construct() {
		list($config, $content) = $this->createPageReader();

		global $ormMap;
		$this->pageReader->output->addReplacement("page_background", "/img/blue_box_klein.jpg");

		$this->dbTable = Doctrine::getTable($this->entryClass);
		$this->dbDataStore = new MosaikDatasource("dbtable");
	}

	function map($name) {
		return "SAG_SeminarTermin";
	}

	function GET() {
		$GLOBALS['firephp']->info("DBText::GET");
		return "";
	}

	function forward($class, $namespace=null) {
		extract ( $GLOBALS['debug']);
		$add=array();
		$name = str_replace ("_u","ü",$this->next());
		$name = str_replace ("_a","ä",$name);
		$name = str_replace ("_o","ö",$name);
		$name = str_replace ("%20"," ",$name);
		$add['name'] = $name;
		$add['rubrik1'] = $name;

		$add['SeminarArten'] = array();	
		//content page reader
		list($config, $content) = $this->createPageReader();

		$content->addDatasource ( $this->dbDataStore );

		$this->dataStore->add("image", "/img/title_". $this->next() . ".png");
		
		//$firephp->log("query");
		//$result = $tab->execute( array($name) )->getFirst(); 
		$result = Doctrine::getTable("SeminarArtRubrik")->findOneByName($name);
		$seminarArten = $result->getSeminarArtenFromView()->execute();
		
		if ( is_object($seminarArten) ) {
			$i=0;
		
			//$seminarArten = $result[0]->SeminarArten;
			foreach ($seminarArten as $seminarArt) {
				if ( $seminarArt->status == 1) {
					$add['SeminarArten'][$i] = $seminarArt->toArray(true);
					$add['SeminarArten'][$i]['HasMoreSeminare'] = $seminarArt->hasMore();
					$add['SeminarArten'][$i]['Seminare'] = $seminarArt->getNextFour();
					$add['SeminarArten'][$i]['title'] =  $this->next();
					$i=$i+1;
				}
			}		
			
			$this->dbDataStore->set ($add);
			$this->dataStore->add ("text",$content->output->get());

			$content->loadPage("seminar/".$this->name() . ".xml");
		} else { // kein seminar gefunden
			$this->dataStore->add("text", $content->output->get());
			$content->loadPage($this->name()."_empty.xml");
		}

		return $content->output->get();
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
}
?>