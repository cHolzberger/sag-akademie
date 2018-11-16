<?php
/**
 */
class SeminarArtNummerTable extends Doctrine_Table
{
  function getNextNumber($seminarArtId, $jahr) {
	$q = Doctrine_Query::create();

	$q->from ("SeminarArtNummer sn");
	$q->where("sn.seminar_art_id = ?", $seminarArtId);
	$q->andWhere("sn.jahr = ?", $jahr);

	if ( $q->count() > 0 ) {
	    $data = $q->execute();
	    $num = $data[0];
	    $num->nummer = $num->nummer + 1;
	    $num->save();

	    return $num->nummer;
	} else {
	    $sem = new SeminarArtNummer();
	    $sem->id =0;
	    $sem->nummer=1;
	    $sem->seminar_art_id = $seminarArtId;
	    $sem->jahr = $jahr;
	    $sem->save();

	    return $sem->nummer;
	}
    }
}