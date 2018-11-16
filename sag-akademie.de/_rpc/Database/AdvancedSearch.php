<?
/*
 * 13.10.2010 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_AdvancedSearch {
	var $_table = "";
	var $_view = "";
	var $_header = null;
	
	function table() {
		return Doctrine::getTable($this->_table);
	}
	
	function view() {
		return Doctrine::getTable($this->_view);
	}

	function doSearch($table, $rules) {
		if ( is_array($rules)) {
			ksort($rules);
		} else if ( is_string($rules)) {
			$rules = explode("#:#", $rules);
		}
		
		$q = $this->view()->advancedSearch($rules);
		$q->useResultCache(true, 3600, "rpc_advanced_search_{$rules}");
		return $q;
	}
	
	function getColumns($table) {
		
	}


}