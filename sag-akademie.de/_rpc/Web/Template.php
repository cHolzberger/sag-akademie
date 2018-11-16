<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

include_once("services/TemplateService.php");
/***
 * email templates
 */
class Web_Template {
	function __construct() {
		TemplateService::init();
	}
	
	function getSections() {
		return TemplateService::getSections();
	}
	
	function getTemplates( $section ) {
		return array ( "section" =>$section, "templates" => TemplateService::getTemplates($section));
	}
	
	function getTemplate($id) {
		return TemplateService::getTemplate($id)->toObject();
	}
	
	function saveTemplate($id, $subject, $content) {
		
		qlog ( __CLASS__. "::" . __FUNCTION__ . ": id => $id, subject => $subject" );
		/* @var $t Emai lTemplate */
		$t = TemplateService::getTemplate($id);
		$t->id = $id;
		$t->content = $content;
		$t->subject = $subject;
		qlog ( __CLASS__. "::" . __FUNCTION__ . ": save" );

		$t->save();
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