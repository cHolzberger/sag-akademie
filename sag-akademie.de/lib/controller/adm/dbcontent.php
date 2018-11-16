<?php

class ADM_DBContent extends SAG_Admin_Component {
	var $ormMap = array (
	    "kontakte"=> array ("Kontakt", "kontakt"),
	    "personen"=> array ("Person", "person"),
	    "akquise"=> array ("AkquiseKontakt", "AkquiseKontakt"),

	    "seminare"=> array ("SeminarArt", "seminarArt"),
	    "buchungen"=> array ("Buchung", "buchung"),
	    "rechnungen"=> array ("Buchung", "buchung"),
	    "termine"=> array ("Seminar", "Seminar"),
	    "standorte"=> array ("Standort", "standort"),
	    "neuigkeiten"=> array ("Neuigkeit", "neuigkeit"),
	    "hotels"=> array ("Hotel", "hotel"),
	    "referenten"=> array ("Referent", "Referent"),
	    "mailings"=> array ("Mailing", "mailing"),
	    "statistiken"=> array ("Mailing", "mailing"),
	    "user"=> array ("XUser", "xuser"),

	);

	var $allowedIntentions = array (
	"save",
	"savePreise",
	"delete",
	"kontakt",
	"person",
	"ok",
	"preise",
	"search",
	"termin",
	"trash",
	"recover",
	"konvert",
	"print",
	 "sendInfo"
//	"edit"

	);

	var $prepareEdit = true; // legt fest ob die daten automatisch fuer forms geladen werden
	var $entryTable = "seminarArt";
	var $entryClass = "SeminarArt";
	var $fnkt = array ();
	var $dbClass = null;
	var $defaultIntention = "onShowList";

	function setDbClass() {
		if ($this->dbClass != null) {
			$this->ormMap[$this->name()] = array ($this->dbClass, strtolower($this->dbClass));
		}
	}

	function getTable() {
		return Doctrine::getTable($this->dbClass);
	}

	function map($name) {
		return "SAG_DBContent";
	}

	function mapIntention($intention) {
		return $intention;
	}
	
	function construct() {
		$this->setDbClass();


		$name = $this->name();

		if (array_key_exists($name, $this->ormMap)) {
			$this->entryTable = $this->ormMap[$name][1];
			$this->entryClass = $this->ormMap[$name][0];
		}

		$this->entryId = urldecode($this->next());
	}

	function init() {
		$this->createPageReader();
		$this->dsDb = new MosaikDatasource("dbtable");
		$this->pageReader->addDatasource($this->dsDb);
		$this->dTable = Doctrine::getTable($this->entryClass);
		$this->findIntention();
		if(isset($_GET['advancedSearch']) || $this->next() == "advancedSearch") {
                       $data = "";

                       if(isset($_POST['rules']))
                       {
                           foreach($_POST['rules'] as $key => $value) {
                            if($value == "or") {
                                $data .= "Oder ";
                            }else{
                               $exp = explode(";", $value);
                               $val_array = explode(":", $exp[0]);
                               if($exp[1] == "LIKE" ) {
                                $exp[1] = "enthält";
                               }
                               $data .= $val_array[0]. " ". $exp[1] ." ". $exp[2] ." | ";
                            }
                           }
                       }else{
                            $data = "Keine Suchkriterien.";
                       }
                       $this->dsDb->add("search", $data);
                }
                if ($this->query("q", false)) {
			$this->dsDb->add("search", utf8_encode(urldecode($_GET["q"])));
		}

		if ($this->query("a", false)) {
			$this->dsDb->add("search", utf8_encode(urldecode($_GET["a"])));
		}
	}

	function POST() {
		$this->init();
		$this->onSave($content, $this->dsDb);
	}

	function renderHtmlForward($class_name, $namespace) {
		$GLOBALS['firephp']->log("SAG_DBContent::renderHtmlForward");
		return $this->renderHtml();
	}

