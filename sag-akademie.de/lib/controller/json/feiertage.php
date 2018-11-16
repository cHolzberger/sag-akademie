<?php
/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class JSON_Feiertage extends k_Component {

	function map() {
		return "JSON_Feiertage";
	}

	function POST() {
		return $this->renderJson();
	}

	function renderJson() {
		$bundeslaender = Doctrine::getTable("XBundesland")->findAll()->toArray(true);
		$entrys = "";
		$i = 0;
		foreach ($bundeslaender as $key => $val) {
			$feiertage = Doctrine::getTable("ViewFeiertagR")->getBundesland($val['id'])->fetcharray();
			$entrys[$i]['name'] = $val['name'];
			$entrys[$i]['feiertage'] = $feiertage;
			$i++;
		}

		return json_encode(array(
		 "mode" => "multi",
		 "data" => $entrys
		));
	}

	function forward() {
		$bundeslaender = Doctrine::getTable("XBundesland")->findAll()->toArray(true);
		$entrys = "";
		$i = 0;
		foreach ($bundeslaender as $key => $val) {
			$feiertage = Doctrine::getTable("ViewFeiertagR")->getBundesland($val['id'], $this->next())->fetcharray();
			$entrys[$i]['name'] = $val['name'];
			$entrys[$i]['feiertage'] = $feiertage;
			$i++;
		}

		return json_encode(array(
		 "mode" => "multi",
		 "data" => $entrys
		));
	}

	function filter() {
		
	}

}
?>
