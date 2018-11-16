<?php
/**
* This class has been auto-generated by the Doctrine ORM Framework
*/
class BuchungTable extends Doctrine_Table {
    function setUp() {
	$this->actAs("BuchungTemplate");
	$this->actAs("SoftDelete");
    }

    public static function findDuplicates($data) {
	$personId = $data['person_id'];
	$kursNr = $data['seminar_id'];


	$q = Doctrine_Query::create()
	->select("k.*")
	->from('Buchung k')
	->where("k.person_id = ? AND k.seminar_id = ? AND k.id <> ? AND k.uuid = ? AND k.umbuchungs_datum = '0000-00-00' AND k.deleted_at = '0000-00-00 00:00:00'", array($personId, $kursNr, $data['id'], $data['uuid']))
	->orderBy("k.person_id ASC");
	return $q;
    }

    public static function offsetFind($offset=1, $limit=100) {
	$q = Doctrine_Query::create()
	->select("k.*")
	->from('Buchung k')
	->orderBy("k.id ASC")
	->where("k.deleted_at = '0000-00-00 00:00:00'")
	->limit($limit)
	->offset($offset);
	return $q;
    }
}
?>