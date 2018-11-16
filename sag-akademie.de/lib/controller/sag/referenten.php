<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of SAG_Referenten
 *
 * @author molle
 */
class SAG_Referenten extends SAG_Component {
	
	function construct() {
		list ($config, $content) = $this->createPageReader();
		$this->dsDb = new MosaikDatasource("dbtable");
		$this->pageReader->addDatasource($this->dsDb);
	}
	function map($name) {
	    return "SAG_Referenten";
	}

	function GET() {
		list($config, $content) = $this->createPageReader();

		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");
		$result = Doctrine::getTable("Referent")->getAll()->fetchArray();
		MosaikDebug::msg($result, "Result:");
		$this->dsDb->add("Referenten", $result);
		MosaikDebug::msg($this->dsDb, "DS:");
		$content->addDatasource($this->dsDb);
		$content->loadPage("ueberuns/".$this->name() . ".xml");
		return $content->output->get();
	}
}
?>
