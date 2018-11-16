<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

include_once("services/TemplateService.php");

class Web_Text {
	private $_table="WebText";
	private $_view="";

	/**
	 *
	 * @return NeuigkeitTable
	 */
	private function table() {
		return Doctrine::getTable($this->_table);
	}

	/**
	 *
	 * @param string $id
	 * @return Neuigkeit
	 */
	private function findObj($id) {
		return $this->table()->find($id);
	}

	/**
	 *
	 * @param string $id
	 * @return array
	 */
	function find($id) {
		$result = array();
		$obj = $this->findObj($id);
		if (!is_object($obj)) {
			$obj = new WebText();
			$obj->id=$id;
			$obj->save();
		}
		$result = $obj->toArray(true);
	
		return $result;
	}

	/**
	 *
	 * @param string $id
	 * @param object $obj
	 * @return Neuigkeit
	 */
	function save($id, $obj) {
		$result = array();

		$result = $this->findObj($id);
		$mergeData = mergeFilter ( $this->_table, $obj);

		$result->merge($mergeData);
		$result->save();
	
		return $result->toArray(true);
	}
	
	/**
	 * create news
	 * @param string $titel
	 * @param string $text
	 * @return WebText
	 */
	function create($name,  $text) {
		$aufgabe = new WebText ();
		$user = Identity::get();
		
		$aufgabe->name = $name;
		$aufgabe->text = $text;
		
		$aufgabe->geaendert_von = $user->getId();
		$aufgabe->geaendert = currentMysqlDatetime();
				
		$aufgabe->save();
		$aufgabe->refresh();
		
		return $aufgabe->toArray(true);
	}
	
	/**
	 * delete news entry
	 * @param string $id
	 */
	function delete($id) {
		$this->findObj($id)->remove();
	}
}


/*
 *

include_once("adm/dbcontent.php");
include_once("services/TemplateService.php");

class ADM_Dokumente extends ADM_DBContent {
    public function map ($name) {
	return "ADM_Dokumente";
    }

    function POST() {
	$this->init();
	$templates = TemplateService::getTemplates();
	for ( $i=0, $count = count($templates); $i<$count; $i++ ) {
	    if ( array_key_exists ($templates[$i]->id, $_POST)) {
		$templates[$i]->content = $_POST[$templates[$i]->id];
		TemplateService::saveTemplate ( $templates[$i]);
	    }
	}

	MosaikConfig::setPersistent("geburtstagsCheck","subject", $_POST['geb_mail_betreff']);
	MosaikConfig::setPersistent("teilnehmerCheck","subject1", $_POST['warnung1_mail_betreff']);
	MosaikConfig::setPersistent("teilnehmerCheck","subject2", $_POST['warnung2_mail_betreff']);
	MosaikConfig::setPersistent("teilnehmerCheck","subject3", $_POST['warnung3_mail_betreff']);

	MosaikConfig::setPersistent("teilnehmerNichtErreicht","check1", $_POST['warnung1_mail_check']);
	MosaikConfig::setPersistent("teilnehmerNichtErreicht","check2", $_POST['warnung2_mail_check']);
	MosaikConfig::setPersistent("teilnehmerNichtErreicht","check3", $_POST['warnung3_mail_check']);

	return $this->onShowList($this->pageReader, $this->dsDb);
    }
     
    function onShowList(& $pr, & $ds) {
	$GLOBALS['dbtableDataFetch'] = $this;
	$templates = TemplateService::getTemplates();
	for ( $i=0, $count = count($templates); $i<$count; $i++ ) {
	    $ds->add($templates[$i]->id , $templates[$i]->content);
	}

	// geburtstags check
	$ds->add("geb_mail_betreff",  MosaikConfig::getPersistent("geburtstagsCheck","subject"));
	$ds->add("warnung1_mail_betreff",  MosaikConfig::getPersistent("teilnehmerCheck","subject1"));
	$ds->add("warnung2_mail_betreff",  MosaikConfig::getPersistent("teilnehmerCheck","subject2"));
	$ds->add("warnung3_mail_betreff",  MosaikConfig::getPersistent("teilnehmerCheck","subject3"));

	$ds->add("warnung1_mail_check",  MosaikConfig::getPersistent("teilnehmerNichtErreicht","check1"));
	$ds->add("warnung2_mail_check",  MosaikConfig::getPersistent("teilnehmerNichtErreicht","check2"));
	$ds->add("warnung3_mail_check",  MosaikConfig::getPersistent("teilnehmerNichtErreicht","check3"));

	$ds->log();
	$pr->loadPage($this->name().".xml");
    }

}

*/