<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Person {
	var $_table="Person";
	var $_view="ViewPerson";

	function table() {
		return Doctrine::getTable($this->_table);
	}

	function view() {
		return Doctrine::getTable($this->_view);
	}

	function findObj($id) {
		return $this->view()->find($id);
	}

	function find($id, $options) {
		$result = array();
		$obj = null;

		$obj = $this->findObj($id);

		$result = $obj->toArray(true);
	
		return $result;
	}

	function create($kontaktId, $name, $vorname, $anrede, $email) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": kontaktId: $kontaktId name: $name anrede: $anrede");
		$person = new Person();

		$person->kontakt_id = $kontaktId;
		$person->name = $name;
		$person->vorname = $vorname;
		$person->geschlecht = $anrede;
		$person->email = $email;
		$person->land_id = -1;
		$person->bundesland_id = -1;
		
		$person->save();
		$person->refresh();

		return $person->toArray();
	}

	function save($id, $obj) {
		qlog(__CLASS__ . "::" .__FUNCTION__);
		qdir($obj);
		
		$result = array();

		$result = $this->table()->find($id);
		$mergeData = mergeFilter ( $this->_table, $obj);

		$result->merge($mergeData);
		$result->save();
		$result->refresh();
	
		return $this->find($result->id,NULL);
	}

	function search ( $searchstring, $options) {		
		qlog(__CLASS__ . "::" .__FUNCTION__. " Searchstring: {$searchstring}");
		qdir($options);
		
		if ( @!isset ( $options['kontaktId'])) {
			return Doctrine::getTable("Person")->simpleSearch($searchstring)->fetchArray();
		} else {
			$q = Doctrine_Query::create()->from("ViewPerson")
			 ->where("kontakt_id = ?" , $options['kontaktId'])
			 ->andWhere("name LIKE ? OR vorname LIKE ?", array("%$searchstring%", "%$searchstring%"));
			
			return $q->fetchArray(); 
		}
		return null;
	}

}