<?php

/**
 * SeminarArtReferenten
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class SeminarArtReferent extends BaseSeminarArtReferent
{
	public function setUp() {
		parent::setUp();

		$this->hasOne('Referent', array (
			'local'=>'referent_id',
			'foreign'=>'id'
			)
		);

		$this->actAs('Versionable', array(
		 'versionColumn' => 'version',
		 'className' => '%CLASS%Version',
		 'auditLog' => true
		 )
		);
		$this->actAs("ChangeCounted");
		$this->actAs("CreationLogged");

	}

	function getReferentenForSeminarIdTableProxy ($id, $standort_id) {
		$q  = Doctrine_Query::create();
		$q->from ( "SeminarArtReferent semRef");
		$q->leftJoin("semRef.Referent referent");
		$q->where ("semRef.seminar_art_id = ?", array($id));
		$q->andWhere ("semRef.standort_id = ?", array($standort_id));
		$q->orderBy("tag ASC, start_stunde ASC, start_minute ASC");
		
		return $q->fetchArray();
	}

	function getBySeminarId ($seminar_id, $referent_id) {
		$q = Doctrine_Query::create();
		$q->from ("SeminarArtReferent semRef");
		$q->where ("semRef.seminar_art_id = ?", $seminar_id);
		$q->andWhere ("semRef.referent_id = ?", $referent_id);

		if ( $q->count() > 0 ) {
			return $q->execute();
		} else {
			return NULL;
		}
	}

	/**
	* Loescht alle referenten des ensprechenden seminars
	* @param Int $seminar_id
	* @return String
	*/
	function clear($seminar_id, $tag) {
		$q = Doctrine_Query::create();
		$q->delete ("SeminarArtReferent semRef");
		$q->where ("semRef.seminar_art_id = ?", $seminar_id);
		$q->andWhere ( "semRef.tag = ?", $tag);
		
		return $q->execute();
	}
}