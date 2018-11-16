<?php
/* 
 * 25.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */


//http://sag-akademie.localhost/resources/test/pdfgenerate/tischbeschilderung.php


//$pipeline->process('http://sag-akademie.localhost/resources/test/pdfgenerate/teilnehmerliste.php', $media); 


$GLOBALS['pdf-margin-top']=0;
$GLOBALS['pdf-margin-bottom']=0;
$GLOBALS['pdf-margin-left']=5;
$GLOBALS['pdf-margin-right']=5;

$GLOBALS['pdf-landscape']=false;
$GLOBALS['pdf-page-size']="a4";

class Generic_AdminPDFDocument extends SAG_Admin_Component {
	function &initDatasource() {
			$this->dsDb = new MosaikDatasource ( "dbtable" );
			return $this->dsDb;
	}
	
	function generateHtml($template) {
		list($config, $content) = $this->createPageReader();
		$content->addDatasource ( $this->dsDb );
		$content->loadPage("pdf/" . $template);
		if ( array_key_exists("margin-top", $content->variable)) $GLOBALS['pdf-margin-top'] = $content->variable['margin-top'];
		if ( array_key_exists("margin-bottom", $content->variable)) $GLOBALS['pdf-margin-bottom'] = $content->variable['margin-bottom'];
		if ( array_key_exists("margin-left", $content->variable)) $GLOBALS['pdf-margin-left'] = $content->variable['margin-left'];
		if ( array_key_exists("margin-right", $content->variable)) $GLOBALS['pdf-margin-right'] = $content->variable['margin-right'];
		if ( array_key_exists("landscape", $content->variable))	$GLOBALS['pdf-landscape'] = $content->variable['landscape'];
		if ( array_key_exists("page-size", $content->variable))	$GLOBALS['pdf-page-size'] = $content->variable['page-size'];
		

		return $content->output->get();		
	}
}

?>