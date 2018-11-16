<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class Kooperationspartner_Kooperationspartner extends Store_File  {
	var $table = "Kooperationspartner";
	
	function handleUpload(&$env) {
		qlog(__CLASS__ . "::" . __FUNCTION__ .": Env:");
		
		qdir($_GET);
		qdir($_POST);
		qdir($_FILES);
		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );

		$filename = basename($obj->logo);
		$obj->logo = $this->uploadFile ($filename );
		
		
		
		$obj->save();
		echo $obj->logo;
	}
	
	function handleDownload(&$env) {

		
		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );
		header("Content-type: image/jpg");
		header("Content-Disposition: inline; filename=\"{$obj->name}.jpg\"");
		header("Expires: Fri, 01 Jan 2010 05:00:00 GMT");
		
		$filename = basename($obj->logo);
		
		
		echo file_get_contents(APPLICATION_PATH . $this->getFilepath($filename));
	}
	
	function handleDelete(&$env) {
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
		$filename = basename($obj->logo);
		
		unlink (APPLICATION_PATH . $this->getFilepath($filename));
		$obj->logo = $this->defaultvalue;
		$obj->save();
		
		echo $obj->logo;
	}
	
	function showInfo(&$env) {
		
		
		$obj = Doctrine::getTable($this->table)->find ( $env->id );		
		$filename = $this->getFilepath(basename($obj->logo));
		
		print_r( stat( APPLICATION_PATH . $this->getFilepath($filename) ));
	}
}