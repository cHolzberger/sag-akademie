<?php
/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * GenericEvent basisklasse fuer alle Events
 *
 * @author molle
 */
class GenericEvent  {
    var $name;
	var $message;
	var $sourceClass=null;
	var $sourceFunction=null;
	var $sourceLine=null;
	var $sourceFile=null;

	function setSourceInfo( $sfile, $sline,$sclass, $sfunction) {
		$this->sourceClass = $sclass;
		$this->sourceFunction = $sfunction;
		$this->sourceLine = $sline;
		$this->sourceFile = $sfile;
	}
}