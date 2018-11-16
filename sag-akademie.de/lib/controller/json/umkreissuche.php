<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
require_once("templates/sag/JsonComponent.php");
require_once("Mosaik/JsonResult.php");

class JSON_Umkreissuche extends JsonComponent {

	public $Erdradius = 6371;

	function map() {
		return "JSON_Standort";
	}

	public function Kugel2Kartesisch($lon, $lat, &$x, &$y, &$z) {
		$lambda = $lon * pi() / 180;
		$phi = $lat * pi() / 180;
		$x = $this->Erdradius * cos($phi) * cos($lambda);
		$y = $this->Erdradius * cos($phi) * sin($lambda);
		$z = $this->Erdradius * sin($phi);
		return true;
	}

	function renderJson() {
		qlog(__CLASS__ . "::" . __METHOD__);
		
		solveJson($_POST);
		qdir($_POST);
		
		
		$jr = new MosaikJsonResult();
		$jr->headline = "Umkreissuche: ";
		$jr->headers = $this->getHeaders();
		$jr->perPage = 500;

		$ausgangsort = 0; 
		
		if  ( @isset ( $_POST['ausgangsort']) ) {
			$ausgangsort = $_POST['ausgangsort'];
		} else if ( @isset($_POST['OpengeodbPlz']['id'])) {
			$ausgangsort = $_POST['OpengeodbPlz']['id'];
		} else if (!isset($_POST['OpengeodbPlz']['id'])) {
			return json_encode(array("data: {}", "headers" => $this->getHeaders()));
		}
		

		if (empty($_POST['umkreis']))
			return "{}";

		@$umkreis = $_POST['umkreis'];
		@$newsletter = $_POST['newsletter'];
		@$kategorie = $_POST['kategorie'];
		@$branche = $_POST['branche'];
		@$taetigkeitsbereich = $_POST['taetigkeitsbereich'];

		@$db = $_POST['db'];

		$ort = Doctrine::getTable("ViewStandortKoordinaten")->find($ausgangsort);

		$q = Doctrine_Query::create();
		// suche als eigene funktion
		$q = $this->suche($q, $ort, $umkreis);
		if ($newsletter == "1") {
			$q->andWhere("newsletter = 1");
		}

		if (is_array($db) && count($db) > 0) {
			$qe = array();
			foreach ($db as $type) {
				$qe[] = "quelle = '$type'";
			}
			$q->andWhere(" ( " . join(" OR ", $qe) . " ) ");
		}

		// kategorie auswerten
		if (is_array($kategorie) && count($kategorie) > 0) {
			$qe = array();
			foreach ($kategorie as $k) {
				$qe[] = " kategorie_id = $k";
			}
			$q->andWhere(" ( " . join(" OR ", $qe) . " ) ");
		}
		
		// rubrik auswerten
		if (is_array($branche) && count($branche) > 0) {
			$qe = array();
			foreach ($branche as $k) {
				$qe[] = " branche_id = $k";
			}
			$q->andWhere(" ( " . join(" OR ", $qe) . " ) ");
		}
		
		// rubrik auswerten
		if (is_array($taetigkeitsbereich) && count($taetigkeitsbereich) > 0) {
			$qe = array();
			foreach ($taetigkeitsbereich as $k) {
				$qe[] = " taetigkeitsbereich_id = $k";
			}
			$q->andWhere(" ( " . join(" OR ", $qe) . " ) ");
		}


		$jr->q = $q;

		return $jr->render();
	}

	public function suche($q, $ort, $radius) {
		//$this->Kugel2Kartesisch($lon, $lat, $UrsprungX, $UrsprungY, $UrsprungZ);

		$q->from("ViewWerbeEmpfaenger")
		 ->where("x >= ?", array($ort->x - $radius))
		 ->andWhere("x <= ?", array($ort->x + $radius))
		 ->where("y >= ?", array($ort->y - $radius))
		 ->andWhere("y <= ?", array($ort->y + $radius))
		 ->where("z >= ?", array($ort->z - $radius))
		 ->andWhere("z <= ?", array($ort->z + $radius))
		 ->andWhere("(? - x) * (? -x) + (? - y) * (? - y) + (? - z) * (? - z) <= ?", array($ort->x, $ort->x, $ort->y, $ort->y, $ort->z, $ort->z, pow($this->Erdradius * sin($radius / (2 * $this->Erdradius)), 2))
		);
//        $sql = 'SELECT ' . $Spalten . '
		//              FROM `' . $this->table . '`
		//            WHERE
		//              KoordX >= ' . ($UrsprungX - $Radius) . '
		//        AND KoordX <= ' . ($UrsprungX + $Radius) . '
//                AND KoordY >= ' . ($UrsprungY - $Radius) . '
		//              AND KoordY <= ' . ($UrsprungY + $Radius) . '
		//            AND KoordZ >= ' . ($UrsprungZ - $Radius) . '
		//           AND KoordZ <= ' . ($UrsprungZ + $Radius) . '
		//         AND POWER(' . $UrsprungX .' - KoordX, 2)
		//         + POWER(' . $UrsprungY .' - KoordY, 2)
		//       + POWER(' . $UrsprungZ .' - KoordZ, 2)
		//       <= "' . pow(2 * $this->Erdradius * sin($Radius / (2 * $this->Erdradius)), 2) . '"';
		//$re = mysql_query($sql, $this->db);
		//$result = array();
		//while ($rd = mysql_fetch_object($re)) {
		//   $result[] = $rd;
		//}
		return $q;
	}

	function getTable() {
		return Doctrine::getTable("Standort");
	}

	function getHeaders() {
		global $_tableHeaders;
		return $_tableHeaders['ViewWerbeEmpfaenger'];
	}

	function filter() {
		
	}

}

?>
