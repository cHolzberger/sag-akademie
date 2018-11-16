<? 
class SeminarArtTemplate extends Doctrine_Template {
	public function preInsert(Doctrine_Event $event) {
		$id = $event->getInvoker()->id;
		clearCacheSeminarArt($id);
	}

	public function preUpdate(Doctrine_Event $event) {
		$id = $event->getInvoker()->id;
		clearCacheSeminarArt($id);
	}

	function getFutureSeminare() {
		$invoker = $this->getInvoker();

		$q = Doctrine_Query::create()
		->from('ViewSeminarPreis s')
		->innerJoin('s.Standort o WITH o.id = s.standort_id')
		->innerJoin('o.Ort r WITH r.plz = o.plz')
		->where('s.seminar_art_id = ?', $invoker->id)
		->andWhere('s.datum_begin > NOW()')
		->andWhere('s.freigabe_veroeffentlichen = 1')
		//->andWhere('s.ausgebucht = 0')
		->orderBy("s.datum_begin")
		->useResultCache(true, 3600, "seminar_art_future_{$invoker->id}");

		return $q->fetchArray();
	}

	/**
	 * gibt die nächsten Termine dieser SeminarArt zurück
	 * die keine Inhouse Seminare sind
	 */
	function getNextFour() {
		$invoker = $this->getInvoker();

		$q = Doctrine_Query::create()
		->from('ViewSeminarPreis s')
		->innerJoin('s.Standort o WITH o.id = s.standort_id')
		->innerJoin('o.Ort r WITH r.plz = o.plz')
		->where('s.seminar_art_id = ?', $invoker->id)
		->andWhere('s.datum_begin > NOW() ')
		->andWhere('s.freigabe_veroeffentlichen = 1')
		->andWhere("s.inhouse = ?", 0)
		//->andWhere('s.ausgebucht = 0')
		->orderBy("s.datum_begin")
		->useResultCache(true, 3600, "seminar_art_next_{$invoker->id}");
		//->limit("4");

		$info = $q->fetchArray();
		return $info;
	}

	/**
	 * gibt an ob es mehr seminare dieser art gibt die kein InhouseSeminar sind
	 */
	function hasMore() {
		$invoker = $this->getInvoker();
		$q = Doctrine_Query::create()
		->from('ViewSeminarPreis s')
		->innerJoin('s.Standort o WITH o.id = s.standort_id')
		->innerJoin('o.Ort r WITH r.plz = o.plz')
		->where('s.seminar_art_id = ?', $invoker->id)
		->andWhere('s.datum_begin > NOW() ')
		->andWhere('s.freigabe_veroeffentlichen = 1')
		//->andWhere('s.ausgebucht = 0')
		->andWhere("s.inhouse = ?", 0)
		
		->orderBy("s.datum_begin")
		->useResultCache(true, 3600, "seminar_art_more_{$invoker->id}");

		$count = $q->count();
		return $count > 4?1:0;
	}

	function getPlanungTableProxy() {
	    $q = Doctrine_Query::create();
	    $q->from ("ViewSeminarArtPreis art")
	    ->select("art.id, art.rubrik, art.dauer, art.bezeichnung, art.farbe, art.textfarbe, art.inhouse")
	    ->where("art.sichtbar_planung = 1")
		->orderBy("art.rubrik")
		->orderBy("id")
		->useResultCache(true, 3600, "seminar_art_planung");
	    return $q;
	}

	public static function detailedTableProxy($id="?") {
		$q = Doctrine_Query::create()
		->from('SeminarArt a')
		->leftJoin('a.GeaendertVon updater')
		->leftJoin('a.Seminare b')
		->leftJoin('b.Standort e')
		->leftJoin('e.Ort f')
		->leftJoin('a.Rubrik d')
		->leftJoin('a.Rubrik2 rubrik2')
		->leftJoin('a.Rubrik3 rubrik3')
		->leftJoin('a.Rubrik4 rubrik4')
		->leftJoin('a.Rubrik5 rubrik5')
		
		->leftJoin('a.Status g');
		if ( $id != "?" ) {
			 $q->where('a.id = ?', $id);
			 $q->useResultCache(true, 3600, "seminar_art_detail_$id");

		} else { 
			$q->where("a.id = ?");
		}

		return $q;
	}
	public static function detailedListTableProxy() {
		$q = Doctrine_Query::create()
	->select ("a.*, d.name, g.name")
		->from('SeminarArt a')
		->leftJoin('a.Rubrik d')
		->leftJoin('a.Rubrik2 rubrik2')
		->leftJoin('a.Rubrik3 rubrik3')
		->leftJoin('a.Rubrik4 rubrik4')
		->leftJoin('a.Rubrik5 rubrik5')
		->leftJoin('a.Status g');
		$q->useResultCache(true, 3600, "seminar_art_detaillist");

		return $q;
	}
	
	function setUp() {
		$this->hasMany('Seminar as Seminare', array (
		'foreign'=>'seminar_art_id',
		'local'=>'id'
		)
	);

		$this->hasMany('SeminarArtNummer as SeminarArtNummern', array (
		'foreign'=>'seminar_art_id',
		'local'=>'id'
		)
	);

		$this->hasOne('SeminarArtRubrik as Rubrik', array (
		'foreign'=>'id',
		'local'=>'rubrik'
		)
	);
	$this->hasOne('SeminarArtRubrik as Rubrik2', array (
		'foreign'=>'id',
		'local'=>'rubrik2'
		)
	);
	$this->hasOne('SeminarArtRubrik as Rubrik3', array (
		'foreign'=>'id',
		'local'=>'rubrik3'
		)
	);
	$this->hasOne('SeminarArtRubrik as Rubrik4', array (
		'foreign'=>'id',
		'local'=>'rubrik4'
		)
	);
	$this->hasOne('SeminarArtRubrik as Rubrik5', array (
		'foreign'=>'id',
		'local'=>'rubrik5'
		)
	);
	$this->hasOne('XUser as GeaendertVon', array (
		'local'=>'geaendert_von',
		'foreign'=>'id',
		)
	);

	$this->hasOne('SeminarArtStatus as Status', array (
		'foreign'=>'id',
		'local'=>'status'
		)
	);

	$this->hasMany("SeminarArtReferent as Referenten", array(
		"foreign" => "seminar_art_id",
		"local" => "id"
		)
	);

	$this->hasMany("SeminarArtZuKooperationspartner as Kooperationspartner", array( 
			"local" => "id",
			"foreign" => "seminar_art_id"
		));

	$this->actAs("ChangeCounted");
	

	}

	function hasFileTableProxy($fname) {
		$lname = "%$fname";
		$q = Doctrine_Query::create()->from("SeminarArt")->
			where ("info_link LIKE ? OR info_link2 LIKE ? OR info_link3 LIKE ?", array($lname, $lname, $lname));

		return $q->count() > 0;
	}
	
} 
?>