<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Download {
	private $_table="Download";
	private $_view="";

	/**
	 *
	 * @return NeuigkeitTable
	 */
	private function table() {
		return Doctrine::getTable($this->_table);
	}

	/**
	 *
	 * @param string $id
	 * @return Neuigkeit
	 */
	private function findObj($id) {
		return $this->table()->find($id);
	}

	/**
	 *
	 * @param string $id
	 * @return array
	 */
	function find($id) {
		$result = array();
		$obj = $this->findObj($id);
		$result = $obj->toArray(true);
	
		return $result;
	}

	/**
	 *
	 * @param string $id
	 * @param object $obj
	 * @return Neuigkeit
	 */
	function save($id, $obj) {
		$result = array();

		$result = $this->findObj($id);
		$mergeData = mergeFilter ( $this->_table, $obj);

		$result->merge($mergeData);
		$result->save();
	
		return $result->toArray(true);
	}
	
	/**
	 * create news
	 * @param string $titel
	 * @param string $kategorie
	 * @return Neuigkeit
	 */
	function create($name,  $kategorie) {
		$aufgabe = new Download ();
		$user = Identity::get();
		
		$aufgabe->name = $name;
		$aufgabe->kategorie = $kategorie;
		
		$aufgabe->geaendert_von = $user->getId();
		$aufgabe->geaendert = currentMysqlDatetime();
				
		$aufgabe->save();
		$aufgabe->refresh();
		
		return $aufgabe->toArray(true);
	}
	
	/**
	 * delete news entry
	 * @param string $id
	 */
	function delete($id) {
		$this->findObj($id)->delete();
	}

	/**
	 * delete news entry
	 * @param string $id
	 */
	function remove($id) {
		$this->findObj($id)->delete();
	}

}