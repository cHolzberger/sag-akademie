<?php

class JSON_SeminarReferentCollision extends k_Component {
    /**
	 * Table name of the Table this JSON Componentent belongs to
	 * @var String
	 */
	private $tableName = "SeminarReferentCollision";
	function map() {
		return "JSON_".$this->tableName;
	}
	function renderJson() {
		$tbl = Doctrine::getTable("ViewSeminarReferentCollision");
		$result = $tbl->getReferentCol()->fetchArray();
		MosaikDebug::msg($result, "Result");
		$data = array();
		if(count($result) >= 1){
		    foreach($result as $entry) {
			$a_tbl = Doctrine::getTable("ViewSeminarReferentDatum");
			$a_result = $a_tbl->getReferentColHits()->fetchArray(array($entry['datum'],$entry['referent_id']));
			foreach($a_result as $key) {
			    $data[$entry['datum']][$entry['referent_id']][] = array("standort_id"=> $key['standort_id'], "seminar_id" => $key['seminar_id'], "seminar_referent_id" => $key['id'], "seminar_kursnr" => $key['seminar_kursnr'] );
			}
		    }
		}
		MosaikDebug::msg($data, "getData");
		return json_encode( array(
			"data"=> $data
		));
	}

}
?>
