<?
/*
 * 02.02.2011 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
include_once ("helpers.php");

class Database_Seminar {
	private $_table = "SeminarArt";

	function findObj($id) {
		if (!isset($id) || !is_string($id)) {
			throw new Exception("Id is invalid: " . $id);
		}

		$q = Doctrine::getTable("SeminarArt")->detailed();
		$q->useResultCache(true, 3600, "rpc_{$this->_table}_{$id}");

		return $q->execute($id)->getFirst();
	}

	function find($id) {
		$result = array();
		$obj = $this->findObj($id);
		$result = $obj->toArray(true);

		return $result;
	}

	function save($id, $data) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": id => " . $id);
		//qdir($data);
		if (count($data) == 0) {
			return $this->find($id);
		}
		$result = array();
		// get old and new info to compare
		$newData = Doctrine::getTable("SeminarArt")->find($id);
		if (!empty($data['farbe'])) {
			$data['farbe'] = str_replace("#", "0x", $data['farbe']);
		}

		if (!empty($data['textfarbe'])) {
			$data['textfarbe'] = str_replace("#", "0x", $data['textfarbe']);
		}

		if ( $data['status'] == 2 && $newData->status != 2) {
			$data['deaktiviert_datum'] = mysqlDateFromSeconds( time() );
		}
		// merge in new data
		$newData->merge(mergeFilter("SeminarArt", $data));

		Doctrine::getTable('XTableVersion')->increment("SeminarArt");

		// save new data
		$newData->save();
		$newData->refresh();

		//if ($newData->aktualisierung_gesperrt != 1) {
			//FIXME: standortabhängige synkronisation
		//	$newData->updateSeminarPreis();
		//}

		//
		return $newData->toArray();
	}

	function create($seminarId, $rubrikId, $planung) {
		$seminar = new SeminarArt();
		$seminar->id = $seminarId;
		$seminar->rubrik = $rubrikId;
		$seminar->sichtbar_planung = $planung;
		$seminar->status = 3;
		$seminar->angelegt_datum = mysqlDateFromSeconds( time() );

		$seminar->save();
		Doctrine::getTable('XTableVersion')->increment("SeminarArt");

		return $this->find($seminarId);
	}

	/**
	 * clones a seminar and sets the clone to inhouse
	 *
	 * @param string $seminarId
	 * @param string $newSeminarId
	 * @return object
	 */
	function cloneFromSeminar($seminarId, $rubrikId, $planung, $newSeminarId) {
		$origSeminar = Doctrine::getTable("SeminarArt")->find($seminarId);

		$seminar = new SeminarArt();
		$seminar->merge($origSeminar->toArray());
		$seminar->status = 3;
		$seminar->id = $newSeminarId;
		$seminar->sichtbar_planung = $planung;
		$seminar->rubrik = $rubrikId;
		$seminar->save();

		return $this->find($newSeminarId);
	}

	function getKooperationspartner($seminarId) {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": {$seminarId}");

		$origSeminar = Doctrine::getTable("SeminarArt")->find($seminarId);
		//	qdir($origSeminar->Kooperationspartner[0]->Kooperationspartner->toArray(true));
		return $origSeminar->Kooperationspartner->toArray();
	}

	function addKooperationspartner($seminarId, $kooperationspartnerId) {
		$s = new SeminarArtZuKooperationspartner();
		$s->seminar_art_id = $seminarId;
		$s->kooperationspartner_id = $kooperationspartnerId;
		$s->save();

		return $this->getKooperationspartner($seminarId);
	}

	function delKooperationspartner($seminarId, $kooperationspartnerId) {
		$origSeminar = Doctrine::getTable("SeminarArtZuKooperationspartner")->findByDql("seminar_art_id = ? AND kooperationspartner_id = ?", array($seminarId, $kooperationspartnerId));
		$origSeminar->delete();
		return $this->getKooperationspartner($seminarId);
	}

	function remove($seminarArtId) {
		$seminarArt = Doctrine::getTable("SeminarArt")->find($seminarArtId);
		$seminare = Doctrine::getTable("Seminar")->findByDql("seminar_art_id = ?", array($seminarArt->id));

		foreach ($seminare as $seminar) {
			$buchungen = Doctrine::getTable("Buchung")->findByDql("seminar_id = ?", array($seminar->id));
			foreach ($buchungen as $buchung) {
				$buchung->delete();
			}
			$seminar->delete();
		}

		$seminarArt->delete();
	}
}
