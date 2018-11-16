<?php
include_once ("generic/adminpdfdocument.php");

class ADM_PDF_Kalender extends Generic_AdminPDFDocument {
	public $template = "kalender";
	public $table = "Kalender";
	public $termine = array();
	public $year = 2000;
	public $detail = "keine";
	private $monthStart = 0;
	private $monthEnd = 0;

	function map($name) {
		return "ADM_PDF_Kalender";
	}

	function renderPdf() {
		$this->initDatasource();
		return $this->generateHtml($this->template);
	}

	function yearSelect() {
		$year = $this->year;
		$data = Doctrine::getTable("ViewSeminarPlanung")->yearSelect($year)->execute(array(), Doctrine::HYDRATE_ARRAY);
		$termine = &$this->termine;

		// arrays initialisieren
		for ($j = $this->monthStart; $j <= $this->monthEnd; $j++) {
			$termine[$j] = array();
			for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $j, $year); $i++) {
				$termine[$j][$i] = array("tag" => $i, "Standorte" => array());

				foreach ($this->standorte as $standort) {
					$termine[$j][$i]['Standorte'][$standort['id']]["Termine"] = array();
				}
			}
		}

		// details
		for ($j = 0; $j < count($data); $j++) {
			$val = $data[$j];
			$time_begin = strtotime($val['datum_begin']);
			$time_ende = strtotime($val['datum_ende']);
			$info = "";
			$referenten = array();
			if ($this->detail == "referenten") {
				$referenten = Doctrine::getTable("ViewSeminarReferent")->findReferentenBySeminar($val['id'], $val['standort_id']);
			}

			// from php date doc:
			// j  	Day of the month without leading zeros  	1 to 31
			// n  	Numeric representation of a month, without leading zeros  	1 through 12
			$tag = 1;
			//	$referenten = Doctrine::getTable("ViewSeminarReferent")->findReferentenBySeminar($val['id']);

			for ($i = $time_begin; $i <= $time_ende; $i = $i + 86400) {
				$tmonth = date("n", $i);
				$tday = date("j", $i);
				//	$val['erster_tag'] = date("d", $time_begin);
				//	@$val['referenten'] = $referenten[$tag]['kuerzel'];
				//	$entrys [$tmonth][$tday] [$val['standort_id']][] = $val;
				if ($this->detail == "referenten") {
					$val['info'] = $referenten[$tag]['kuerzel'];
				} else if ($this->detail == "teilnehmer") {
					$val['info'] = $val['teilnehmer'];
				} else {
					$val['info'] = "";
				}
				if ( is_array($termine[$tmonth][$tday]['Standorte'][$val['standort_id']]["Termine"])) {
					$termine[$tmonth][$tday]['Standorte'][$val['standort_id']]["Termine"][] = $val;
				}
				$tag++;
				if ($tag > 30)
					break;
			}
		}

		//qlog("Termine:");
		//qdir($this->termine);
	}

	function getMonth($month) {
		//$data = Doctrine::getTable("ViewSeminarPlanung")->yearMonthSelect($this->next(), $month)->execute(array(), Doctrine::HYDRATE_ARRAY);
		// termine aus der datenbank holen
		$data = array();

		for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $this->year); $i++) {
			$itime = mktime(0, 0, 0, $month, $i, $this->year);
			$info = getdate($itime);
			$weekend = $info['wday'] == 0 || $info['wday'] == 6;
			$data[] = array("jahr" => $this->year, "monat" => $month, "tag" => $i, "Standorte" => $this->termine[$month][$i]['Standorte'], "weekend" => $weekend);
		}

		return $data;
	}
	
	function getMonthForStandort($month, $standort) {
		//$data = Doctrine::getTable("ViewSeminarPlanung")->yearMonthSelect($this->next(), $month)->execute(array(), Doctrine::HYDRATE_ARRAY);
		// termine aus der datenbank holen
		$data = array();

		for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $this->year); $i++) {
			$itime = mktime(0, 0, 0, $month, $i, $this->year);
			$info = getdate($itime);
			$weekend = $info['wday'] == 0 || $info['wday'] == 6;
			$data[] = array("jahr" => $this->year, "monat" => $month, "tag" => $i, "Standorte" => $this->termine[$month][$i]['Standorte'], "weekend" => $weekend);
		}

		return $data;
	}

	function renderPdfForward() {
		qlog(__CLASS__ . "::" . __FUNCTION__);
		$this->initDatasource();
		$data = explode("-", $this->next());
		$this->year = $year = $data[0];
		$this->month = $month = $data[1];
		$this->zeitraum = $_GET['zeitraum'];
		//$data = Doctrine::getTable($this->table)->find( $this->next() );
		//$this->dsDb->add("Kalender", $data->toArray());
		$this->detail = $_GET['detail'];
		$this->monthStart = 0;
		$this->monthEnd = 0;
		$this->standort = $_GET['standort'];

		$monate = array();

		switch ( strtolower($month) ) {
			case "q1" :
				$this->monthStart = 1;
				$this->monthEnd = 3;
				break;
			case "q2" :
				$this->monthStart = 4;
				$this->monthEnd = 6;
				break;
			case "q3" :
				$this->monthStart = 7;
				$this->monthEnd = 9;
				break;
			case "q4" :
				$this->monthStart = 10;
				$this->monthEnd = 12;
				break;
			case 1 :
			case 2 :
			case 3 :
			case 4 :
			case 5 :
			case 6 :
			case 7 :
			case 8 :
			case 9 :
			case 10 :
			case 11 :
			case 12 :
				$this->monthStart = $month;
				$this->monthEnd = $month;
				break;
			case "h1":
				$this->monthStart = 1;
				$this->monthEnd = 6;
				break;
			case "h2":
				$this->monthStart = 7;
				$this->monthEnd = 12;
				break;			
			$this->monthStart = 1;
				$this->monthEnd = 12;
			default :
				$this->monthStart = 1;
				$this->monthEnd = 12;
		}

		switch ( $this->standort ) {
			case "alle" :
				$this->standorte = Doctrine::getTable("Standort")->findByDql("sichtbar_planung=? AND name <> ?", array(1, "Allgemein"))->toArray();

				$this->yearSelect();

				for ($i = $this->monthStart; $i <= $this->monthEnd; $i++) {
					$pageBreak = "true";
					if ( $i == $this->monthEnd ) {
						$pageBreak="false";
					}
					
					array_push($monate, array("Standorte" => $this->standorte, "Tage" => $this->getMonth($i), "monat" => monatToString($i) . " - ${year}", "druckdatum" => date("d.m.Y H:i"), "pageBreak" => $pageBreak));
				}

				$this->dsDb->add("Monate", $monate);
				$this->dsDb->add("druckdatum", date("d.m.Y H:i"));

				setHttpFilename("Kalender-" . $year . "-" . $month . ".pdf");
				break;
			default: 
				$this->template="kalender-standort";
				$pageBreak = "false";
				$this->standorte = Doctrine::getTable("Standort")->findByDql("id=?", array($this->standort))->toArray();
				$standortName = $this->standorte[0]['name'];
				$this->yearSelect();
				if ( $this->zeitraum == "jahr") {
					$GLOBALS['pdf-page-size']="A3";
				}
				
				for ($i = $this->monthStart; $i <= $this->monthEnd; $i++) {
					if ( $i %6 == 0 && $this->zeitraum!="jahr") {
						$pageBreak="true";
					}
					$monateStr = array();
					array_push($monateStr, array("name" => monatToString($i)));
					array_push($monate, array("Standorte" => $this->standorte, "MonatLbl"=>$monateStr, "Tage" => $this->getMonth($i), "monat" => monatToString($i) . " - ${year}", "druckdatum" => date("d.m.Y H:i"), "pageBreak" => $pageBreak));
				}

				$this->dsDb->add("Monate", $monate);
				$this->dsDb->add("druckdatum", date("d.m.Y H:i"));
				$this->dsDb->add("standortName", $standortName);
				$this->dsDb->add("currentYear", $year);
				
				
				setHttpFilename("Kalender-$standortName-" . $year . "-" . $month . ".pdf");
	
			
		}
		$html = $this->generateHtml($this->template);
		$this->dsDb = null;
		$this->pagereader = null;
		return $html;
	}

	function renderHtml() {
		$this->initDatasource();
		return $this->generateHtml($this->template);
	}

	function renderHtmlForward() {
		return $this->renderPdfForward();
	}

}
