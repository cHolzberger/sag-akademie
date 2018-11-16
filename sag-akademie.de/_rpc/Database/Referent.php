<?

/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Referent {

	var $_table = "Referent";
	var $_view = "ViewSeminar";
	var $_terminView = "SeminarReferent";
	
	function getAllMail () {
		$q = Doctrine_Query::create()
		 ->from("Referent referent")
		 ->select("referent.email");
		
		$referenten = $q->fetchArray();
		
		return $referenten;	
	}
	
	function create(  $name, $vorname, $firma) {
		$user = Identity::get();
		
		$referent = new Referent();
		$referent->firma = $firma;
		$referent->vorname = $vorname;
		$referent->name = $name;
		
		$referent->geaendert_von = $user->getId();
		$referent->geaendert = currentMysqlDatetime();
		
		$referent->save();
		$referent->refresh();
		
		return $this->find($referent->id);
	}
	
	function find($id) {
		$result = array();
		$obj = Doctrine::getTable("Referent")->find($id);
		$result = $obj->toArray(true);

		return $result;
	}
	
	function save($id, $data) {
		$user = Identity::get();
		
		$result = array();
		// get old and new info to compare
		$newData = Doctrine::getTable("Referent")->find($id);
		$oldData = (object) ($newData->toArray());

		// merge in new data
		$newData->merge( mergeFilter("Referent", $data) );

		$referent->geaendert_von = $user->getId();
		$referent->geaendert = currentMysqlDatetime();
		// save new data
		$newData->save();
		
		//
		return (object)$newData->toArray();
	}

	function findBySeminar($seminar_art_id) {
		$result = array();
		return $result;
	}

	/**
	 *
	 * @param number $seminar_id
	 * @param number $standort_id
	 * @return array
	 */
	function findByTermin($seminar_id, $standort_id) {
		$result = array();
		$tbl = Doctrine::getTable($this->_terminView);
		$data = $tbl->getReferentenForSeminarId($seminar_id, $standort_id);

		$cTag = 0;
		$_lmax = 0;
		foreach ($data as $tag) {
			$cTag = $tag['tag'];
			if ( $cTag > $_lmax ) $_lmax = $cTag;

			if (!array_key_exists($cTag, $result) || !is_array($result[$cTag]))
				$result [$cTag] = array();
			$tmp = $tag['Referent'];
			$tmp['theorie'] = $tag['theorie'];
			$tmp['praxis'] = $tag['praxis'];
			$result[$cTag][] = $tmp;
		}
		$result['length'] = $_lmax;
		return $result;
	}

}