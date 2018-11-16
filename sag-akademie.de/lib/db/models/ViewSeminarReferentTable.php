<?php

/**
 */
class ViewSeminarReferentTable extends Doctrine_Table {

	function findReferentenByTag($seminarId, $tag) {
		$q = Doctrine_Query::create()->select("vsr.kuerzel")
		 ->from("ViewSeminarReferent vsr")
		 ->select("vsr.kuerzel")
		 ->where("vsr.id = ?")
		 ->andWhere("vsr.tag = ?");
		$ar = $q->fetchArray(array($seminarId, $tag));
		if (count($ar) > 0) {
			return $ar[0]['kuerzel'];
		} else {
			return "#fehler " . $seminarId . " - " . $tag;
		}
	}

	function findReferentenBySeminar($seminarId, $standort_id=0) {
		$q = Doctrine_Query::create()->select("vsr.kuerzel")
		 ->from("ViewSeminarReferent vsr INDEXBY ViewSeminarReferent.tag")
		 ->select("vsr.kuerzel, vsr.tag")
		 ->where("vsr.id = ?");
		 if ( $standort_id!=0) {
		 	$q->andWhere("vsr.standort_id=?",$standort_id);
		 }
		$ar = $q->fetchArray(array($seminarId));
		return $ar;
	}

}
