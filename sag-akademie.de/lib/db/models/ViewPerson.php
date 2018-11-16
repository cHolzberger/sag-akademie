<?php

/**
 * ViewPerson
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class ViewPerson extends BaseViewPerson {
	public function setUp() {
		parent::setUp();

		$this->hasOne('Kontakt', array('local' => 'kontakt_id', 'foreign' => 'id'));
		$this->hasOne('XUser as GeaendertVon', array('local' => 'geaendert_von', 'foreign' => 'id', ));
		$this->hasMany('ViewBuchungPreis as Buchungen', array('local' => 'id', 'foreign' => 'person_id'));

		$this->hasOne('XUser as AngelegtVon', array('local' => 'angelegt_user_id', 'foreign' => 'id', ));
	}

	function advancedSearchTableProxy($array) {
		qlog(__CLASS__."::".__FUNCTION__.": ");
		qdir($array);
		
		$q = Doctrine_Query::create();
		//$q->select("{a.*}");
		$q->from("ViewPerson a");
		//		MosaikDebug::msg($array, "Ausgabe:");
		$first = true;
		$tmp_where = "";

		foreach ($array as $key => $value) {
			$value = $array[$key];

			if ($value == "or") {
				if ( $tmp_where != "" ) {
					$tmp_where = $tmp_where . " OR ";
				}
				$first = true;
			} else {
				$exp = explode(";", $value);
				$val_array = explode(":", $exp[0]);
				switch($val_array[1]) {
					case "string" :
						$exp[2] = "%" . $exp[2] . "%";
						break;
					case "date" :
						$exp[2] = mysqlDateFromLocal($exp[2]);
						break;
					case "datetime" :
						$exp[2] = mysqlDatetimeFromLocal($exp[2]);
						break;
				}
				$search = "'" . $exp[2] . "'";

				if ($first) {
					$tmp_where .= "a." . $val_array[0] . " " . $exp[1] . " " . $search;
					$first = false;
				} else {
					$tmp_where .= " AND a." . $val_array[0] . " " . $exp[1] . " " . $search;
				}
			}
		}
		qlog("Advanced Search Query: " . $tmp_where);
		$q->where($tmp_where);
		//$q->addComponent("a", "ViewPerson");
		//MosaikDebug::msg($q->getSQL(),"Query");
		return $q;
	}

}
