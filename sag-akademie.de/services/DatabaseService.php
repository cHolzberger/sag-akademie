<?php

ini_set('include_path',
    ini_get('include_path')
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/controller"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib"
	.PATH_SEPARATOR.dirname(dirname(__FILE__))."/services"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/templates"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/konstrukt"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/"
    .PATH_SEPARATOR.dirname(dirname(__FILE__))."/lib/Doctrine/models"
);

include_once("services/GenericService.php");

/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of DatabaseService
 *
 * @author molle
 */
class DatabaseService extends GenericService {
    /**
     * derzeitg aktives query
     * @var Resource
     */
    public $currentQuery;

     /**
     * speichert das letzte Query Object zwischen
     *
     * @var Resource
     **/
    public $lastQuery = null;

     /**
     * soll die Ergebnismenge zurueckgegeben werden?
     * @var boolean
     */
    public $doFetch = false;


    /**
     * Ruft die daten ab die in $lastQuery angefordert wurden
     */
    protected function _doFetch() {
	$this->lastQuery = $this->currentQuery;
	return false;
    }
}
