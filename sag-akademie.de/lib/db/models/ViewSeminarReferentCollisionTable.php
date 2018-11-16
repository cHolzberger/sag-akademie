<?php
/**
 */
class ViewSeminarReferentCollisionTable extends Doctrine_Table
{
 function getReferentCol() {
     $q =  Doctrine_Query::create();
	$q->from("ViewSeminarReferentCollision referentcol")
		->where("referentcol.hit > '1'");
	return $q;
 }
}