<?php
/* 
 * 25.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */


//http://sag-akademie.localhost/resources/test/pdfgenerate/tischbeschilderung.php


//$pipeline->process('http://sag-akademie.localhost/resources/test/pdfgenerate/teilnehmerliste.php', $media); 


class Generic_CSVDocument extends SAG_Admin_Component {
	function &initDatasource() {
			$this->dsDb = new MosaikDatasource ( "dbtable" );
			return $this->dsDb;
	}
	
}

?>