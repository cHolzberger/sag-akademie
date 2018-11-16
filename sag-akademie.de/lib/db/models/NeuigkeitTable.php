<?php
/**
 */
class NeuigkeitTable extends Doctrine_Table
{
 function detailed() {
	$q =  Doctrine_Query::create();
	$q->from("Neuigkeit neuigkeit")
		->leftJoin("neuigkeit.GeaendertVon user")
		->where("neuigkeit.id = ?");
	return $q;
    }
    function getLastNews() {
	$q =  Doctrine_Query::create();
	$q->from("Neuigkeit neuigkeit")
		->leftJoin("neuigkeit.GeaendertVon user")
		->where("neuigkeit.deleted_at = '0000-00-00 00:00:00'")
		->orderBy("neuigkeit.datum DESC")
		->limit(3);
	return $q;
    }
    function getNews() {
	$q =  Doctrine_Query::create();
	$q->from("Neuigkeit neuigkeit")
		->where("neuigkeit.deleted_at = '0000-00-00 00:00:00'");
	return $q;

    }
}