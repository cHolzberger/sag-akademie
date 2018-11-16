<?

class KontaktListener extends Doctrine_Record_Listener {
	public function preInsert(Doctrine_Event $event) {
		global $identity;


		if (is_object($identity)) {
			$event->getInvoker()->angelegt_user_id = $identity->uid();
			$event->getInvoker()->angelegt_datum = currentMysqlDatetime();
			
			$event->getInvoker()->geaendert_von = $identity->uid();
			$event->getInvoker()->geaendert = currentMysqlDatetime();
		}
		if (empty($event->getInvoker()->alias)) {
			$event->getInvoker()->alias = $event->getInvoker()->firma;
		}

		if ($event->getInvoker()->newsletter == 1) {
			$event->getInvoker()->newsletter_anmeldedatum = currentMysqlDate();
		} else {
			$event->getInvoker()->newsletter_abmeldedatum = currentMysqlDate();
		}

	}

	public function preUpdate(Doctrine_Event $event) {
		global $identity;
		//$event->getInvoker()->updated = date('Y-m-d', time());
		$mod = $event->getInvoker()->getModified();

		if (is_object($identity)) {
			$event->getInvoker()->geaendert_von = $identity->uid();
			$event->getInvoker()->geaendert = currentMysqlDatetime();
		}

		if (array_key_exists("newsletter", $mod)) {
			if ($event->getInvoker()->newsletter == 1) {
				$event->getInvoker()->newsletter_anmeldedatum = currentMysqlDate();
			} else {
				$event->getInvoker()->newsletter_abmeldedatum = currentMysqlDate();
			}
		}
	}

}

class KontaktTemplate extends Doctrine_Template {

	public function setUp() {
		$this->hasOne('XBranche as Branche', array('local' => 'branche_id', 'foreign' => 'id'));

		$this->hasOne('XTaetigkeitsbereich as Taetigkeitsbereich', array('local' => 'taetigkeitsbereich_id', 'foreign' => 'id'));

		$this->hasMany('Person as Personen', array('local' => 'id', 'foreign' => 'kontakt_id', "owningSide" => true));

		$this->hasOne('XUser as AngelegtVon', array('local' => 'angelegt_user_id', 'foreign' => 'id', ));

		$this->hasOne('XBundesland as Bundesland', array('local' => 'bundesland_id', 'foreign' => 'id', ));

		$this->hasOne('XLand as Land', array('local' => 'land_id', 'foreign' => 'id', ));

		$this->hasOne('XUser as GeaendertVon', array('local' => 'geaendert_von', 'foreign' => 'id', ));

		$this->hasMany('KontaktAlias as Alias', array('local' => 'id', 'foreign' => 'kontakt_id'));

		$this->hasOne('KontaktKategorie as Kategorie', array('local' => 'kontaktkategorie', 'foreign' => 'id'));

		$this->actAs("ChangeCounted");
		$this->addListener(new KontaktListener());

	}

	public function detailedTableProxy() {
		$q = Doctrine_Query::create()->from('Kontakt kontakt')->leftJoin('kontakt.GeaendertVon updater')->leftJoin('kontakt.Personen personen')->leftJoin('personen.Buchungen buchungen')->leftJoin('kontakt.Branche branche')->leftJoin('kontakt.Taetigkeitsbereich taetigkeitsbereich')->leftJoin('kontakt.Kategorie kategorie')->where('kontakt.id = ?');

		return $q;
	}

	function advancedSearchTableProxy($array, $kontext = "Kunde") {
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": ");
		qdir($array);

		$q = Doctrine_Query::create();

		$q->from("ViewKontakt a");
		//	MosaikDebug::msg($array, "Ausgabe:");
		$first = true;
		if ($kontext == "all") {
			$tmp_where = "";
			$tmp_args = array();
		} else {
			$tmp_where = "kontext = ?";
			$tmp_args = array($kontext);
		}

		$q->where("1 = 0");
		foreach ($array as $key => $value) {
			if ($value == "or") {
				if ($tmp_where != "kontext=?" || $tmp_where == "") {
					$q->orWhere($tmp_where, $tmp_args);
					if ($kontext == "all") {
						$tmp_where = "";
						$tmp_args = array();
					} else {
						$tmp_where = "kontext = ?";
						$tmp_args = array($kontext);
					}
					$first = true;
				}
			} else {
				$exp = explode(";", $value);
				$val_array = explode(":", $exp[0]);
				switch($val_array[1]) {
					case "string" :
						$exp[2] = "%" . $exp[2] . "%";
						break;
					case "date" :
						$exp[2] = mysqlDateFromLocal($exp[2]);
						break;
					case "datetime" :
						$exp[2] = mysqlDatetimeFromLocal($exp[2]);
						break;
				}
				$tmp_args[] = $exp[2];
				if ($first) {
					$tmp_where .= "a." . $val_array[0] . " " . $exp[1] . " ?";
					$first = false;
				} else {
					$tmp_where .= " AND a." . $val_array[0] . " " . $exp[1] . " ?";
				}
			}

		}
		$q->orWhere($tmp_where, $tmp_args);

