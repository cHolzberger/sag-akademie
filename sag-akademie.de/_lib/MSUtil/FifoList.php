<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of List
 *
 * @author cholzberger
 */
class MSUtil_FifoList {
	var $_cid = 0;
	var $_arr = null;

	public function __construct() {
		$this->_arr = array();
	}

	function rewind () {
		$this->_cid=0;
	}

	function next() {
		$this->_cid = $this->_cid+1;
	}

	function valid () {
		if ( $this->_cid < $this->_max ) {
			return true;
		} return false;
	}

	function push (&$dta) {
		$this->_arr[] = $dta;
		$this->_max = count($this->_arr);
	}

	function &current() {
		return $this->_arr[$this->_cid];
	}

	function isEmpty () {
		return ( count($this->_arr) == 0 );
	}
    
}
