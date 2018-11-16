<?php
include("adm/dbcontent.php");

class NeuigkeitPdfStore extends MosaikFileStore {
    var $fieldname = "pdf_upload";
    var $filestore = "/pdf/";
    var $fileprefix = "news_";
    var $allowedExtensions = array ("pdf");
    var $defaultset = false;
    var $defaultvalue = "";
}

class ADM_Neuigkeiten extends ADM_DBContent {
    function map($name) {
	return "ADM_Neuigkeiten";
    }
    function onSave(& $pr, & $ds) {
	$result = $this->getOneClass($this->entryId);
	$data = $_POST[$this->entryClass];
	$st = new NeuigkeitPdfStore();
	MosaikDebug::msg($data, 'DATA');
	$data['pdf'] = $st->uploadFile(basename($result->pdf));
	$data["geaendert_von"] = Mosaik_ObjectStore::init()->get("/current/identity")->uid();
	$data["geaendert"] = currentMysqlDatetime();
	$data["datum"] = mysqlDateFromLocal($data["datum"]);
	$result->merge($data);
	$result->save();

	$download = new XDownload();
	$download->file_path = basename($data["pdf"]);
	$download->store = $st->filestore;
	$download->download_name = $_FILES[$st->fieldname]["name"];
	$download->save();
	instantRedirect("/admin/".$this->name()."/". $result->id .";iframe?edit");
    }
    function onNew( & $pr, & $ds) {
	$fn = $this->name()."/new.xml";
	$this->dsDb->add("formaction", "/admin/".$this->name()."/".$this->entryId."?save");

	$pr->loadPage($fn);
    }
    function fetchOne($id, $refresh=false) {
	MosaikDebug::msg($id, "fetchOne");

	$result = Doctrine::getTable("Neuigkeit")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
	return $result;
    }
}
?>
