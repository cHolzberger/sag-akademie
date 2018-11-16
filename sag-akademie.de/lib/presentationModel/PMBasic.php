<?php
/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of PMSummeBuchungen
 *
 * @author molle
 */
class PMBasic {
    public $cached = false;

    public $src = null;
    public $dst = null;
    /**
     * erstellt summen der vorhandenen felder des eingabearrays $data
     * filtert dabei felder mit dem name monat und bennent sie dem entsprechenden monat nach um
     *
     * @param array $source
     * @param array $dest
     *
     */
    function filter($source, &$dest) {
	// klassen variablen setzen
	$this->src = &$source;
	$this->dst = &$dest;

	// prefilter aufruf
	$this->preFilter();

	$this->runFilter();

	$this->postFilter();
    }




    function preFilter() {

	MosaikDebug::msg(count($this->src),__CLASS__. " => running filter on array with length:");
    }

    function runFilter() {
	
    }

    function postFilter() {
	
    }
}
?>