<?php
/***
 * Inhouse Seminare anlegen / löschen und verschieben
 *
 */

include_once ("services/PlanungService.php");
include_once ("Events/SeminarUpdateEvent.php");

/**
 *
 * Chagelog:
 *
 * 07.06.2009 Inital code
 *
 *
 * @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 * @copyright 2009 MOSAIK Software - http://www.mosaik-software.de
 */
class JSON_Jahresplanung extends k_Component {

	function map() {
		return "JSON_Jahresplanung";
	}

	function renderJson() {

	}

	function POST() {
		qlog(__CLASS__ . "::" . __METHOD__);
		$postData = json_decode(stripslashes($_POST['data']), true);

		switch (json_last_error()) {
			case JSON_ERROR_DEPTH :
				qlog('JSON-Error - Maximale Stacktiefe überschritten');
				break;
			case JSON_ERROR_CTRL_CHAR :
				qlog('JSON Error - Unerwartetes Steuerzeichen gefunden');
				break;
			case JSON_ERROR_SYNTAX :
				qlog('JSON Error - Syntaxfehler, ungültiges JSON');
				break;
		}
		$data = $postData['data'];
		$notizen = $postData['notizen'];
		$jahr = $postData['year'];
		$delete = $postData['del'];
		$known = Array();
		$jsonResponse = Array();
		$jsonResponse['del'] = Array();
		$jsonResponse['new'] = Array();
		$jsonResponse['change'] = Array();
		$jsonResponse['debug'] = $postData;

		$statusCodes = Doctrine::getTable("SeminarFreigabestatus");
		$codes = $statusCodes->findAll(Doctrine::HYDRATE_ARRAY);

		foreach ($codes as $code) {// this sucks, GROUP BY mysql ist besser
			$codes[$code['flag']] = $code;
		}

		MosaikDebug::msg($delete, "delete");
		if (is_array($delete)) {
			foreach ($delete as $d_id) {
				$tmp = array();
				MosaikDebug::msg($d_id, "delete");
				$tmp['id'] = $d_id;
				$dbTermin = Doctrine::getTable("Seminar")->find($d_id);
				// fixme alle abhaengigen loeschen -> ueber doctrine

				if (is_object($dbTermin)) {
					$tmp['status'] = "deleted";
					$dbTermin->delete();
				} else {
					$tmp['status'] = 'notfound';
				}
				$jsonResponse['del'][] = $tmp;
			}
		}

		MosaikDebug::msg($codes, "Codes:");

		if (is_array($notizen))
			foreach ($notizen as $n_monat => $d_monat) {
				MosaikDebug::msg($d_monat, "Notizen $n_monat:");

				if (is_array($d_monat))
					foreach ($d_monat as $n_tag => $d_tag)
						foreach ($d_tag as $n_standort => $notiz) {
							//MosaikDebug::msg($notiz,"notiz")
							if (isset($notiz['id'])) {
								//  MosaikDebug::msg("update","update");
								$o = Doctrine::getTable("PlanungNotiz")->find(array($notiz['id'], $notiz['standort_id']));
								// fehler standort ist nicht gespeichert?!
								$o->merge($notiz);
								$o->save();
							} else {
								MosaikDebug::msg("new", "new");
								$o = new PlanungNotiz();
								$o->id = $jahr . "-" . ($n_monat + 1) . "-" . $n_tag;
								$o->notiz = $notiz['notiz'];
								$o->standort_id = $n_standort;
								$o->save();
							}
						}
			}

		foreach ($data as $_monat => $tage) {
			if (!is_array($data[$_monat]))
				continue;

			foreach ($data[$_monat] as $_tag => $standorte) {
				if (!is_array($data[$_monat][$_tag]))
					continue;

				foreach ($data[$_monat][$_tag] as $standort_id => $terminList) {
					foreach ($terminList as $termin) {
						if ($termin == null || in_array($termin['id'], $known))
							continue;

						if (!array_key_exists("verschoben", $termin)) {
							$known[] = $termin['id'];
							continue;
						}

						$monat = $_monat + 1;
						$tag = $termin['erster_tag'];
						// es ist wirklich ein termin und er wurde verschoben oder neu angelegt
						$known[] = $termin['id'];
						$status = $codes[$termin['freigabe_flag']];

						$dauer = $termin['dauer'] - 1;
						// der aktuelle tag muss rausgerechnet werden
						$mysqlDatumStart = date("Y-m-d", mktime(0, 0, 0, $monat, $tag, $jahr));
						$mysqlDatumEnde = date("Y-m-d", mktime(0, 0, 0, $monat, $tag + $dauer, $jahr));
						// die dauer wird aus den daten die von der komponente gesendet werden genommen, die aus der viewseminarpreis kommen

						if (substr($termin['id'], 0, 3) == "new") {
							MosaikDebug::msg($termin, "Neuer Termin: " . $tag . "." . $monat . "." . $jahr . " Standort: " . $standort_id);
							$dbArt = Doctrine::getTable("SeminarArt")->find($termin['seminar_art_id']);

							$dbTermin = new Seminar();

							$dbTermin->datum_begin = $mysqlDatumStart;
							$dbTermin->datum_ende = $mysqlDatumEnde;
							$dbTermin->standort_id = $standort_id;

							if ($dbTermin->standort_id == -1) {
								$dbTermin->inhouse = 1;
							}
							$dbTermin->seminar_art_id = $termin['seminar_art_id'];
							$dbTermin->kursnr = "#PL#-" . $termin['seminar_art_id'];
							$dbTermin->kursgebuehr = $dbArt->kursgebuehr;
							$dbTermin->kosten_unterlagen = $dbArt->kosten_unterlagen;
							$dbTermin->kosten_refer = $dbArt->kosten_refer;
							$dbTermin->kosten_allg = $dbArt->kosten_allg;
							$dbTermin->freigabe_status = $codes[$termin['freigabe_flag']]['id'];
							// $dbTermin->planung_info = $termin['planung_info'];

							$dbTermin->teilnehmer_min = $dbArt->teilnehmer_min_tpl;
							$dbTermin->teilnehmer_max = $dbArt->teilnehmer_max_tpl;

							$dbTermin->save();

							$jsonResponse['new'][] = Array("id" => $termin['id'], "saved", 'begin' => $dbTermin->datum_begin, 'ende' => $dbTermin->datum_ende);
						} else {
							MosaikDebug::msg($termin, "Termin geandert: " . $tag . "." . $monat . "." . $jahr . " Standort: " . $standort_id);

							$dbRef = Doctrine::getTable("SeminarReferent")->getBySeminarId($termin["id"]);

							for ($i = 0; $i < count($dbRef); $i++) {
								$dbRef[$i]->standort_id = $standort_id;
							}

							$dbTermin = Doctrine::getTable("seminar")->find($termin["id"]);

							$dbTermin->datum_begin = $mysqlDatumStart;
							$dbTermin->datum_ende = $mysqlDatumEnde;

							$dbTermin->standort_id = $standort_id;
							if ($dbTermin->standort_id == -1) {
								$dbTermin->inhouse = 1;
							}

							$dbTermin->freigabe_status = $codes[$termin['freigabe_flag']]['id'];
							$dbTermin->save();

							MosaikDebug::msg($termin, "Termin: " . $mysqlDatumStart . " - " . $mysqlDatumEnde . " Standort: " . $standort_id);
							$jsonResponse['change'][] = Array("id" => $termin['id'], "saved", 'begin' => $dbTermin->datum_begin, 'ende' => $dbTermin->datum_ende);
						}
					}
				}
			}
		}

		//return "json_encode($jsonResponse);
		$e = new SeminarUpdateEvent();
		PlanungService::dispatchEvent($e);
		return "{}";
	}

