<?php
include("adm/dbcontent.php");

class ADM_Hotels extends ADM_DBContent {
    function map($name) {
	return "ADM_Hotels";
    }

    /*function fetchOne($id, $refresh=false) {
		$result = KontaktTable::detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
		return $result;
	}*/
    function fetchOne($id, $refresh=false) {
	MosaikDebug::msg($id, "fetchOne");

	$result = Doctrine::getTable("Hotel")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
	return $result;
    }
    function onShowDetail(&$pr, &$ds) { // shows a list of "buchungen"
	$hotel = $this->dTable->detailed($this->entryId)->execute()->getFirst();

	$this->dsDb->add("Hotel",$hotel->toArray(true));

	$fn = $this->name() . "/show.xml";
	$pr->loadPage( $fn );
    }
    function onSave(& $pr, & $ds) {
	$result = $this->getOneClass($this->entryId);
	$data = $_POST[$this->entryTable];
	$data["geaendert_von"] = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
	$data["geaendert"] = currentMysqlDatetime();
	$result->merge($data);
	$result->save();
	instantRedirect("/admin/".$this->name()."/". $this->entryId .";iframe?edit");
    }
    function onPreise(&$pr, &$ds) {

	$ds->add ("formaction", "/admin/" .$this->name() . "/".$this->entryId."?savePreise");

	$hotel = $this->dTable->detailed($this->entryId)->execute()->getFirst();
	$this->dsDb->add("Hotel",$hotel->toArray(true));

	$hotel = Doctrine::getTable("Hotel")->getStandardPreis($this->entryId)->execute()->getFirst();
	if ( is_object ( $hotel) ) {
	    $this->dsDb->add("HotelStandardPreis",$hotel->toArray(true));
	}

	$hotelPreise = Doctrine::getTable("Hotel")->getPreise($this->entryId)->execute();
	$ds->add("HotelPreise",$hotelPreise->toArray(true));

	$fn = $this->name() . "/preise.xml";
	$pr->loadPage( $fn );
    }

    function onSavePreise(&$pr, &$ds) {
	// action log component
	if (isset ( $_POST['remove'] )) {
	    foreach ( $_POST['remove'] as $remove ) {
		$entry = Doctrine::getTable("HotelPreis")->findById($remove)->getFirst();
		if ( is_object($entry) ) {
		    $entry->delete();
		}
	    }
	}
	//MosaikDebug::msg($_POST['HotelStandardPreis'],"onSave");

	if ( is_array ( $_POST['HotelStandardPreis'])) {
	    $data = $_POST['HotelStandardPreis'];

	    //MosaikDebug::msg($data,"Data");
	    //$hotel = Doctrine::getTable("Hotel")->getStandardPreis($this->entryId)->execute()->getFirst();

	    $data['zimmerpreis_ez'] = priceToDouble($data['zimmerpreis_ez']);
	    $data['zimmerpreis_dz'] = priceToDouble($data['zimmerpreis_dz']);
	    $data['marge'] = priceToDouble($data['marge']);
	    $data['fruehstuecks_preis'] = priceToDouble($data['fruehstuecks_preis']);


	    if ( Doctrine::getTable("Hotel")->getStandardPreis($this->entryId)->count() == 0 ) {
		$hotel = new HotelPreis();
		$hotel->datum_start = "0000-00-00";
		$hotel->datum_ende =  "0000-00-00";
		$hotel->hotel_id = $this->entryId;
		$hotel->merge($data);
		$hotel->save();
	    } else {
		$hotel = Doctrine::getTable("Hotel")->getStandardPreis($this->entryId)->execute()->getFirst();

		$hotel->merge($data);
		$hotel->save();
	    }
	}

	if ( is_array ( $_POST["HotelPreise"]) ) foreach ( $_POST['HotelPreise'] as $hotelPreis ) {
		$hotelPreis['datum_start'] = mysqlDateFromLocal($hotelPreis['datum_start']);
		$hotelPreis['datum_ende'] = mysqlDateFromLocal($hotelPreis['datum_ende']);


		$hotelPreis['zimmerpreis_ez'] = priceToDouble($hotelPreis['zimmerpreis_ez']);
		$hotelPreis['zimmerpreis_dz'] = priceToDouble($hotelPreis['zimmerpreis_dz']);
		$hotelPreis['marge'] = priceToDouble($hotelPreis['marge']);
		$hotelPreis['fruehstuecks_preis'] = priceToDouble($hotelPreis['fruehstuecks_preis']);

		if ( $hotelPreis['id'] == "new") {
		    $hp = new HotelPreis();
		    $hp->merge($hotelPreis);
		    $hp->hotel_id = $this->entryId;
		    $hp->save();
		} else {
		    $hp = Doctrine::getTable("HotelPreis")->findById($hotelPreis['id'])->getFirst();
		    $hp->merge($hotelPreis);
		    $hp->save();
		}
	    }
    }
}
