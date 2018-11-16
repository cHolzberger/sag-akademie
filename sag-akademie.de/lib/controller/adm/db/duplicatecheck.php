<?php
/**
 * Abstract base duplicateCheck
 */
class DuplicateCheck {
	function __construct($table, $duplicateTable) {
		$this->checkTable = $table;
		$this->duplicateTable = $duplicateTable;

	}
	// alias function to be ovverriden
	function addAlias($key, $data) {

	}

	function removeDuplicate($key, $obj) {

	}

	function findNextEntry($offset, $retkey) {
		$offset = $offset > 1?$offset-1:$offset;

		$tbl = Doctrine::getTable($this->checkTable);
		$limit = 1;

		$count = $tbl->count();
		$pages = ceil($count/$limit);

		for ($i = $offset; $i < $pages; $i++) {
			$entrys = $tbl->offsetFind($i, $limit)->execute();
			$cnt = array ();
			$duplicates = array (0);

			foreach ($entrys as $entry) {
				$q = $tbl->findDuplicates($entry);

				if ($q !== false && $q->count() >= 1) {
					$lduplicate = $q->execute();
					$duplicate = $lduplicate[0];
					
					if (!$this->isIgnored($entry->id, $duplicate->id)) {
						return array ($retkey=>$entry, "duplicate"=>$duplicate, "currentRecord"=>$i, "countRecords"=>$pages);
					}
				}
			}
		}
		return NULL;
	}

	/****
	 * event
	 */
	function mergeEntry() {
		if ( isset ($_GET['merge']) && isset ($_POST['ignore'])) {
			foreach ($_POST['ignore'] as $parentKey=>$child) {
				$this->ignoreDuplicate($parentKey, $child);
			}
		} else if ( isset ($_GET['merge']) && isset ($_POST['merge'])) {
			$tmp = array_keys($_POST['merge']);
			$parentKey = $tmp[0];
			$duplicateKey = array_keys($_POST['merge'][$parentKey]);
			$duplicateKey = $duplicateKey[0];

			$tbl = Doctrine::getTable($this->checkTable);
			// update Data
			if ( isset ($_POST['data'][$parentKey])) {
				$data = $_POST['data'];
				$entry = $tbl->find($parentKey);
				// alias definition of old names
				$this->addAlias($parentKey, $entry);
				$entry->merge($data[$parentKey]);
				$entry->save();
			}

			// delete dead entrys
			$dupl = $tbl->find($duplicateKey);
			$this->removeDuplicate($parentKey, $dupl);

			return true;
		}
		return false;
	}

	/**
	 * Ignore list handling
	 * @return
	 * @param object $id1
	 * @param object $id2
	 */
	function ignoreDuplicate($id1, $id2) {
		$cn = $this->duplicateTable;
		$ign = new $cn();
		$ign->kontakt_id1 = $id1;
		$ign->kontakt_id2 = $id2;
		$ign->save();

		$ign = new $cn();
		$ign->kontakt_id2 = $id1;
		$ign->kontakt_id1 = $id2;
		$ign->save();
	}

	function isIgnored($id1, $id2) {
		$ignore = Doctrine::getTable($this->duplicateTable)->findByDql("kontakt_id1 = ? AND kontakt_id2 = ?", array ($id1, $id2));
		return $ignore->count() != 0;
	}

}
/**
 * Kontakt Duplicate check
 */
class KontaktDuplicateCheck extends DuplicateCheck {
	function addAlias($newKey, $kontakt) {
		$kalias = new KontaktAlias();
		$kalias->kontakt_id = $newKey;
		$kalias->alias = $kontakt->firma;
		$kalias->save();
	}

	function removeDuplicate($newKey, $obj) {
		// delete and update
		$q = Doctrine_Query::create()
		->update()
		->from("Person")
		->set("kontakt_id","?",$newKey)
		->where("kontakt_id=?",$obj->id)
		->execute();
		
		$obj->delete();
	}
}

/**
 * Person Duplicate Check
 */
