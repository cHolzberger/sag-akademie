<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_InhouseSeminar {
	private $_table = "SeminarArt";
	
	function view() {
		return Doctrine::getTable("ViewInhousePreis");
	}


	function findObj($id) {
			$q= Doctrine::getTable("SeminarArt")->detailed($id);
			$q->useResultCache(true, 3600, "rpc_{$this->_table}_{$id}");
			return $q->execute()->getFirst();
	}

	function find($id) {
		$result = array();
		$obj = $this->findObj($id);
		$result = $obj->toArray(true);

		return $result;
	}

	function save($id, $data) {
		$result = array();
		// get old and new info to compare
		$newData = Doctrine::getTable("SeminarArt")->findByDql("inhouse=1 AND id=?", $id)->getFirst();
		$oldData = (object) ($newData->toArray());

		// merge in new data
		$newData->merge( mergeFilter("SeminarArt", $data) );

		// save new data
		$newData->save();
		Doctrine::getTable('XTableVersion')->increment("InhouseSeminarArt");

		//
		return (object)$newData->toArray();
	}

	function create($seminarId, $planung) {
		$seminar = new SeminarArt();
		$seminar->id = $seminarId;
		$seminar->sichtbar_planung = $planung;
		$seminar->inhouse = 1;
		//$seminar->standort_id = -1;
		$seminar->status = 1;

		$seminar->save();

		Doctrine::getTable('XTableVersion')->increment("InhouseSeminarArt");

		return $this->find($seminarId);
	}
	
	/**
	 * clones a seminar and sets the clone to inhouse
	 * 
	 * @param string $seminarId
	 * @param string $newSeminarId
	 * @return object 
	 */
	function cloneFromSeminar( $seminarId, $planung, $newSeminarId) {
		$origSeminar = Doctrine::getTable("SeminarArt")->find($seminarId);
		
		$seminar = new SeminarArt();
		$seminar->merge ( $origSeminar->toArray());
		
		$seminar->id = $newSeminarId;
		$seminar->sichtbar_planung = $planung;
		$seminar->inhouse = 1;
		$seminar->status = 1;
		$seminar->save();
		Doctrine::getTable('XTableVersion')->increment("InhouseSeminarArt");

		return $this->find($newSeminarId);
	}
	
	function remove($seminarArtId) {
		$seminarArt = Doctrine::getTable("SeminarArt")->find($seminarArtId);
		$seminare = Doctrine::getTable("Seminar")->findByDql("seminar_art_id = ?", array($seminarArt->id));
		
		foreach ( $seminare as $seminar ) {
			$buchungen = Doctrine::getTable("Buchung")->findByDql("seminar_id = ?"  , array($seminar->id));
			foreach (  $buchungen as $buchung ) {
				$buchung->delete();
			}
			$seminar->delete();
		}
		
		$seminarArt->delete();	
		Doctrine::getTable('XTableVersion')->increment("InhouseSeminarArt");
		
	}

}