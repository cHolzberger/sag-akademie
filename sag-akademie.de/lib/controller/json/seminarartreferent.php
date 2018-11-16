<?php
/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class JSON_SeminarArtReferent extends k_Component {
	/**
	 * Table name of the Table this JSON Componentent belongs to
	 * @var String
	 */
	private $tableName = "SeminarArtReferent";

	function map() {
		return "JSON_".$this->tableName;
	}

	/**
	 * speichert die referenten information zu den tagen einer seminar art in der datenbank
	 * @return String
	 */
	function POST() {
		if ( $_POST['type'] != "json") return;
		$tbl = Doctrine::getTable($this->tableName);

		$seminar = Doctrine::getTable("SeminarArt")->find( urldecode($this->next() ));

		$data = json_decode(utf8_encode(stripslashes ($_POST['data'])), true);

		$check = "";

		$standort_id = $data['standort_id'];
		$seminar->clearReferenten($standort_id);

		MosaikDebug::infoDebug($data, $_POST['data']);

		MosaikDebug::infoDebug($data, "Look");
		
		foreach ( $data['data'] as $tag => $referenten) {
			foreach ( $referenten as $referent ) {
				if ( false == $seminar->hasReferent($referent['id'], $tag + 1, $standort_id) ) {
				    @ $theorie = $referent['theorie'];
				    @ $praxis = $referent['praxis'];
				    @ $seminar->addReferent( $referent['id'], ($tag +1 ), $standort_id, $theorie, $praxis, $referent['start_stunde'], $referent['start_minute'], $referent['ende_stunde'], $referent['ende_minute'],
					$referent['start_praxis_stunde'], $referent['start_praxis_minute'], $referent['ende_praxis_stunde'], $referent['ende_praxis_minute'], $referent['optional']);
				}
			}
		}

		return $check;
	}

	function renderJson() {
		$tbl = Doctrine::getTable($this->tableName);

		$data = $tbl->findAll(Doctrine::HYDRATE_ARRAY);

		foreach ($data as $key=>$d) {
			$data[$key]['langbeschreibung'] = htmlentities($data[$key]['langbeschreibung']);
		}

		if (isset($_GET['convert'] )) {
			foreach ( $data as $key=>$d) {
				$data[$key]['datum_begin'] = mysqlDateToLocal($d['datum_begin']);
				$data[$key]['datum_ende'] = mysqlDateToLocal($d['datum_ende']);
			}
		}

		return json_encode( array(
			"mode" => "multi",
			"headers" => $this->getHeaders(),
			"data"=> $data
			));
	}

	function forward () {
		if ( isset ($_POST['type'])) {
			return $this->POST();
		}

		$tbl = Doctrine::getTable($this->tableName);
		$data = $tbl->getReferentenForSeminarId( urldecode($this->next()) ,  $_GET['standort_id']);

		$cTag = 0;
		$return = array();

		foreach ( $data as $tag ) {
			$cTag = $tag['tag'];

			if ( !array_key_exists($cTag, $return) || ! is_array( $return[$cTag] )) $return [$cTag] = array();
			$tmp = $tag['Referent'];
			$tmp['theorie'] = $tag['theorie'];
			$tmp['praxis'] = $tag['praxis'];
			$tmp['start_stunde'] = $tag['start_stunde'];
			$tmp['start_minute'] = $tag['start_minute'];
			$tmp['ende_stunde'] = $tag['ende_stunde'];
			$tmp['ende_minute'] = $tag['ende_minute'];
			$tmp['start_praxis_stunde'] = $tag['start_praxis_stunde'];
			$tmp['start_praxis_minute'] = $tag['start_praxis_minute'];
			$tmp['ende_praxis_stunde'] = $tag['ende_praxis_stunde'];
			$tmp['ende_praxis_minute'] = $tag['ende_praxis_minute'];
			$tmp['optional'] = $tag['optional'];
			
			$return[$cTag][] = $tmp;
		}

		return json_encode( array(
			"mode" => "multi",
			"length" => count($return),
			"headers" => $this->getHeaders(),
			"data"=> $return
			));
	}

	function getHeaders() {
		$head = array();

		$head[] = array ( "field"=> 'id', "label"=>"ID", "format"=>"default");
		$head[] = array ( "field"=> 'grad',"label"=>"Grad", "format"=>"default");
		$head[] = array ( "field"=> 'vorname',"label"=>"Vorname", "format"=>"default");
		$head[] = array ( "field"=> 'name',"label"=>"Name", "format"=>"default");
		$head[] = array ( "field"=> 'image',"label"=>"Bild", "format"=>"image");
	$head[] = array("field" => 'start_stunde', "label" => "ID", "format" => "default");
		$head[] = array("field" => 'start_minute', "label" => "ID", "format" => "default");
	
		$head[] = array("field" => 'ende_stunde', "label" => "ID", "format" => "default");
	
		$head[] = array("field" => 'ende_minute', "label" => "ID", "format" => "default");
		return $head;
	}

	function filter() {

	}
}

?>
