<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of PMSummeBuchungen
 *
 * @author molle
 */
include_once("debug.php");
include_once("presentationModel/PMBasic.php");

/**
 * formatiert einen array
 * aus:
 * $data[$i]['monat']=1, $data[$i]['gesamt'] = 1, $data[$i]['whatever'] = 2
 *
 * wird
 *
 * $data[1][gesamt]=1
 * $data[1][whatever]=2
 */
class PMColumnLayout extends PMBasic {

    //put your code here
    var $monate = array("-", "Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    var $index = "monat";

    /**
     * erstellt summen der vorhandenen felder des eingabearrays $data
     * filtert dabei felder mit dem name monat und bennent sie dem entsprechenden monat nach um
     * @param array $data
     *
     */
    function runFilter() {
        $source = &$this->src;
        $data = &$this->dst;

        for ($i = 0; $i < count($source); $i++) {

            $ti = $source[$i][$this->index];
            array_key_exists($ti, $data) ? true : $data[$ti] = array();
	    
            if ( !array_key_exists("monat_translated",$data[$ti]) || $data[$ti]["monat_translated"] != true) {
				$data[$ti]["monat_translated"] = true;
				if (array_key_exists('monat',$source[$i]) && array_key_exists($source[$i]['monat'], $this->monate)) {
                	@$data[$ti]["monat"] = $this->monate[$source[$i]['monat']];
				}
            }

            foreach ($source[$i] as $key => $value) {
		if ( $key == 'monat') continue;
                // filter
                if (empty($value)) {
                    $data[$ti][$key] = "0";
                } else {
                    $data[$ti][$key] = $value;
                }
            }
        }
    }

    /**
     * gibt fuer den monat als zahl 1-12
     * den enstprechenden string aus
     *
     * @param int $monat
     * @return string
     */
    function getMonatLabel($monat) {
        return $this->monate[$monat];
    }

}
?>
