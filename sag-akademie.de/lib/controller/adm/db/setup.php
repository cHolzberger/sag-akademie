<?php
include_once("config.php");

class ADM_DB_Setup extends SAG_Admin_Component {
	function GET() {
	    $text = "";
	 //  if (getenv("PHP_MCM_DEBUG_CONFIG") != "online") {
		$GLOBALS['path'][] = array('name'=> $this->name(), 'url' => $this->url());
		$text = "<span><h1>DB Setup</h1>";
		$text .= "<div>Models from DB...";
		Doctrine::generateModelsFromDb(WEBROOT . '/lib/db/models/', array('doctrine'), array('generateTableClasses' => false));
		$text .=" OK</div>";
		/*
		$newVersion = MosaikConfig::getVar("dbversion") +1;
		$text .= "<div>YAML from Models(V: $newVersion)... ";
		
		Doctrine::generateYamlFromModels(WEBROOT . "/lib/Doctrine/yaml/schema_v_". $newVersion . ".yml", WEBROOT . '/lib/Doctrine/models');
		$text .= " OK</div>";
		 * 
		 */
	   //}
		/*
		$text .= "<div>Import newsl...";
		$newlimport = Doctrine::getTable("ViewMailingImport")->findAll();
		foreach ($newlimport as $newl) {
		    $text .= $newl->email . "<br/>";
		    $x = new AkquiseKontakt();
		    $x->email = $newl->email;
		    $x->vorname = $newl->Vorname;
		    $x->name = $newl->Nachname;
		    $x->firma = $newl->Firma;
		    $x->ort = $newl->Ort;
		    $x->plz = $newl->PLZ;
		    $x->strasse = $newl->Strasse;
		    $x->tel = $newl->Telefon;
		    $x->fax = $newl->Fax;
		    $x->abteilung = $newl->Abteilung;
		    $x->kontakt_quelle = $newl->Quelle;
		    $x->wiedervorlage = 9;
		    $x->save();
		}*/
		return $text . "<div>Done</div></span>";
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
}
?>
