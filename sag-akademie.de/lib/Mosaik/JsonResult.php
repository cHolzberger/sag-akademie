<?php
/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
require_once ("controller/json/_tableHeaders.php");
/**
 * Description of JsonResult
 *
 * @author molle
 */
class MosaikJsonResult {
	public $q;
	public $headers;
	public $perPage = 1500;
	public $headline = "Unbekannt";
	public $cacheId;
	public $nocache = false;
	/**
	 * speichert eine gruppe von filtern die auf die daten angewendet werden
	 * um diese zu erweitern oder abzuaendern
	 * @var
	 */
	public $filter = null;

	function __construct($cacheId = null) {
		if ($cacheId == null) {
			$this->nocache = true;
			$this->cacheId = md5(microtime());
		} else {
			$this->cacheId = $cacheId;
		}
	}

	function setCacheId($id) {
		$this->cacheId = $id;
	}
	
	function setNocache($b) {
		$this->nocache = $b;
	}

	/**
	 *
	 * rendert genau einen treffer
	 */
	function renderSingle() {
		$data = array();

		$info = array();

		$count = $this->q->count();

		if ($count == 0)
			return json_encode(array("error" => true, "message" => "JsonResult count is zero"));

		$info['headline'] = $this->headline;
		$info['pages'] = ceil($count / $this->perPage);
		$info['count'] = $count;
		$info['mode'] = "single";
		$info['headers'] = $this->headers;
		$info['data'] = $this->q->fetchArray();
		$info['data'] = $info['data'][0];

		if ($this->filter)
			$this->filter->extend($info['data']);

		return json_encode($info);

	}

	/**
	 * rendert alle ergebnisse sofort
	 */
	function renderAll($baseTable="") {
		//    	return $this->render();

		if (!is_object($this->q)) {
			throw new Exception("Error Processing Request: No Query set!", 1);

		}
		qlog(__CLASS__ . "::". __FUNCTION__);
		$data = array();
		$info = array();

		qlog(__CLASS__ . "::" . __FUNCTION__.": joining tables:");
		$refTables = $this->map_leftjoin($baseTable);
		$found = array();
		for ($i = 0; $i < count($refTables); $i++) {
			if ( array_search($refTables[$i], $found) === false) {
				if ( empty($baseTable)) {
					qlog(__CLASS__ . "::" . __FUNCTION__.": ERROR -> renderAll used without base table and with aliases");
				}
				$this->q->leftJoin($refTables[$i]);
				array_push($found, $refTables[$i]);
				qlog(__CLASS__ . "::" . __FUNCTION__.": {$refTables[$i]}");
			}
			
		}
	
		qlog(__CLASS__ . "::" . __FUNCTION__.": Mapping fieldnames");
		$this->q->select(implode(", ", $this->map_fieldnames($baseTable)));
		
		
		if ($this->nocache) {
			qlog(__CLASS__ . "::" . __FUNCTION__.": not using result cache");
			$this->q->useResultCache(null);
		} else {
			qlog(__CLASS__ . "::" . __FUNCTION__.": Setting resultcache id: json_" . $this->cacheId);
			$this->q->useResultCache(true, 3600, "json_" . $this->cacheId);
		}
		//qlog(__CLASS__ . "::" . __FUNCTION__.": using sql:");
		//qlog($this->q->getDql());
		//qlog($this->q->getSqlQuery());
		
		qlog(__CLASS__ . "::" . __FUNCTION__.": couting results");
		$count = $this->q->count();
		$info['headline'] = $this->headline;
		$info['pages'] = ceil($count / $this->perPage);
		$info['count'] = $count;
		$info['mode'] = "all";
		$info['headers'] = $this->headers;
		qlog(__CLASS__ . "::" . __FUNCTION__.": got " . $count . " results");
		
		
		$info['data'] = $this->q->fetchArray();

		if ($this->filter)
			for ($i = 0; $i < $count; $i++) {
				$this->filter->extend($info['data'][$i]);
			}

		return json_encode($info);
	}

	/**
	 *
	 * paging modus rendert bei der ersten anfrage nur info wie viele ergebnisse vorhanden sind
	 * wenn $_GET['page'] gesetzt ist wird die seite mit daten ausgegeben
	 */
	function render($baseTable="") {
		if (!is_object($this->q)) {
			throw new Exception("Error Processing Request: No Query set!", 1);

		}
		$data = array();
		$page = 0;

		if (MosaikConfig::getEnv('page')) {
			$page = MosaikConfig::getEnv('page');
		}
		$info = array();
		$count = -1;
		$info['mode'] = "paged";
		// this is important it maps fieldnames like Hotel:name to Hotel_name
		// also it returns a query to be used against the referenced table
		$refTables = $this->map_leftjoin($baseTable);
		for ($i = 0; $i < count($refTables); $i++) {
			$this->q->leftJoin($refTables[$i]);
		}

		$this->q->select(implode(", ", $this->map_fieldnames($baseTable)));

		$this->q->useResultCache(true, 3600, "json_" . $this->cacheId);
		if ($page != 0) {
			$this->q->limit($this->perPage);
			$this->q->offset(($page - 1) * $this->perPage);
			$info['data'] = $this->q->fetchArray();

			if ($this->filter)
				for ($i = 0; $i < count($info['data']); $i++) {
					$this->filter->extend($info['data'][$i]);
				}

			$info['page'] = intval($page);
		} else {
			$count = $this->q->count();
			$info['headline'] = $this->headline;
			$info['pages'] = ceil($count / $this->perPage);
			$info['count'] = $count;
			$info['headers'] = $this->headers;
		}

		return json_encode($info);
	}

	/**
	 * resolves relations in table definitions
	 * e.g. Hotel:name -> Hotel_name
	 *
	 * etc.
	 *
	 * the alias has to be defined in the table defintion
	 */
	function map_fieldnames($baseTable) {
		$ret = array();

		for ($i = 0; $i < count($this->headers); $i++) {
			$name = $this->headers[$i]['field'];
			$expl = explode(":", $name);
			if (count($expl) == 1) {
				$ret[] = "$name";
			} else {
				$name = str_replace(":", "_", $name);
				$field = array($baseTable);
				for ($j = 0; $j < count($expl); $j++) {
					$field[] = $expl[$j];
				}

				$ret[] = join(".", $field) . " as " . $name;
			}
			// correct the alias Hotel:name will be Hotel_name
			$this->headers[$i]['d_field'] = $this->headers[$i]['field'];
			$this->headers[$i]['field'] = str_replace(":", "_", $this->headers[$i]['field']);
		}

		return $ret;
	}

	/**
	 * joins tables for field names like
	 * "HotelBuchung.datum" -> $q->leftJoin("HotelBuchung")
	 * "HotelBuchung:Hotel:name" -> $q->leftJoin("HotelBuchung.Hotel")
	 */
	function map_leftjoin($baseTable) {
		$ret = array();

		for ($i = 0; $i < count($this->headers); $i++) {
			$name = $this->headers[$i]['field'];
			$expl = explode(":", $name);
			if (count($expl) == 1) {
				continue;
			}

			if (count($expl) == 2) {
				$ret[] = $baseTable.".".$expl[0];
				continue;
			}

			$field = array($baseTable);
			for ($j = 0; $j < (count($expl) - 1); $j++) {
				$field[] = $expl[$j];
			}

			$ret[] = join(".", $field);
		}

		return $ret;
	}

}
?>
