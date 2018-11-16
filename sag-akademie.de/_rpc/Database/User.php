<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_User {
	var $_table="XUser";
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
	
	function create($vorname, $nachname, $name, $email) {
		qlog (__CLASS__ . "::". __FUNCTION__);
		try {
			$_u = new XUser();
			$user = Identity::get();
		
			$_u->name = $name;
			$_u->vorname = $vorname;
			$_u->nachname = $nachname;
			$_u->email = $email;
			//FIXME: deprecated
			$_u->class="admin";
		
			$_u->save();
			$_u->refresh();
		
			return $_u->toArray(true);
		} catch ( Exception $e) {
			qlog("Exception: " . $e->getMessage());
		}
		return null;
	}

}