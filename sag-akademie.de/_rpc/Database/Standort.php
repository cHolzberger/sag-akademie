<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Standort {
	var $_table="Standort";
	var $_view="";

	function table() {
		return Doctrine::getTable($this->_table);
	}

	function findObj($id) {
		return $this->table()->find($id);
	}

	function find($id) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": id =>{$id}");
		 
		$result = array();
		$obj = $this->findObj($id);
		$result = $obj->toArray();
	
		return $result;
	}

	function save($id, $obj) {
		$result = array();
		$user = Identity::get();

		$result = $this->findObj($id);
		$mergeData = mergeFilter ( $this->_table, $obj);
		
		$mergeData['geaendert'] = currentMysqlDatetime();
		$mergeData['geaendert_von'] = $user->getId();
		
		$result->merge($mergeData);
		$result->save();
		Doctrine::getTable('XTableVersion')->increment("Standort");
		return $result->toArray(true);
	}
	
	function create($name, $strasse, $nr, $plz,$ort, $art , $kuerzel) {
		qlog (__CLASS__ . "::". __FUNCTION__);
		try {
			$_s = new Standort();
			$user = Identity::get();
		
			$_s->name = $name;
			$_s->nr = $nr;
			$_s->ort = $ort;
			$_s->plz = $plz;
			$_s->art = $art;
			$_s->kuerzel = $kuerzel;
			
			$_s->geaendert = currentMysqlDatetime();
			$_s->geaendert_von = $user->getId();
		
			$_s->save();
			$_s->refresh();
		
			Doctrine::getTable('XTableVersion')->increment("Standort");
			
			return $_s->toArray(true);
		} catch ( Exception $e) {
			qlog("Exception: " . $e->getMessage());
		}
		return null;
	}

}