<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class Aktuelles_Aktuelles extends Store_File  {
	var $table = "Neuigkeit";
	
	function handleUpload(&$env) {
		qlog(__CLASS__ . "::" . __FUNCTION__ .": Env: ");
		qdir($_FILES);
		$obj = Doctrine::getTable($this->table)->find ( $env->id );

		$filename = basename($obj->pdf);
		
		$obj->pdf = $this->uploadFile ( $filename );
		
		$obj->save();
		echo $obj->pdf;
	}
	
	function handleDownload(&$env) {
		header("Content-Type: application/pdf");
		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
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