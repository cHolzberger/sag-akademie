<?php
/* 
 * 
 */
include("adm/dbcontent.php");

class ADM_Umkreissuche extends ADM_DBContent {
    // Erdradius in Kilometern
    public $Erdradius = 6371;
    // mysql link identifier
    private $db;
    // Datentabelle
    private $table = false;
    // Fehler zeigen?
    public $zeigeFehler = true;

    private $ausgangsort = 0;

    public $dbClass="OpengeodbPlz";

    public function map ($name) {
	return "ADM_Umkreissuche";
    }

    function POST() {
	$this->init();
	$this->onShowList($this->pageReader, $this->dsDb);
	return $this->pageReader->output->get();
    }

   function onShowList(&$pr, &$dsDb) {
	if ( isset($_GET['init'])) {
	    $this->initTable();
	}
	$this->dsDb->add("dbtable",$this->dbClass);
	$this->dsDb->add("dbclass",$this->dbClass);
	$fn = $this->name();

	if ( isset($_GET['search']) ) {
	   	$fn = $this->name() . "/list";    
	}
	$this->dsDb->add("formaction", "/admin/umkreissuche?search=1" );

	$pr->loadPage($fn);
    }
    /**
     * must be run after each update to opengeodb_plz
     */
    public function initTable() {
	$data = Doctrine::getTAble("OpengeodbPlz")->findAll();

	$x =0;
	$y = 0;
	$z = 0;

	foreach ( $data as $entry) {
	    $this->Kugel2Kartesisch($entry->lon, $entry->lat, $x, $y, $z);
	    $entry->x = $x;
	    $entry->y = $x;
	    $entry->z = $z;
	    $entry->save();
	}
    }

    public function Kugel2Kartesisch($lon, $lat, &$x, &$y, &$z) {
        $lambda = $lon * pi() / 180;
        $phi = $lat * pi() / 180;
        $x = $this->Erdradius * cos($phi) * cos($lambda);
        $y = $this->Erdradius * cos($phi) * sin($lambda);
        $z = $this->Erdradius * sin($phi);
        return true;
    }

    
}
   
?>