	function forward() {
		qlog(__CLASS__ . ":" . __FUNCTION__);

		if (@$_POST['type'] == "json") {
			return $this->POST();
		}
			$year = $this->next();
		$yearCacheKey = "json_kalender_" . $year;
		$cache = DBPool::$cacheDriver;
	
		$data = array();
		$entrys = array();
		$mode = "year";
		$month = 0;
		$feiertage = array();
		$feiertageOrdered = array();
		/* seminare */
		// den aufruf auswerten
		qlog("fetching seminarPlanung...");
		if (($month = MosaikConfig::getEnv('month'))) {
			$data = Doctrine::getTable("ViewSeminarPlanung")->yearMonthSelect($this->next(), $month)->execute(array(), Doctrine::HYDRATE_ARRAY);
			$mode = "month";
			
			$yearCacheKey .= "_" . $month; 
			
		} else {
			$data = Doctrine::getTable("ViewSeminarPlanung")->yearSelect($this->next())->execute(array(), Doctrine::HYDRATE_ARRAY);
			$feiertage = Doctrine::getTable("Feiertag")->yearSelect($this->next())->execute(array(), Doctrine::HYDRATE_ARRAY);

			
		}
		//if (($cdata = $cache->fetch($yearCacheKey)) !== FALSE) {
		//		return $cdata;
		//	}
		qlog("fetch done");

		// feiertage aus der datenbank holen

		for ($i = 0; $i < count($feiertage); $i++) {
			$date = explode("-", $feiertage[$i]['datum']);
			$tmonth = intval($date[1]);
			$tday = intval($date[2]);
			//$bundesland = $feiertage[$i]['bundesland_id'];
			//$feiertageOrdered[$tmonth][$tday][$bundesland] = $feiertage[$i];
			$feiertageOrdered[$tmonth][$tday] = $feiertage[$i];
		}

		// termine aus der datenbank holen

		for ($j = 0; $j < count($data); $j++) {

			$val = $data[$j];
			$time_begin = strtotime($val['datum_begin']);
			$time_ende = strtotime($val['datum_ende']);
			/** OPTIMIZATION**/
			$cacheKey = "kalender_seminar_" . $val['id'];

			if ($cache->fetch($cacheKey) === true) {
				for ($i = $time_begin; $i <= $time_ende; $i = $i + 86400) {
					$tmonth = date("n", $i);
					$tday = date("j", $i);
					$entrys[$tmonth][$tday][$val['standort_id']][] = $cache->fetch($cacheKey . "_tag_" . $tday);
				}
				continue;
			}
			/** END OPTIMIZATION **/

			// from php date doc:
			// j  	Day of the month without leading zeros  	1 to 31
			// n  	Numeric representation of a month, without leading zeros  	1 through 12
			$tag = 1;
			$referenten = Doctrine::getTable("ViewSeminarReferent")->findReferentenBySeminar($val['id'], $val['standort_id']);

			for ($i = $time_begin; $i <= $time_ende; $i = $i + 86400) {
				$tmonth = date("n", $i);
				$tday = date("j", $i);

				$val['erster_tag'] = date("d", $time_begin);
				@$val['referenten'] = $referenten[$tag]['kuerzel'];
				if ($val['inhouse'] == "1") {
					$val['standort_id'] = -1;
				}
				$entrys[$tmonth][$tday][$val['standort_id']][] = $val;
				$cache->save($cacheKey, true, null);
				$cache->save($cacheKey . "_tag_" . $tday, $val, null);

				$tag++;
				if ($tag > 30)
					break;
			}
		}

		/* seminar arten wg. performance */
		$seminarArt = Doctrine::getTable("SeminarArt")->getPlanung()->fetchArray();
		$standorte = Doctrine::getTable("Standort")->getPlanung()->fetchArray();

		$tnotizen = Doctrine::getTable("ViewPlanungNotiz")->findByJahr($this->next(), Doctrine::HYDRATE_ARRAY);
		$notizen = array();
		foreach ($tnotizen as $n) {
			$notizen[$n['monat']][$n['tag']][$n['standort_id']] = $n;
		}

		$ret = json_encode(array("mode" => $mode, "month" => $month, "termine" => $entrys, "seminar_arten" => $seminarArt, "standorte" => $standorte, "feiertage" => $feiertageOrdered, "notizen" => $notizen));

		//$cache->save($yearCacheKey, $ret, null);

		return $ret;
	}

	function filter() {

	}

}
?>