class PersonDuplicateCheck extends DuplicateCheck {
	function addAlias($newKey, $kontakt) {
		// no alias function for Personen
	}

	function removeDuplicate($newKey, $obj) {
		MosaikDebug::msg($newKey, "ParentKey");
		MosaikDebug::msg($obj->id, "ChildKey");
		// alle buchungen aus der DB holen
		$buchungen = Doctrine::getTable("Buchung")->findByPerson_id($obj->id);

		foreach ($buchungen as $buchung) {
			$buchung->person_id = $newKey;
			$buchung->save();
		}

		// buchungen aktualisieren und wenn wirklich keine mehr vorhanden sind loeschen
		$buchungen = Doctrine::getTable("Buchung")->findByPerson_id($obj->id);
		if ($buchungen->count() == 0)Doctrine::getTable("Person")->find($obj->id)->delete();
	}
}

/**
 * Buchungen Duplicate Check
 */
class BuchungDuplicateCheck extends DuplicateCheck {
	function addAlias($newKey, $kontakt) {
		// no alias function for Personen
	}

	function removeDuplicate($newKey, $obj) {
		if (!$obj) return;
		$hb = Doctrine::getTable("HotelBuchung")->findByBuchung_id($obj->id);
		foreach ($hb as $buchung) {
			if ($obj->NHotelBuchung->count() == 0) {
				$buchung->buchung_id = $newKey;
				$buchung->save();
			}
		}
		$obj->delete();
	}
}
/**
 * AkquiseKontakt Duplicate Check
 */
class AkquiseKontaktDuplicateCheck extends DuplicateCheck {
	function addAlias($newKey, $kontakt) {
		// no alias function for Personen
	}

	function removeDuplicate($newKey, $obj) {
		$obj->delete();
	}
}

class ADM_DB_DuplicateCheck extends SAG_Admin_Component {
	function construct() {
		$this->createPageReader();

		$this->dsDb = new MosaikDatasource("dbtable");
		$this->pageReader->addDatasource($this->dsDb);
	}

	function map($name) {
		return "ADM_DB_DuplicateCheck";
	}

	function forwardIframe($class, $namespace = "") {
		return $this->forward($class, $namespace);
	}

	function forward($class, $namespace = "") {
		$GLOBALS['path'][] = array ('name'=>$this->name(), 'url'=>$this->url(".."));
		$GLOBALS['path'][] = array ('name'=>$this->next(), 'url'=>$this->url());

		$name = $this->name();
		$this->entryId = $this->next();

		list ($config, $content) = $this->createPageReader();
		$content->addDatasource($this->dataStore);
		$content->addDatasource($this->dsDb);

		$this->dsDb->add("formaction", "/admin/db/".$this->name()."/".$this->entryId."?save");

		if ($this->entryId == "kontakte") {
			$this->onKontakte($content, $this->dsDb);
		} else if ($this->entryId == "personen") {
			$this->onPersonen($content, $this->dsDb);
		} else if ($this->entryId == "buchungen") {
			$this->onBuchungen($content, $this->dsDb);
		} else if ($this->entryId == "akquise") {
			$this->onAkquiseKontakte($content, $this->dsDb);
		} else {
			return $this->GET();
		}

		//$content->datasourceList->log();
		return $content->output->get();
	}

	/****
	 * http events
	 */
	function onPanel($pr, $ds) {
		$pr->loadPage("db/duplicateCheck.xml");
	}

	function GET() {
		$GLOBALS['path'][] = array ('name'=>$this->name(), 'url'=>$this->url());

		list ($config, $content) = $this->createPageReader();

		$content->addDatasource($this->dataStore);
		$content->addDatasource($this->dsDb);

		$this->onPanel($content, $this->dsDb);

		return $content->output->get();
	}

	function HEAD() {
		throw new k_http_Response(200);
	}

	/****
	 * event
	 */

