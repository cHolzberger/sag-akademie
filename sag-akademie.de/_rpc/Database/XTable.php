<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 *
 * liefert alle infos die fuer die neue startseite gebraucht werden zurueck
 */
include_once ("helpers.php");
include_once ("controller/json/_tableHeaders.php");

class Database_XTable {

	/**
	 * returns the data from table with name x_$Name (name=dropdown => XDropdown)
	 * used for dropdown data and generic lists
	 *
	 * provides access to all tables, converts some information
	 * see definition on how the table data is mapped to new field names
	 *
	 * @param string $name
	 * @return array
	 */
	function fetchAll($name) {
		
		
		switch ($name) {
			case "InhouseSeminarArt":
				$table_name = "ViewInhouseSeminarArt";
				break;
			default:
				$table_name = $name;
				break;
		}
		
		$cache = DBPool::$cacheDriver; 
		$cacheKey = "rpc_".__CLASS__."_".__FUNCTION__."_".$name;
		
		if ( ($data = $cache->fetch($cacheKey) )!==FALSE) {
			return $data;
		}

		$hasName = Doctrine::getTable($table_name)->hasColumn("name");
		
		$q = Doctrine_Query::create()->from($table_name);
		if ( $hasName ) {
			$q->orderBy('name');
		}
		
		$array = $q->fetchArray();
		//$table = Doctrine::getTable($table_name);
		//$data = $table->findAll();
		//$array = $data->toArray(true);
		$ret = array();

		switch ($name) { // compat wraparound
			case "XGrad":
				for ($i = 0; $i < count($array); $i++) {
					$tmp = array();
					$tmp['id'] = $i + 1;
					$tmp['name'] = $array[$i]['id'];
					$ret[] = $tmp;
				}
				break;
			case "ViewStandortKoordinaten":
				for ($i = 0; $i < count($array); $i++) {
					$tmp = $array[$i];
					$tmp['name'] = $array[$i]['standort_name'];
					$ret[] = $tmp;
				}
				break;
			case "XUser":
				for ($i = 0; $i < count($array); $i++) {
					$tmp = array();
					$tmp['id'] = $array[$i]['id'];
					$tmp['name'] = $array[$i]['name'] . ", " . $array[$i]['vorname'];
					$ret[] = $tmp;
				}
				break;
			case "SeminarArt":
				for ($i = 0; $i < count($array); $i++) {
					if ($array[$i]['inhouse'] == "1")
						continue;

					$tmp = $array[$i];
					$tmp['name'] = $array[$i]['id'];
					$tmp['fk1'] = $array[$i]['rubrik'];

					$ret[] = $tmp;
				}
				break;
			case "InhouseSeminarArt":
				for ($i = 0; $i < count($array); $i++) {
					$tmp = $array[$i];
					$tmp['name'] = $array[$i]['id'];
					$tmp['fk1'] = $array[$i]['rubrik'];

					$ret[] = $tmp;
				}
				break;
			case "SeminarArtRubrik":
				for ($i = 0; $i < count($array); $i++) {
					$tmp = array();
					$tmp['id'] = $array[$i]['id'];
					$tmp['name'] = $array[$i]['name'];
					$tmp['info1'] = $array[$i]['aktiv'];
					$ret[] = $tmp;
				}
				break;
			case "Standort": 
				for ($i = 0; $i < count($array); $i++) {
					// Inhouse ausschliessen
					if ( $array[$i]['id'] == -1) continue;
					$ret[] = $array[$i];
				}
				
				break;
			default:
				$ret = $array;
				break;
		}

		
		$ret = array("table_name" => $name, "data" => $ret);
		$cache->save($cacheKey, $ret, null);
		return $ret;
	}

	function save($table, $id, $values) {
		
	}

	function setData($name, $values) {
		
	}

	/**
	 * gets the version info for all tables stored inside the version table
	 * 
	 * @return array 
	 */
	function getVersions() {
		$q = Doctrine_Query::create();
		$q->from("XTableVersion INDEXBY table_name");
		
		$data = $q->fetchArray();
		
		return $data;
	}

	/**
	 * version management for table data
	 *
	 * @param string $table
	 * @return object
	 */
	function getVersion($table) {
		$info = Doctrine::getTable("XTableVersion")->find($table);

		if (!is_object($info)) {
			$info = new XTableVersion();
			$info->table_name = $table;
			$info->version = 1;
			$info->last_change = currentMysqlDatetime();
			$info->save();
			return $info->toArray();
		} else {
			return $info->toArray();
		}
	}

	/**
	 * Update table version
	 * 
	 * @param string $table
	 * @param string $version 
	 */
	function setVersion($table, $version) {
		$info = Doctrine::getTable("XTableVersion")->find($table);

		if (!is_object($info)) {
			$info = new XTableVersion();
			$info->table_name = $table;
			$info->version = $version;
			$info->last_change = currentMysqlDatetime();
			$info->save();
		} else {
			$info->version = $version;
			$info->last_change = currentMysqlDatetime();
			$info->save();
		}
	}

	function getIgnoredColumns( $table ) {
		$reqInfo = array();
		$reqInfo['ViewAkquiseKontaktR']['badFields'] = array('id', 'kontakt_id', 'quelle_id','land_id', 'bundesland_id', 'angelegt_user_id');
		$reqInfo['ViewAkquise']['badFields'] = array('id', 'kontakt_id', 'quelle_id');
		$reqInfo['ViewKontakt']['badFields'] = array('id', 'land_id', 'bundesland_id', 'angelegt_user_id');
		$reqInfo['ViewPerson']['badFields'] = array('id', 'kontakt_id', 'angelegt_user_id', 'land_id', 'bundesland_id');
		$reqInfo['ViewSeminarPreis']['badFields'] = array('id', 'standort_id', 'verlegt_seminar_id', 'freigabe_status', 'sichtbar_planung', 'freigabe_veroeffentlichen', 'abgeschlossen');
		$reqInfo['ViewBuchungPreis']['badFields'] = array('id', 'seminar_id', 'packet_id', 'person_id', 'umgebucht_id', 'angelegt_user_id', 'bildungscheck_ausstellung_bundesland_id');
		
		return $reqInfo[$table]['badFields'];
	}
	
	function getTableColumns($table) {
		global $_tableHeaders;
		
		qlog(__CLASS__ . "::" . __FUNCTION__ . ": table => $table");
		
		$columns = Doctrine::getTable($table)->getColumns();
		
		$_th = array_key_exists($table, $_tableHeaders) ? $_tableHeaders[$table] : null;
		$th = array();
		if ( $_th ) {
			foreach ( $_th as $key => $item ) {
				$th[$item['field']] = $item;
			}
		}

		$badFields = $this->getIgnoredColumns ($table);
		
		ksort($columns);
		
		$data = array();
		
		foreach ( $columns as $key => $val) {
			if(@ !in_array($key, $badFields)) {
				$_d = array();
				$_d['col'] = $key;
				$_d['type'] = $val["type"];
				if ( array_key_exists( $key, $th) ) {
					$_d['label'] = $th[$key]['label'];
				} else {
					$_d['label'] = $key;
				}
				$_d['value'] = $key;
 			
				$data[] = $_d;
			}
		}
		
		return $data;
	}

}