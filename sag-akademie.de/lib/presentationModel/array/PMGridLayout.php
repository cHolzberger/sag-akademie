<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of PMSummeBuchungenNachRubrik
 *
 * @author molle
 */
include_once("presentationModel/PMBasic.php");
include_once("debug.php");

/**
 * diese klasse erzeugt aus mehreren datensaetzen ein zur darstellung passendes array
 *
 * eingangswerte sind per default:
 * $data['name'] = "xx", $data['monat'] = "1", $data['gesamt']=2
 * $data['name'] = "xx2", $data['monat'] = "2", $data['gesamt']=4
 *
 * nach der transofrmation erhaelt man:
 *
 * $data['1']['xx'] = 2
 * $data['2']['xx2'] = 4
 */
class PMGridLayout extends PMBasic {

    //put your code here
    var $monate = array("-", "Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    var $verticalLabels = "monat";
    var $horizontalLabels = "name";
    var $valueField = "gesamt";
    /** soll 'gesamt' ausgerechnet werden?
     *
     * @var bool
     */
    var $calculate = false;

    /**
     * erstellt summen der vorhandenen felder des eingabearrays $data
     * filtert dabei felder mit dem name monat und bennent sie dem entsprechenden monat nach um
     * @param array $data
     *
     */
    function runFilter() {
        $source = &$this->src;
        $dest = &$this->dst;
        

        for ($i = 0; $i < count($source); $i++) {
            $cr = $source[$i];
            $ti = $cr[$this->verticalLabels];
            $ab = $cr[$this->horizontalLabels];

	    array_key_exists($ti, $dest) ? true : $dest[$ti] = array();

	    $value = $cr[$this->valueField];

            

            // filter
            // variabler zugriff auf die klasse


            if (!array_key_exists("monat_translated",$dest[$ti]) || $dest[$ti]["monat_translated"] != true && array_key_exists("monat", $source[$i]) ) {
                $dest[$ti]["monat_translated"] = true;
				if ( array_key_exists('monat',$source[$i]) && array_key_exists($source[$i]['monat'], $this->monate)) {
                	@$dest[$ti]["monat"] = $this->monate[$source[$i]['monat']];
				}
            }

            if ( empty($value) ) {

                $dest[$ti][$ab] = intval(0);
            } else {
                $dest[$ti][$ab] = intval($value);
            }
	    
            if ($this->calculate) {
                array_key_exists("gesamt", $dest[$ti]) ? true : $dest[$ti]['gesamt'] = 0;
                $dest[$ti]['gesamt'] += $value;
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