	function renderHtml() {
		$this->init();
		$name = $this->name();

		list ($config, $content) = $this->createPageReader();
		$this->content = &$content;
		$content->addDatasource($this->dataStore);
		$content->addDatasource($this->dsDb);

		$this->dsDb->add("dbtable", $this->entryTable);
		$this->dsDb->add("dbclass", $this->entryClass);

		$this->dsDb->add("formaction", "/admin/".$this->name()."/".urlencode($this->entryId)."?save");
		MosaikDebug::msg($this->intention, "DBContent::renderHtml");
		MosaikDebug::msg($this->allowedIntentions, "DBContent::allowedIntetions");

		if (in_array($this->intention, $this->allowedIntentions)) {
			$meth = "on".ucfirst($this->intention);
			$this->$meth($content, $this->dsDb);
		} else {

			switch($this->intention) {

				case "new":
					$this->prepareData();
					$GLOBALS['path'][] = array ('name'=>"Neu", 'url'=>$this->url()."/".$this->next());
					$this->onNew($content, $this->dsDb);
				break;
				case "edit":
					if ( $this->prepareEdit ) {
						$this->prepareData();
						$GLOBALS['path'][] = array ('name'=>"Bearbeiten", 'url'=>$this->url()."/".$this->next());
					}
					$this->onEdit($content, $this->dsDb);
				break;
				case "show":
					$this->onShowDetail($content, $this->dsDb);
				break;
				case "list":
					$this->onShowList($content, $this->dsDb);
				break;
				case "konvert":
					$this->onKonvert($content, $this->dsDb);
				break;
			    	case "print":
					$this->onPrint($content, $this->dsDb);
				break;
				default:
					$meth = $this->defaultIntention;
					$this->$meth($content, $this->dsDb);
				break;
			}
		}

		return $content->output->get();
	}

	function findIntention() {
		if ( isset ($_GET['save'])) {
			$this->intention = "save";
		} else if ($this->next() == "new") {
			$this->intention = "new";
		} else if ( isset ($_GET['delete'])) {#new page 
			$this->intention = "delete";
		} else if ( isset ($_GET['kontakt'])) {#delete page 
			$this->intention = "kontakt";
		} else if ( isset ($_GET['person'])) {#kontakt page 
			$this->intention = "person";
		} else if ( isset ($_GET['ok'])) {#ok page 
			$this->intention = "ok";
		} else if ( isset ($_GET['edit'])) {#person page 
			$this->intention = "edit";
		} else if ( isset ($_GET['show'])) {#show 
			$this->intention = "show";
		} else if ( isset ($_GET['preise'])) {
			$this->intention = "preise";
		} else if ( isset ($_GET['savePreise'])) {
			$this->intention = "savePreise";
		} else if ( isset ($_GET['list'])) {
			$this->intention = "list";
		} else if ( isset ($_GET['termin'])) {
			$this->intention = "termin";
		} else if ( isset ($_GET['trash']) || $this->next() == "trash") {
			$this->intention = "trash";
		} else if ( isset ($_GET['search']) || isset ($_GET['q']) || isset ($_GET['a']) || isset($_GET['advancedSearch']) || $this->next() == "advancedSearch") {
                        $this->intention = "search";
		} else if ( isset($_GET['recover'] )) {
			$this->intention = "recover";
		} else if ( isset($_GET['print'] )) {
			$this->intention = "print";
		} else if ( isset($_GET['sendInfo'] )) {
			$this->intention = "sendInfo";
	    } else if ( isset($_GET['konvert'] )) {
			$this->intention = "konvert";
		} else {
			$this->intention = "showList";
		}

		$this->intention = $this->mapIntention($this->intention);
		//$GLOBALS['firephp']->log($this->intention, "intention");
	}

	/*** filters new or editable data 
	 only called when prepareData is called
	 ***/
	function filterData($result) {
		return $result;
	}

	/*** prepares data for edit or new data 
	 * called onEdit and onNew
	 * @return mixed Result
	 */
	function prepareData() {
		$result = array ();
		switch($this->intention) {
			case "edit":
				$result = $this->getOne($this->entryId, false);
			break;
			case "new":
				$result = $this->getOneClass($this->entryId)->toArray();
			break;
			default:
				$result = $this->getOne($this->entryId);
			break;
		}

		$result = $this->filterData($result);

		$this->dsDb->add($this->entryClass, $result);
		$this->dsDb->add($this->entryTable, $result);
		$this->dsDb->log();
		return $result;
	}

	function dispatch() {
		$this->addBreadcrumb();
		return parent::dispatch();
	}

	function onRecover ( &$pr, &$ds) {
		instantRedirect("/admin/".$this->name().";iframe");
	}
	
	function onOk( & $pr, & $ds) {
		$fn = $this->name()."/ok.xml";
		$pr->loadPage($fn);
		instantRedirect("/admin/".$this->name().";iframe");
	}

	function onPerson( & $pr, & $ds) {
		$fn = $this->name()."/person.xml";
		$pr->loadPage($fn);
		instantRedirect("/admin/".$this->name().";iframe");
	}

	function onKontakt( & $pr, & $ds) {
		$fn = $this->name()."/kontakt.xml";
		$pr->loadPage($fn);
		instantRedirect("/admin/".$this->name().";iframe");
	}

	function onDelete( & $pr, & $ds) {
		$fn = $this->name()."/delete.xml";
		$this->delete();
		$pr->loadPage($fn);
		instantRedirect("/admin/".$this->name()."/delete;iframe");
	}

	function onPreise( & $pr, & $ds) {
		$fn = $this->name()."/preise.xml";
		$pr->loadPage($fn);
	}

