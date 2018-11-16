<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Feiertag {
	var $_table="Feiertag";
	var $_view="";

	function table() {
		return Doctrine::getTable($this->_table);
	}

	function findObj($id) {
		$q->useResultCache(true, 3600, "rpc_{$this->_table}_{$id}");
		
		return $this->table()->find($id);
	}

	function find($id) {
		$result = array();
		$obj = $this->findObj($id);
		$result = $obj->toArray(true);
	
		return $result;
	}

	function save($id, $obj) {
		$result = array();

		$result = $this->findObj($id);
		$mergeData = mergeFilter ( $this->_table, $obj);

		$result->merge($mergeData);
		$result->save();
	
		return $result->toArray(true);
	}
	
	function create($datum, $name) {
		$feiertag = $this->table()->create();
		$user = Identity::get();
		
		$feiertag->datum = $datum;
		$feiertag->name = $name;
				
		$feiertag->save();
		$feiertag->refresh();
		
		return $feiertag->toArray(true);
	}

}