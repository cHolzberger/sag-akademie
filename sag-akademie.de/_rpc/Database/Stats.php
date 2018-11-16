<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
include_once ("helpers.php");
include_once("presentationModel/array/PMColumnLayout.php");
include_once("presentationModel/array/PMGridLayout.php");

class Database_Stats {

	var $monate = array("-", "Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

	function getBuchungen($year) {
		/** buchungen pro monat generieren * */
		$pm = new PMColumnLayout();

		$buchungen = array();
		$_buchungen = Doctrine::getTable("ViewBuchungStatusSumme")->findByJahr($year, Doctrine::HYDRATE_ARRAY);

		$pm->filter($_buchungen, $buchungen);


		/** gesamt anzahl der buchungen generieren * */
		$pm = new PMColumnLayout();
		$buchungenJahr = array();
		$_buchungenJahr = Doctrine::getTable("ViewBuchungenSummeJahr")->findByJahr($year, Doctrine::HYDRATE_ARRAY);
		$pm->index = "jahr";
		$pm->filter($_buchungenJahr, $buchungenJahr);


		return array("monthly" => $buchungen, "total" => $buchungenJahr);
	}

	function getSeminareProBereich($year) {
		// Seminare nach bereich im Jahr
		$pm = new PMGridLayout();
		$pm->calculate = true;
		$tbereich = array();
		for ($i = 1; $i < 12; $i++) {
			$tbereich[$i] = array();
		}

		$_tbereich = Doctrine::getTable("ViewSeminareProBereich")->findByJahr($year, Doctrine::HYDRATE_ARRAY);
		$pm->filter($_tbereich, $tbereich);

		$pm = new PMGridLayout();
		$pm->verticalLabels = "jahr";
		$pm->horizontalLabels = "name";

		$tbereichGesamt = array();
		$_tbereichGesamt = Doctrine::getTable("ViewSeminareProBereichJahr")->findByJahr($year, Doctrine::HYDRATE_ARRAY);
		$pm->calculate = true;
		$pm->filter($_tbereichGesamt, $tbereichGesamt);

		return array("monthly" => $tbereich, "total" => $tbereichGesamt[$year]);
	}

	function getBelegungProBereich($year) {
		// Buchungen nach bereich
		$pm = new PMGridLayout();
		$pm->calculate = true;
		$nbereich = array();
		for ($i = 1; $i < 12; $i++) {
			$nbereich[$i] = array();
			$nbereich[$i]['monat'] = $pm->getMonatLabel($i + 1);
		}

		$_nbereich = Doctrine::getTable("ViewSeminarBelegung")->findByJahr($year, Doctrine::HYDRATE_ARRAY);
		$pm->filter($_nbereich, $nbereich);

		$pm = new PMGridLayout();
		$pm->verticalLabels = "jahr";
		$pm->horizontalLabels = "name";

		$nbereichGesamt = array();
		$_nbereichGesamt = Doctrine::getTable("ViewSeminarBelegungJahr")->findByJahr($year, Doctrine::HYDRATE_ARRAY);
		$pm->calculate = true;
		$pm->filter($_nbereichGesamt, $nbereichGesamt);

		return array("monthly" => $nbereich, "total" => $nbereichGesamt[$year]);
	}

	function getBuchungenIndex() {
		$ret = array();
		$data = Doctrine::getTable("ViewAnzahlBuchungen")->findAll(Doctrine::HYDRATE_ARRAY);
		$ret = $data;
		$count = array();
		$toYear = date("Y")+2;
		// init
		for ($i = 2004; $i <= $toYear; $i++) {
			$ret [$i] = array();
		}

		foreach ($data as $monat) {
			if (!isset($count[$monat['jahr']]))
				$count[$monat['jahr']] = 0;
			$count[$monat['jahr']] += $monat['anzahl'];
			$ret[$monat['jahr']][ $monat['monat'] ] = $monat['anzahl'];
		}
		//$ds->log();
		for ($i = 2004; $i <= $toYear; $i++) {
			if ( array_key_exists($i, $count)) {
				$ret [$i]['total'] = $count[$i];
			} else {
				$ret [$i]['total'] = 0;
			}
		}

		return $ret;
	}

	// akquise kontakte angelegt vom Benutzer
	function getUserStats () {
			$akquiseStats = array();
			$kontaktStats = array();

			$users = array();
		
			$ak = Doctrine::getTable("ViewUserKontakt")->findByDql("1=1")->toArray();
			$k =$rows = Doctrine::getTable("ViewUserAkquiseKontakt")->findByDql("1=1")->toArray();

			foreach($ak as $_a ) {
                try {
                    if ( !array_key_exists($_a['jahr'], $akquiseStats )) {
                        $akquiseStats[$_a['jahr']] =array();
                    }

                    if ( !array_key_exists( $_a['monat'] , $akquiseStats[$_a['jahr']] ))  {
                        $akquiseStats[$_a['jahr']][$_a['monat']] =array();
                    }

                    $user = $_a["nachname"] .", ". $_a['user_vorname'];
                    $users[] = $user;
                    $akquiseStats[$_a['jahr']][$_a['monat']][$user] = $_a['anzahl'];
                } catch (Exception $e) {
                    qerror("Exeption: " . $e);
                }
			}

			foreach($k as $_a ) {
                try {
                    if ( !array_key_exists( $_a['jahr'], $kontaktStats)) {
                        $kontaktStats[$_a['jahr']] =array();
                    }

                    if ( !array_key_exists( $_a['monat'], $kontaktStats[$_a['jahr']]))  {
                        $kontaktStats[$_a['jahr']][$_a['monat']] =array();
                    }

                    $user = $_a["nachname"] .", ". $_a['user_vorname'];
                    $users[] = $user;
                    $kontaktStats[$_a['jahr']][$_a['monat']][$user] = $_a['anzahl'];
                } catch (Exception $e) {
                    qerror("Exeption: " . $e);
                }
			}
			$users = array_unique($users);

			return array("user"=>$users, "akquiseStats" => $akquiseStats, "kontaktStats" => $kontaktStats);
	}



}
