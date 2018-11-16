<?php

/**
 * ViewAkquise
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class ViewAkquise extends BaseViewAkquise {
	function advancedSearchTableProxy($array) {
		$q =  Doctrine_Query::create();
		// umstellen auf raw sql
		//$q->select("a");
		$q->from( "ViewAkquise a" );
		//MosaikDebug::msg($array, "Ausgabe:");

		$first = true;
		$tmp_where = "";
		foreach ($array as $key=>$value ) {
			$value = $array[$key];

			if($value == "or") {
				$tmp_where = $tmp_where . " OR " ;
				$first = true;
			} else {
				$exp = explode(";", $value);
				$val_array = explode(":", $exp[0]);
				switch($val_array[1]) {
					case "string":
						$exp[2] = "%".$exp[2]."%";
						break;
					case "date":
						$exp[2] = mysqlDateFromLocal($exp[2]);
						break;
					case "datetime":
						$exp[2] = mysqlDatetimeFromLocal($exp[2]);
						break;
				}
				$search = "'".$exp[2]."'";
				if($first) {
					$tmp_where .= "a.".$val_array[0]." ".$exp[1]." ".$search;
					$first = false;
				}else {
					$tmp_where .= " AND a.".$val_array[0]." ".$exp[1]." ".$search;
				}
			}
		}
		$q->where($tmp_where);
		//$q->addComponent("a", "ViewAkquise");

		return $q;
	}
}