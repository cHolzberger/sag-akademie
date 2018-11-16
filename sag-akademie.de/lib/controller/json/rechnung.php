<?php
/* 
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted		
 * */
require_once("Mosaik/JsonResult.php");
class JSON_Rechnung extends k_Component {
      function POST() {
	return $this->renderJson();
    }
	function renderJson() {
	    $jr = new MosaikJsonResult();
// FIXME: personen werden nicht aufgeloest
	    $jr->q = Doctrine::getTable("ViewBuchungPreis")->unbezahlt();
	    $jr->headers = $this->getHeaders();
	    $jr->headline = "Rechnungen";
	    
	    return $jr->render();
	}

	function getHeaders() {
		$head = array();
		$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
		$head[] = array ("field"=> 'status', "label"=>"Status", "format"=>"status");
		$head[] = array ("field"=> 'status_status', "label"=>"Storno Status", "format"=>"default");
		$head[] = array ("field"=> 'storno_datum', "label"=>"Storno Datum", "format"=>"date");
		$head[] = array ("field"=> 'Person:name', "label"=>"Name", "format"=>"default");
		$head[] = array ("field"=> 'Person:vorname', "label"=>"Vorname", "format"=>"default");
		$head[] = array ("field"=> 'Person:Kontakt:firma', "label"=>"Firma", "format"=>"default");
		$head[] = array ("field"=> 'Person:Kontakt:vdrk_mitglied', "label"=>"Vdrk Mitglied", "format"=>"bool");
		$head[] = array ("field"=> 'Person:Kontakt:vdrk_mitglied_nr', "label"=>"Vdrk Mitglied Nr.", "format"=>"default");
		$head[] = array ("field"=> 'vdrk_referrer', "label"=>"VDRK Referrer", "format"=>"bool");
		$head[] = array ("field"=> 'arbeitsagentur', "label"=>"Arbeitsagentur", "format"=>"bool");
		$head[] = array ("field"=> 'zustaendige_arbeitsagentur', "label"=>"Zustaendige Arbeitsagentur", "format"=>"default");
		$head[] = array ("field"=> 'bildungscheck', "label"=>"Bildungscheck", "format"=>"default");
		$head[] = array ("field"=> 'bildungscheck_austellung_ort', "label"=>"Ausstellungsort", "format"=>"default");
		$head[] = array ("field"=> 'bildungscheck_ausstellung_datum', "label"=>"Ausstellungsdatum", "format"=>"default");
		$head[] = array ("field"=> 'bildungscheck_ausstellung_bundesland', "label"=>"Ausstellungsbundesland", "format"=>"default");
		$head[] = array ("field"=> 'HotelBuchung:Hotel:name', "label"=>"Hotel", "format"=>"default");
		$head[] = array ("field"=> 'HotelBuchung:anreise_datum', "label"=>"Anreisedatum", "format"=>"default");
		$head[] = array ("field"=> 'HotelBuchung:uebernachtungen', "label"=>"Übernachtungen", "format"=>"default");
		$head[] = array ("field"=> 'Seminar:SeminarArt:nettoep', "label"=>"Preis", "format"=>"currency");
		$head[] = array ("field"=> 'rabatt', "label"=>"Rabatt", "format"=>"default");
		$head[] = array ("field"=> 'rechnunggestellt', "label"=>"Rechnung gestellt", "format"=>"date");
		$head[] = array ("field"=> 'zahlungseingang_datum', "label"=>"Zahlungseingang", "format"=>"date");
		$head[] = array ("field"=> 'bemerkung', "label"=>"Notiz Kunde", "format"=>"default");
		$head[] = array ("field"=> 'notiz', "label"=>"Notiz SAG", "format"=>"default");
		$head[] = array ("field"=> 'uuid', "label"=>"Best. Code", "format"=>"default");
		$head[] = array ("field"=> 'seminar_unterlagen', "label"=>"Unterlagen", "format"=>"bool");

		return $head;
	}
	
	function filter() {
		
	}
}

?>
