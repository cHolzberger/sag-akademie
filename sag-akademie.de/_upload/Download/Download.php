<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class Download_Download extends Store_File  {
	var $table = "Download";
	
	function handleUpload(&$env) {
		qlog(__CLASS__ . "::" . __FUNCTION__ .": Env=>" . $env->id);
		qdir($_FILES);
		qdir($_GET);
		$obj = Doctrine::getTable($this->table)->find ( $env->id );

		$filename = basename($obj->file);
		
		$obj->file = $this->uploadFile ( $filename );
		
		$obj->save();
		echo $obj->file;
	}
	
	function handleDownload(&$env) {
		header("Content-Type: application/pdf");
		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
		$filename = basename($obj->file);
		
		echo file_get_contents(APPLICATION_PATH . $this->getFilepath($filename));
	}
	
	function handleDelete(&$env) {
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
		$filename = basename($obj->file);
		
		unlink (APPLICATION_PATH . $this->getFilepath($filename));
		$obj->file = $this->defaultvalue;
		$obj->save();
		
		echo $obj->file;
	}
	
	function showInfo(&$env) {
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
		$filename = $this->getFilepath(basename($obj->file));
		
		print_r( stat( APPLICATION_PATH . $this->getFilepath($filename) ));
	}
}