	function onKontakte( & $pr, & $ds) {
		$merger = new KontaktDuplicateCheck("Kontakt", "KontaktDuplicateIgnore");

		if ($merger->mergeEntry()) {
			instantRedirect("/admin/db/".$this->name()."/".$this->entryId.";iframe?merge=".$_GET['merge']);
		}

		if ( isset ($_GET['merge'])) $offset = $_GET['merge'];
		else $offset = 0;
		$arr = $merger->findNextEntry($offset, "kontakt");

		if ($arr === NULL) {
			$pr->loadPage("db/duplicateCheck/kontakteKeineDuplikate.xml");
			return;
		}

		$kontakt = $arr['kontakt']->toArray();
		$duplicate = $arr['duplicate']->toArray();

		//MosaikDebug::msg($kontakt, "kontakt");
		//MosaikDebug::msg($duplicate, "dupl");
		//$kontakte = KontaktTable::findKontakts($offset, $limit)->execute();
		$cnt = array ();

		if (is_array($kontakt)) {
			$diff = array_diff_assoc($duplicate, $kontakt);

			if (array_key_exists("id", $diff) && array_key_exists("kontaktQuelleStand", $diff) && count($diff) == 2) {
				$diff['exact'] = 1;
			} else if (array_key_exists("id", $diff) && count($diff) == 1) {
				$diff['exact'] = 1;
			} else {
				$diff['exact'] = 0;
			}

			$diff['parent'] = $kontakt;
			$kontakt['duplicates'][] = $diff;
			$cnt[] = $kontakt;
		}
		//MosaikDebug::msg($cnt, "dupl");

		$this->dsDb->add("formaction", "/admin/db/".$this->name()."/".$this->entryId."?merge=".$arr['currentRecord']);
		$this->dsDb->add("nextLink", "/admin/db/".$this->name()."/".$this->entryId."?merge=".($arr['currentRecord']+3));

		$ds->add("KontaktDuplikate", $cnt);
		$ds->add("KontaktCount", $arr['countRecords']);
		$ds->add("KontaktCurrent", $arr['currentRecord']);
		$ds->add("KontaktNext", $arr['currentRecord']+1);
		$pr->loadPage("db/duplicateCheck/kontakte.xml");
	}

	/* Pesoinen dupolikate*/

	function onPersonen( & $pr, & $ds) {

		$merger = new PersonDuplicateCheck("Person", "PersonDuplicateIgnore");

		if ($merger->mergeEntry()) {
			instantRedirect("/admin/db/".$this->name()."/".$this->entryId.";iframe?merge=".$_GET['merge']);
		}

		if ( isset ($_GET['merge']))$offset = $_GET['merge'];
		else $offset = 0;
		$arr = $merger->findNextEntry($offset, "person");

		if ($arr === NULL) {
			return $pr->loadPage("db/duplicateCheck/personenKeineDuplikate.xml");

		}

		$person = $arr['person']->toArray();
		$duplicate = $arr['duplicate']->toArray();
		$cnt = array ();

		if (is_array($person)) {
			$diff = array_diff_assoc($duplicate, $person);

			$diff['exact'] = 0;

			if (array_key_exists("id", $diff) && count($diff) == 2) {
				$diff['exact'] = 1;
			}

			$diff['parent'] = $person;
			$person['duplicates'][] = $diff;
			$cnt[] = $person;
		}


		$this->dsDb->add("formaction", "/admin/db/".$this->name()."/".$this->entryId."?merge=".$arr['currentRecord']);
		$this->dsDb->add("nextLink", "/admin/db/".$this->name()."/".$this->entryId."?merge=".($arr['currentRecord']+2));

		$ds->add("PersonDuplikate", $cnt);
		$ds->add("PersonCount", $arr['countRecords']);
		$ds->add("PersonCurrent", $arr['currentRecord']);
		$ds->add("PersonNext", $arr['currentRecord']+1);
		$pr->loadPage("db/duplicateCheck/personen.xml");
	}

	/* Buchungen dupolikate*/

