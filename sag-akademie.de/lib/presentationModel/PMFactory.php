<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class PMFactory {
    private $pmCache=array();

    /**
     * gibt einen neuen filter zurueck
     *
     * @param string $namespace
     * @param string $name
     *
     * @return PMBasic
     */
    function createFilter($namespace, $name) {
	$this->lookupCache($namespace, $name);
	return $this->pmCache[$namespace][$name];
    }

    function lookupCache($namespace,$name) {
	if ( @ !is_array ( $this->pmCache[$namespace] )) {
	    include_once("presentationModel/$namespace/$name.php");

	    $this->pmCache[$namespace] = array();
	    $this->pmCache[$namespace][$name] = new $name();
	} else if (@ !is_object ($this->pmCache[$namespace][$name]) ) {
	    $this->pmCache[$namespace][$name] = new $name();
	} else {
	    $this->pmCache[$namespace][$name]->cached=true;
	}
    }
}
?>
