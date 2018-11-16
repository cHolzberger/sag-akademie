<?php
/**
 */
class ViewSeminarReferentDatumTable extends Doctrine_Table
{
    function getReferentColHits() {
	 $q =  Doctrine_Query::create();
	    $q->from("ViewSeminarReferentDatum a")
		    ->where("a.datum = ?")
		    ->andWhere("a.referent_id = ?");
	    MosaikDebug::msg($q->getSql(), "Query");
	    return $q;
     }
}