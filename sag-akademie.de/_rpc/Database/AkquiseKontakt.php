<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_AkquiseKontakt {

	var $_table = "AkquiseKontakt";
	var $_view = "ViewAkquise";
	var $_header = null;

	function table() {
		return Doctrine::getTable($this->_table);
	}

	function view() {
		return Doctrine::getTable($this->_view);
	}

	function search($searchText) {
		$q = $this->table()->detailSearch($searchText);

		return $q->fetchArray();
	}

	function searchStart($a) {
		$a = utf8_encode(urldecode($a));
		$hits = $this->table()->search($a . "%");
		$ids = array();

		if (count($hits) < 1) {
			return array();
		}

		foreach ($hits as $hit) {
			$ids[] = $hit['id'];
		}

		$q = $this->table()->detailedIn($ids);
		return $q;
	}

	function advancedSearch($rules) {
		if ( is_array($rules)) {
			ksort($rules);
		} else if ( is_string($rules)) {
			$rules = explode("#:#", $rules);
		}

		$q = $this->view()->advancedSearch($rules);
		return $q;
	}

	/**
	 * save a dataset for this table
	 *
	 * @param mixed $obj
	 * @param mixed $id
	 * @return boolean
	 */
	function save($id, $obj) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": $id");

		$user = Identity::get();

		//get the object
		$change_obj = $this->table()->find($id);
		$old_data = (object) $change_obj->toArray();
		// merge the data ( using helper)
		$mergeData = mergeFilter ( $this->_table, $obj);
		qdir($mergeData);

		$change_obj->merge($mergeData);
		$change_obj->geaendert_von = $user->getId();
		$change_obj->geaendert = currentMysqlDatetime();

		$change_obj->save();
		$change_obj->refresh();

		if ( $change_obj->newsletter == 1 && $old_data->newsletter == 0) {
			$change_obj->anmelde_datum = currentMysqlDate();
		} else if ( $change_obj->newsletter == 0 && $old_data->newsletter == 1) {
			$change_obj->abmelde_datum = currentMysqlDate();
		}
		
		return $this->find($change_obj->id);
	}

	/**
	 * 	querys this database view for the tupel with id $id resolves its relations
	 * and returns the data as array.
	 *
	 * @param mixed $id
	 * @return array
	 */
	function find($id) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . " => $id" );
		
		if ( !$id>0  ) {
			qlog("Error ID is invalid $id");
			throw new Exception ("Invalid Request");
		}
		
		$obj = $this->view()->find($id);
		$array = $obj->toArray(true);

		return $array;
	}

	function create($name) {
		$user = Identity::get();

		$kontakt = new AkquiseKontakt();
		$kontakt->firma = $name;
		$kontakt->alias = $name;
		$kontakt->kontakt_quelle_stand = currentMysqlDatetime();
		$kontakt->angelegt_datum = currentMysqlDatetime();
		$kontakt->angelegt_user_id = $user->getId();
		$kontakt->save();
		$kontakt->refresh();

		return $this->find($kontakt->id);
	}

	function filter() {

	}

}

?>
