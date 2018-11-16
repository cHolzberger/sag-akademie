<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class SeminareOhneReferent extends NotificationCheck {

    var $view = "ViewSeminarOhneReferent";

    function run() {
		parent::run();
	
		$seminare = Doctrine::getTable($this->view)->findAll();
		
		for ( $i=0; $i<count($seminare); $i++ ) {
		    $seminar = $seminare[$i];
		    if ( $seminar->createReferentenFromTemplate() ) {
				echo "Erstelle Referenten f&uuml;r: " . $seminar->kursnr  .  "<br/>";
		    } else {
				echo "Keine Referenten f&uuml;r: " . $seminar->kursnr  .  "<br/>";
		    }
	}

	return true;
    }

}
?>
