<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SAG_Download_Kursinformationen extends SAG_Component {
     var $entryTable = "seminar_art";
    var $entryClass = "SeminarArt";

    function map($name) {
	return "SAG_Kursinformationen";
    }
    function construct() {
	list($config, $content) = $this->createPageReader();

	global $ormMap;
	$this->dsDb = new MosaikDatasource("dbtable");

	$this->dTable = Doctrine::getTable($this->entryClass);
	$this->pageReader->addDatasource($this->dsDb);
    }

    function renderHtml() {
	$this->pageReader->output->addReplacement("page_background" , "/img/header_bg_gross.jpg");
	$seminare = $this->dTable->findByStatus("1",Doctrine::HYDRATE_ARRAY);

	$this->dsDb->add("seminarArt",$seminare);
	$this->pageReader->loadPage( "download/kursinformationen" );
	return $this->pageReader->output->get();
    }
}
?>
