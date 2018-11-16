<?php

/*
 * 30.01.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 *
 * liefert alle infos die fuer die Termin und Seminarseite gebraucht werden zurueck
 */

include_once ("helpers.php");

class Pages_TermineSeminare {
	function getRubriken() {
		
	}

	function getOverview() {
		$sar = Doctrine::getTable("SeminarArtRubrik")->overview()->fetchArray();
		return $sar;
	}
}