<?php
class SAG_Neuigkeit extends SAG_Component {
    var $entryTable = "neuigkeit";
    var $entryClass = "Neuigkeit";
    function construct() {
		list($config, $content) = $this->createPageReader();

		global $ormMap;

		$this->dTable = Doctrine::getTable($this->entryClass);
		$this->dsDb = new MosaikDatasource ("dbtable");
    }
    function forward ($class, $namespace="") {
		list($config, $content) = $this->createPageReader();

		$this->pageReader->output->addReplacement("page_background" , "/img/header_bg_gross.jpg");

		$content->loadPage( $this->url() ."/" . $this->next());
		return $content->output->get();
	}
}

