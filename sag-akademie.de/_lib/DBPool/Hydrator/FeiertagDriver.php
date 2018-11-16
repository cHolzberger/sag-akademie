<?php

/**
 * Builds result sets in to the hierarchy graph using php arrays
 *
 */
class DBPool_Hydrator_FeiertagDriver extends Doctrine_Hydrator_ArrayDriver
{
    private $_fields;

    function  __construct ($fields) {
        parent::__construct();
        $this->_fields = $fields;
    }

    private function _solve(&$arr, &$target, $idx, ...$fields) {

        if ( count( $fields ) > 0 ) {
            if ( !array_key_exists($target[$idx],$arr)) {
                $arr[$target[$idx]] = [];
            }
            return $this->_solve($arr[$target[$idx]], $target, ...$fields);
        }
        return $arr[$target[$idx]] = &$target;
    }

    public function hydrateResultSet($stmt)
    {
        $fields = $this->_fields;
        $data = parent::hydrateResultSet($stmt);
        $table = $this->getRootComponent();

        $feiertageOrdered = [];
        qlog("Hydrating TableName: " . $table->getTableName() );
        /*if($table->getTableName() != "feiertag") {
            throw new Doctrine_Exception('Nur für Feiertage');
        }*/
       $x = array();
       qlog("Datasets: ".count($data));
        qdir($data[0]);

        for ($i = 0; $i < count($data); $i++) {
            $this->_solve($feiertageOrdered, $data[$i], ...$fields);


			/*$tmonth = intval($feiertage[$i]['month']);
			$tday = intval($feiertage[$i]['day']);

            if ( !array_key_exists( $tmonth , $feiertageOrdered)) {
                $feiertageOrdered[$tmonth] = [];
            }
			$feiertageOrdered[$tmonth][$tday] = $feiertage[$i];*/
        }

        return $feiertageOrdered;
    }
}