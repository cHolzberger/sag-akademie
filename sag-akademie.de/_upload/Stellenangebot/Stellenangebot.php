<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class Stellenangebot_Stellenangebot extends Store_File  {
	var $table = "Stellenangebot";
	
	function handleUpload(&$env) {
		qlog(__CLASS__ . "::" . __FUNCTION__ .": Env:");
		
		qdir($_GET);
		qdir($_POST);
		qdir($_FILES);
		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );

		$filename = basename($obj->pdf);
		$obj->pdf = $this->uploadFile ($filename );
		
		$obj->save();
		qlog("Saved new file as: " . $obj->pdf);
		echo $obj->pdf;
	}
	
	function handleDownload(&$env) {		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );
		header("Content-Type: application/pdf");
		header("Expires: Fri, 01 Jan 2010 05:00:00 GMT");
		
		$filename = basename($obj->pdf);
		
		
		echo file_get_contents(APPLICATION_PATH . $this->getFilepath($filename));
	}
	
	function handleDelete(&$env) {
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
		$filename = basename($obj->pdf);
		
		unlink (APPLICATION_PATH . $this->getFilepath($filename));
		$obj->pdf = $this->defaultvalue;
		$obj->save();
		
		echo $obj->pdf;
	}
	
	function showInfo(&$env) {
		
		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
		$filename = $this->getFilepath(basename($obj->pdf));
		
		print_r( stat( APPLICATION_PATH . $this->getFilepath($filename) ));
	}
}