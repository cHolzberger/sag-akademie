<?php
/**
 */
class ReferentTable extends Doctrine_Table
{
    function detailed() {
	$q =  Doctrine_Query::create();
	$q->from("Referent referent")
		->leftJoin("referent.GeaendertVon user")
		->where("referent.id = ?");

	return $q;
    }
    function getAll() {
	$q =  Doctrine_Query::create();
	$q->from("Referent referent")
		->where("referent.id >= 1")
		->andWhere("referent.veroeffentlicht = 1")
		->orderBy('referent.name ASC');
	return $q;
    }
    function getRefs() {
	$q =  Doctrine_Query::create();
	$q->from("Referent referent")
		->where("referent.id >= 1")
		->orderBy('referent.name ASC');
	return $q;
    }
}