		return $q;
	}

	public function getAnsprechpartner() {
		$invoker = $this->getInvoker();

		$q = Doctrine_Query::create();
		$q->from('Person person')->where("person.ansprechpartner <> 0")->andWhere("person.kontakt_id = ?", $invoker->id);

		return $q->execute()->getFirst();
	}

	public static function searchTableProxy($string, $kontext = "Kunde") {
		$string = strtolower(trim(utf8_decode($string)));

		if (strlen($string) == 2) {
			$q = Doctrine_Query::create()->from("ViewKontakt k")->where("k.kontext=? AND k.firma LIKE ?", array($kontext, $string));
		} else {
			$q = Doctrine_Query::create()->from("ViewKontakt k")->where("k.kontext =? AND k.firma LIKE ?", array($kontext, $string))->orWhere("k.kontext =? AND k.alias LIKE ?", array($kontext, $string))->orWhere("k.kontext =? AND k.email LIKE ?", array($kontext, $string));

			$data = explode(" ", $string);
			foreach ($data as $d) {
				$d = utf8_encode($d);

				if (!empty($d)) {
					$q->orWhere("k.kontext =? AND k.firma <> '' AND k.firma LIKE ?", array($kontext, "%{$d}%"));
					$q->orWhere("k.kontext =? AND k.alias <> '' AND k.alias LIKE ?", array($kontext, "%{$d}%"));
					$q->orWhere("k.kontext =? AND k.email <> '' AND k.email LIKE ?", array($kontext, "%{$d}%"));

				}
			}
		}

		return $q;
	}

	public static function searchAllTableProxy($string) {
		$string = strtolower(trim(utf8_decode($string)));

		if (strlen($string) == 2) {
			$q = Doctrine_Query::create()->from("ViewKontakt k")->where("k.firma LIKE ?", array($string));
		} else {
			$q = Doctrine_Query::create()->from("ViewKontakt k")->where("k.firma LIKE ?", array($string))->orWhere("k.alias LIKE ?", array($string))->orWhere("k.email LIKE ?", array($string));

			$data = explode(" ", $string);
			foreach ($data as $d) {
				$d = utf8_encode($d);

				if (!empty($d)) {
					$q->orWhere("k.firma <> '' AND k.firma LIKE ?", array("%{$d}%"));
					$q->orWhere("k.alias <> '' AND k.alias LIKE ?", array("%{$d}%"));
					$q->orWhere("k.email <> '' AND k.email LIKE ?", array("%{$d}%"));

				}
			}
		}

		return $q;
	}

	function detailSearchTableProxy($firma, $enclose = "", $kontext = "Kunde") {
		//$firma = utf8_decode ($firma);

		//$firma = str_replace(" ", "%", $firma);
		//$firma = str_replace("-", "%", $firma);

		//$firma = utf8_encode($firma);
		$firma = $enclose . $firma . $enclose;
		$q = Doctrine_Query::create()->from("ViewKontakt k")->where("k.kontext =? AND k.firma <> '' AND k.firma LIKE ? ", array($kontext, "%" . $firma))->orWhere("k.kontext =? AND k.alias <> '' AND k.alias LIKE ?", array($kontext, "%" . $firma))->orWhere("k.kontext =? AND k.email <> '' AND k.email LIKE ?", array($kontext, "%" . $firma))->orWhere("k.kontext =? AND k.firma <> '' AND k.firma LIKE ? ", array($kontext, $firma . "%"))->orWhere("k.kontext =? AND k.alias <> '' AND k.alias LIKE ?", array($kontext, $firma . "%"))->orWhere("k.kontext =? AND k.email <> '' AND k.email LIKE ?", array($kontext, $firma . "%"));

		return $q;
	}

	function detailSearchAllTableProxy($firma, $enclose = "") {
		//$firma = utf8_decode ($firma);

		//$firma = str_replace(" ", "%", $firma);
		//$firma = str_replace("-", "%", $firma);

		//$firma = utf8_encode($firma);
		$firma = $enclose . $firma . $enclose;
		$q = Doctrine_Query::create()->from("ViewKontakt k")->where("k.firma <> '' AND k.firma LIKE ? ", array("%" . $firma))->orWhere("k.alias <> '' AND k.alias LIKE ?", array("%" . $firma))->orWhere("k.email <> '' AND k.email LIKE ?", array("%" . $firma))->orWhere("k.firma <> '' AND k.firma LIKE ? ", array($firma . "%"))->orWhere("k.alias <> '' AND k.alias LIKE ?", array($firma . "%"))->orWhere("k.email <> '' AND k.email LIKE ?", array($firma . "%"));

		return $q;
	}

}