	function onBuchungen( & $pr, & $ds) {
		$merger = new BuchungDuplicateCheck("Buchung", "BuchungDuplicateIgnore");

		if ($merger->mergeEntry()) {
			instantRedirect("/admin/db/".$this->name()."/".$this->entryId.";iframe?merge=".$_GET['merge']);
		}

		if ( isset ($_GET['merge']))$offset = $_GET['merge'];
		else $offset = 0;
		$arr = $merger->findNextEntry($offset, "person");

		if ($arr === NULL) {
			$pr->loadPage("db/duplicateCheck/buchungenKeineDuplikate.xml");
			return;
		}

		$person = $arr['person']->toArray();
		$duplicate = $arr['duplicate']->toArray();
		$cnt = array ();

		if (is_array($person)) {
			$diff = array_diff_assoc($duplicate, $person);

			$diff['exact'] = 0;

			if (array_key_exists("id", $diff) && count($diff) == 2) {
				$diff['exact'] = 1;
			}

			$diff['parent'] = $person;
			$person['duplicates'][] = $diff;
			$cnt[] = $person;
		}


		$this->dsDb->add("formaction", "/admin/db/".$this->name()."/".$this->entryId."?merge=".$arr['currentRecord']);
		$this->dsDb->add("nextLink", "/admin/db/".$this->name()."/".$this->entryId."?merge=".($arr['currentRecord']+2));
		$ds->add("Buchung", $person);
		$ds->add("BuchungDuplikate", $cnt);
		$ds->add("BuchungCount", $arr['countRecords']);
		$ds->add("BuchungCurrent", $arr['currentRecord']);
		$ds->add("BuchungNext", $arr['currentRecord']+1);

		$pr->loadPage("db/duplicateCheck/buchungen.xml");
	}

	/** Akquise DB*/
	function onAkquiseKontakte( & $pr, & $ds) {
		$merger = new AkquiseKontaktDuplicateCheck("AkquiseKontakt", "AkquiseKontaktDuplicateIgnore");

		if ($merger->mergeEntry()) {
			instantRedirect("/admin/db/".$this->name()."/".$this->entryId.";iframe?merge=".$_GET['merge']);
		}

		if ( isset ($_GET['merge']))$offset = $_GET['merge'];
		else $offset = 0;
		$arr = $merger->findNextEntry($offset, "akquiseKontakt");

		if ($arr === NULL) {
			$pr->loadPage("db/duplicateCheck/akquiseKeineDuplikate.xml");
			return;
		}

		$kontakt = $arr['akquiseKontakt']->toArray();
		$duplicate = $arr['duplicate']->toArray();

		//MosaikDebug::msg($kontakt, "kontakt");
		//MosaikDebug::msg($duplicate, "dupl");
		//$kontakte = KontaktTable::findKontakts($offset, $limit)->execute();
		$cnt = array ();

		if (is_array($kontakt)) {
			$diff = array_diff_assoc($duplicate, $kontakt);

			if (array_key_exists("id", $diff) && array_key_exists("kontaktQuelleStand", $diff) && count($diff) == 2) {
				$diff['exact'] = 1;
			} else if (array_key_exists("id", $diff) && count($diff) == 1) {
				$diff['exact'] = 1;
			} else {
				$diff['exact'] = 0;
			}

			$diff['parent'] = $kontakt;
			$kontakt['duplicates'][] = $diff;
			$cnt[] = $kontakt;
		}
		//MosaikDebug::msg($cnt, "dupl");

		$this->dsDb->add("formaction", "/admin/db/".$this->name()."/".$this->entryId."?merge=".$arr['currentRecord']);
		$this->dsDb->add("nextLink", "/admin/db/".$this->name()."/".$this->entryId."?merge=".($arr['currentRecord']+2));

		$ds->add("AkquiseKontaktDuplikate", $cnt);
		$ds->add("AkquiseKontaktCount", $arr['countRecords']);
		$ds->add("AkquiseKontaktCurrent", $arr['currentRecord']);
		$ds->add("AkquiseKontaktNext", $arr['currentRecord']+1);
		$pr->loadPage("db/duplicateCheck/akquiseKontakte.xml");
	}
}
?>