	function onPrint( & $pr, & $ds) {
		$this->prepareData();

		$fn = $this->name()."/print.xml";
		$pr->loadPage($fn);
	}

	function onSavePreise( & $pr, & $ds) {
		$fn = $this->name()."/preise.xml";
		$pr->loadPage($fn);
	}

	function onEdit( & $pr, & $ds) {
		$fn = $this->name()."/edit.xml";
		$pr->loadPage($fn);
	}

	function onNew( & $pr, & $ds) {
		$fn = $this->name()."/edit.xml";
		$this->dsDb->add("formaction", "/admin/".$this->name()."/".urlencode($this->entryId)."?save");

		$pr->loadPage($fn);
	}

	function onTrashcan( & $pr, & $ds) {
		$fn = $this->name()."/trash.xml";
		$this->dsDb->add("formaction", "/admin/".$this->name()."/".urlencode($this->entryId)."?save");

		$pr->loadPage($fn);
	}

	function onShowList( & $pr, & $ds) {
		$GLOBALS['dbtableDataFetch'] = $this;
		$pr->loadPage($this->name().".xml");
	}

	function onShowDetail( & $pr, & $ds) {
		$fn = $this->name()."/show.xml";
		$pr->loadPage($fn);
	}

	function onSearch( & $pr, & $ds) {
		//$this->dsDb->add("Results", $this->getData($this->entryTable));
		$fn = $this->name()."/search.xml";
		$pr->loadPage($fn);
	}

	function onTermin( & $pr, & $ds) {
		$this->dsDb->add("Results", $this->getData($this->entryTable));

		$fn = $this->name()."/termin.xml";
		$pr->loadPage($fn);
	}

	function onSave( & $pr, & $ds) {
		$result = $this->getOneClass($this->entryId);
		$result->merge($_POST[$this->entryTable]);
		$result->save();
		$this->entryId = $result->id;
		// no need for it
		//$fn =  $this->name() . "/saved.xml";
		//$pr->loadPage( $fn );
		//return ""; // seite wird im framework neu geladen!
		instantRedirect("/admin/".$this->name()."/". $this->entryId .";iframe?edit"); // FIXME: iframe nur wenn auch nen iframe vorhanden ist
	}

	/***
	 * CALLBACK for dbform and foreach
	 */
	function getData($table) { /* callback for the dbtable module */
		MosaikDebug::msg($table, "getData");
		if (array_key_exists("by", $_GET)) {
			$fnkt = "findBy".$this->query('by');
			$table = Doctrine::getTable($table);
			$result = $table->$fnkt($this->query('value'));
		} else if ($this->query("a", false)) {
			$hits = Doctrine::getTable($table)->search($this->query("a")."%");
			$ids = array ();

			if (count($hits) < 1) {
				return array ();
			}

			foreach ($hits as $hit) {
				$ids[] = $hit['id'];
			}

			$q = Doctrine::getTable($table)->detailedIn($ids);
			$result = $q->fetchArray();

		} else if ($this->query("q", false)) {
			//MosaikDebug::msg($table, "Search in");
			//MosaikDebug::msg($this->query("q"), "Search For");
			$hits = Doctrine::getTable($table)->search(utf8_encode(urldecode($this->query("q"))));
			$ids = array ();

			if (count($hits) < 1) {
				return array ();
			}

			foreach ($hits as $hit) {
				$ids[] = $hit['id'];
			}

			$q = Doctrine::getTable($table)->detailedIn($ids);
			$result = $q->fetchArray();
		} else {
			$result = Doctrine::getTable($table)->findAll();
		}
		return $result;
	}

	/***
	 * misc funktions
	 */
	function delete() {
		MosaikDebug::msg($this->entryId, "delete");
		$row = $this->dTable->find($this->entryId);
		if (is_object($row)) $row->delete();
	}

	function fetchOne($id, $refresh = false) {
		MosaikDebug::msg($id, "fetchOne");
		$result = $this->dTable->find($this->entryId);
		if (!$result) return array (); // FIXME: trhow an error!
		if ($refresh)$result->refreshRelated();
		return $result->toArray($refresh);
	}

	function getOneClass($id) {
		MosaikDebug::msg($id, "getOneClass");
		$cl = $this->entryClass;
		if ($id == "new") return new $cl(); // FIXM
		return $this->dTable->find($this->entryId);
	}

	function getOne($id, $refresh = false) {
		MosaikDebug::msg($id, "getOne");

		$result = $this->fetchOne($id, $refresh);
		if (!count($result)) {
			instantRedirect("/admin/".$this->name().";iframe");// FIXME: iframe nur wenn auch nen iframe vorhanden ist
		}

		return $result;
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
}
?>