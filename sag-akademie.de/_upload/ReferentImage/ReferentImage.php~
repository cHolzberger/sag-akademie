<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class ReferentImage_ReferentImage extends Store_File  {
	function handleUpload(&$env) {
		qlog(__CLASS__ . "::" . __FUNCTION__ .": Env:");
		
		qdir($_GET);
		qdir($_POST);
		qdir($_FILES);
		
		$referentId = $env->id;
		
		$referent = Doctrine::getTable("Referent")->find ( $referentId );

		$filename = basename($referent->image);
		$referent->image = $this->uploadFile ($filename );
		
		qlog ("image: ". $referent->image);
		
		$referent->save();
		echo $referent->image;
	}
	
	function handleDownload(&$env) {
	
		$referentId = $env->id;
		$referent = Doctrine::getTable("Referent")->find ( $referentId );
		header("Content-Type: image/jpg");
		header("Content-Disposition: inline; filename=\"{$referent->name}-{$referent->vorname}.jpg\"");
		header("Expires: Fri, 01 Jan 2010 05:00:00 GMT");
		
		$filename = basename($referent->image);
		
		echo file_get_contents(APPLICATION_PATH . $this->getFilepath($filename));
	}
	
	function handleDelete(&$env) {
		$referentId = $env->id;
		
		$referent = Doctrine::getTable("Referent")->find ( $referentId );		
		$filename = basename($referent->image);
		
		unlink (APPLICATION_PATH . $this->getFilepath($filename));
		$referent->image = $this->defaultvalue;
		$referent->save();
		
		echo $referent->image;
	}
	
	function showInfo(&$env) {
		$referentId = $env->id;
		
		$referent = Doctrine::getTable("Referent")->find ( $referentId );		
		$filename = $this->getFilepath(basename($referent->image));
		
		print_r( stat( APPLICATION_PATH . $this->getFilepath($filename) ));
	}
}