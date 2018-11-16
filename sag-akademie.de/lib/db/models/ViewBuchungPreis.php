<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ViewBuchungPreis extends BaseViewBuchungPreis {
    public function setUp() {
	$this->actAs("BuchungTemplate");
	$this->actAs("SoftDelete");
    }
    function advancedSearchTableProxy($array) {
	$q = Doctrine_Query::create();

	$q->from( "ViewBuchungPreis buchung" );
	$this->detailedQ($q);
	$first = true;
	$tmp_where = "";
	$q->where("1 = 0");
	foreach($array as $key => $value) {
	    if($value == "or") {
		$q->orWhere($tmp_where);
		$tmp_where = "";
		$first = true;
	    }else {
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
		    $tmp_where .= "buchung.".$val_array[0]." ".$exp[1]." ".$search;
		    $first = false;
		}else {
		    $tmp_where .= " AND buchung.".$val_array[0]." ".$exp[1]." ".$search;
		}
	    }
	}
	$q->orWhere($tmp_where);

	return $q;
    }

}
?>