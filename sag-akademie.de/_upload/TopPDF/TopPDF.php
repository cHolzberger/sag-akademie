<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class TopPDF_TopPDF extends Store_File  {
	function handleUpload(&$env) {
		qlog(__CLASS__ . "::" . __FUNCTION__ .": Env:");
		
		qdir($_GET);
		qdir($_POST);
		qdir($_FILES);
		
		$seminarId = $env->id;
		$field = $env->get("field", "info_link");
		
		$seminar = Doctrine::getTable("SeminarArt")->find ( $seminarId );
		
		$filename = basename($seminar->$field);
		$seminar->$field = $this->uploadFile ($filename );
		qlog ("PDF $field: ". $seminar->$field);
		$seminar->save();
		echo $seminar->$field;
	}
	
	function handleDownload(&$env) {
		header("Content-Type: application/pdf");
		
		$seminarId = $env->id;
		$field = $env->get("field", "info_link");
		
		$seminar = Doctrine::getTable("SeminarArt")->find ( $seminarId);
		$filename = basename($seminar->$field);
		
		echo file_get_contents(APPLICATION_PATH . $this->getFilepath($filename));
	}
	
	function handleDelete(&$env) {
		$seminarId = $env->id;
		$field = $env->get("field", "info_link");
		
		$seminar = Doctrine::getTable("SeminarArt")->find ( $env->seminarId);
		
		$filename = basename($seminar->$field);
		
		unlink (APPLICATION_PATH . $this->getFilepath($filename));
		$seminar->$field = "";
		$seminar->save();
		
		echo $seminar->$field;
	}
	
	function showInfo(&$env) {
		$seminarId = $env->id;
		$field = $env->get("field", "info_link");
		
		$seminar = Doctrine::getTable("SeminarArt")->find ( $env->id);
		$filename = basename($seminar->$field);
		
		print_r( stat( APPLICATION_PATH . $this->getFilepath($filename) ));
	}
}