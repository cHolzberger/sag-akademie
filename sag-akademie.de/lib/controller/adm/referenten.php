<?php
include("adm/dbcontent.php");

class ReferentImageStore extends MosaikFileStore {
    var $fieldname = "image_upload";
    var $filestore = "/img/";
    var $fileprefix = "referent_";
    var $allowedExtensions = array ( "jpg", "jpeg", "gif", "png");
    var $defaultvalue = 'referent_keinfoto.jpg';
}

/**
 * @
 */
class ADM_Referenten extends ADM_DBContent {
    var $entryClass="Referenten";

    function map($name) {
	return "ADM_Referenten";
    }
    function onNew( & $pr, & $ds) {
	$fn = $this->name()."/new.xml";
	$this->dsDb->add("formaction", "/admin/".$this->name()."/".$this->entryId."?save");

	$pr->loadPage($fn);
    }
    function onSave(&$pr, &$ds) {
	$result = $this->getOneClass($this->entryId);
	$data = $_POST[$this->entryClass];
	$st = new ReferentImageStore();
	$data['image'] = $st->uploadFile(basename($result->image));
	$data["geaendert_von"] = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
	$data["geaendert"] = currentMysqlDatetime();
	if(!isset($data['veroeffentlicht']))
	{
	    $data['veroeffentlicht'] = 0;
	}
	$data['geburtstag'] = mysqlDateFromLocal($data['geburtstag']);
	$data['taetig_seit'] = mysqlDateFromLocal($data['taetig_seit']);
	$data['vertragsdatum'] = mysqlDateFromLocal($data['vertragsdatum']);


	$data['kosten_ganzertag'] = priceToDouble($data['kosten_ganzertag']);
	$data['kosten_halbertag'] = priceToDouble($data['kosten_halbertag']);
	$data['kilometerpauschale'] = priceToDouble($data['kilometerpauschale']);
//	$data['kosten_anfahrt'] = priceToDouble($data['kosten_anfahrt']);
	$data['kosten_uebernachtung'] = priceToDouble($data['kosten_uebernachtung']);

	$result->merge($data);
	$result->save();
	instantRedirect("/admin/".$this->name()."/". $result->id . ";iframe?edit");
    }

    function fetchOne($id, $refresh=false) {
	MosaikDebug::msg($id, "fetchOne");

	$result = Doctrine::getTable("Referent")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
	return $result;
    }

    /*
	function onShowList( & $pr, & $ds) {
		$GLOBALS['dbtableDataFetch'] = $this;
		/*$ret = array();

		$result = Doctrine::getTable("SeminarArt")->findAll(Doctrine::HYDRATE_ARRAY);

		foreach ( $result as $key=>$r ) {
		    $referenten = Doctrine::getTable("ViewSeminarArtReferent")->findBySeminarArtId($r['id']);
		    $tmp = array();
		    foreach ($referenten as $referent) {
			if (!array_key_exists($referent['tag'], $tmp)) $tmp[ $referent['tag'] ] = $r;
			@$tmp[ $referent['tag'] ][ $referent['standort_name'] ] .= $referent['referent_name'] . ", ";
			@$tmp[ $referent['tag'] ][ "tag" ] = $referent['tag'];
		    }

		    foreach ( $tmp as $t) {
			array_push($ret, $t);
		    }
		}
		$ds->add("Referenten", $ret); * /
		$pr->loadPage($this->name().".xml");
	}
    */
}