<?php
/* 
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class GENERIC_Widget extends Generic_Admin_Component {
	function init() {
		list($config, $content) = $this->createPageReader();

		$this->dsDb = new MosaikDatasource ( "dbtable" );
		$this->pageReader->addDatasource ( $this->dsDb );
		
		return $content;
	}
	
	function renderHtml() {
		$pr = $this->init();
		$pr->loadPage("widget/mtable");
		
		return $pr->output->get();
	}
	
	function renderHtmlForward($class_name, $namespace) {
		$GLOBALS['firephp']->log("SAG_DBContent::renderHtmlForward");
		return $this->renderHtml();
	}
}
?>