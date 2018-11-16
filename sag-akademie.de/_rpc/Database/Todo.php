<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Todo {
	var $_table="XTodo";
	var $_view="";

	function table() {
		return Doctrine::getTable($this->_table);
	}

	function findObj($id) {
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
	
	function create($betreff, $kategorie, $rubrik, $zugewiesen) {
		$aufgabe = new XTodo();
		$user = Identity::get();
		
		$aufgabe->betreff = $betreff;
		$aufgabe->kategorie = $kategorie;
		$aufgabe->rubrik_id = $rubrik;
		$aufgabe->zugeordnet_id = $zugewiesen;
		$aufgabe->erstellt_von_id = $user->getId();
		$aufgabe->erstellt = currentMysqlDatetime();
		$aufgabe->status = 1;
		
		$aufgabe->save();
		$aufgabe->refresh();
		
		return $aufgabe->toArray(true);
	}

}