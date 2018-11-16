<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 *
 * liefert alle infos die fuer die neue startseite gebraucht werden zurueck
 */
include_once ("helpers.php");

class Pages_Startseite {

	/**
	 * Statistiken fuer die Startseite
	 * formated for DataSource.js
	 *
	 * @return array
	 */
	function getStatistics($table) {
		$cache = DBPool::$cacheDriver; 
		$cacheKey = "rpc_startseite_statistics";
		
		if ( ($data = $cache->fetch($cacheKey) )!==FALSE) {
			return $data;
		}
		
		$data = array();

		$startPM = date("Y-m-01 00:00:00", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
		$endPM = date("Y-m-t 00:00:00", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));

		$startPY = date("Y-m-01 00:00:00", mktime(0, 0, 0, 1, 1, date("Y") - 1));
		$endPY = date("Y-m-t 00:00:00", mktime(0, 0, 0, 31, 12, date("Y") - 1));

		$startTY = date("Y-m-01 00:00:00", mktime(0, 0, 0, 1, 1, date("Y")));
		$endTY = date("Y-m-t 00:00:00", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

		$startTM = date("Y-m-01 00:00:00");
		$endTM = date("Y-m-t 00:00:00");

		//  derzeitiger Monat
		$data['currentMonth']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startTM, $endTM);
		$data['currentMonth']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startTM, $endTM);
		$data['currentMonth']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startTM, $endTM);

		// letzter Monat
		$data['currentYear']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startTY, $endTY);
		$data['currentYear']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startTY, $endTY);
		$data['currentYear']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startTY, $endTY);

		// diese Jahr
		$data['lastMonth']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startPM, $endPM);
		$data['lastMonth']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startPM, $endPM);
		$data['lastMonth']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startPM, $endPM);

		// letztes Jahr
		$data['lastYear']['buchungen'] = Doctrine::getTable("Buchung")->countByDate($startPY, $endPY);
		$data['lastYear']['storno'] = Doctrine::getTable("Buchung")->countStornoByDate($startPY, $endPY);
		$data['lastYear']['umbuchungen'] = Doctrine::getTable("Buchung")->countUmbuchungenByDate($startPY, $endPY);

		$ret= array ( "table_name"=> $table , "data" => $data );
		$cache->save($cacheKey, $ret, null);
		return $ret;
	}

	/**
	 * Naechsten 4 Termine fuer Luenen und Darmstadt
	 *
	 * formated for DataSource.js
	 * @return array
	 */
	function getNextTermine($table) {
		$cache = DBPool::$cacheDriver; 
		$cacheKey = "rpc_" . __CLASS__."_".__FUNCTION__ . "_" . $table;
		
		if ( ($data = $cache->fetch($cacheKey) )!==FALSE) {
			return $data;
		}
		
		$count = 4;
		$array = array_merge(
		 $this->_getTermine(2, $count),
		 $this->_getTermine(1, $count)
		);

		$ret = array ( "table_name"=> $table , "data" => $array );
		$cache->save($cacheKey, $ret, null);
		return $ret;
	}

	private function _getTermine($standort_id, $count) {
		// 2->freigegebene termine
		$q = Doctrine::getTable("Seminar")->getNext($standort_id, $count, 1);
		$data = $q->fetchArray();

		foreach ($data as $k => $d) {
			$data[$k]['datum'] = mysqlDateToLocal ( $d['datum_begin'] );
		}
		return $data;
	}

	/**
	 * nicht erledigte aufgaben fuer den user auslesen und fuer darstellung auf der startseite bereitstellen
	 * @param int $userId
	 * @return array
	 */
	function getUserTodo($table) {
		$todo = array();
		$identity = Identity::getIdentity();
		$userId = $identity->getId();
		
		$cache = DBPool::$cacheDriver; 
		$cacheKey = "rpc_" . __CLASS__."_".__FUNCTION__ . "_" . $userId;
		
		//if ( ($data = $cache->fetch($cacheKey) )!==FALSE) {
		//	return $data;
		//}
		


		$_todo = Doctrine::getTable("XTodo")->getUserTodo($userId)->execute()->toArray(true);
		$count = count($_todo);

		for ( $i=0 ; $i < $count; $i++) {
			$todo[$i] = $_todo[$i];//->toArray();
			$todo[$i]['erstellt'] = mysqlDateToLocal ( $_todo[$i]['erstellt'] );
			$todo[$i]['kategorie'] = $_todo[$i]['Kategorie']['name'];
		}
		$ret = array ( "table_name"=> $table , "data" => $todo );
		$cache->save($cacheKey, $ret, null);
		return $ret;
	}

	function getTermineToday($table) {
		$now = currentMysqlDate();
		qlog(__CLASS__ . "::" . __FUNCTION__ .": ". $now);
		$cache = DBPool::$cacheDriver; 
		$cacheKey = "rpc_" . __CLASS__."_".__FUNCTION__ . "_" . $now;
		
		if ( ($data = $cache->fetch($cacheKey) )!==FALSE) {
			qlog("Using cached results");
			return $data;
		}
		
		$results = Doctrine::getTable("ViewSeminarPreis")->findByDql("datum_begin <= NOW() AND datum_ende >= NOW() AND status=?", array( 1) ); 		
		$ret = null;
		if ( is_object($results) && $results->count() > 0) {
			$data = $results->toArray();
			$ret= array("table_name" => $table, "data" => $data);
			qdir($data);
		} else {
			qlog("Keine Termine...");
			$ret= array("table_name" => $table, "data" => array());
		}
		
		$cache->save($cacheKey, $ret, null);
		return $ret;
	}

}