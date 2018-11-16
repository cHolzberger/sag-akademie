<?php

/**
 * Mosaik File Store
 *
 * general purpose file store class
 * features:
 * * move uploaded files to new location
 * * unlink files
 * * overwrite existing files or generate new name
 * * Set default values like "",False, true
 *
 * moves or deletes file specifyed by fieldname
 * the new file name will be generated in the format: WEBROOT/filestore/fileprefix_UID.matchedExtension
 *
 * it uses the $_POST vars $fieldname and $fieldname . "_unlink"
 *
 * can be used with tag mform:upload
 *
 *
 * Changelog:
 *
 * 27.02.2010 <sg@mosaik-software.de>:
 * new feature: Var $defaultset boolen: false = return only the defaultvalue / true = $filestore . $defaultvalue
 *
 * 26.02.2010 <ch@mosaik-software.de>:
 * First Version
 * Will move uploaded files around
 *
 * @author Christian Holzberger <ch@mosaik-software.de>
 * @author Sebastian Gieselmann <sg@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */
class Store_File {

	/**
	 * name of the $_FILES variable
	 * @var String
	 */
	var $fieldname = "unknown";
	/**
	 * the pathname of the store, after the webroot
	 * @var String
	 */
	var $filestore = "unknown";
	/**
	 * path to webroot;
	 * @var String
	 */
	var $webroot = WEBROOT;
	/**
	 * prefix prepended to newly generated filenames
	 * @var String
	 */
	var $fileprefix = "unknown";
	/**
	 * Array of allowed extensions
	 * @var Array
	 */
	var $allowedExtensions = array();
	/**
	 * this will be the empty image default value
	 * @var Bool
	 */
	var $defaultset = true;
	/**
	 * this will be the empty file default value
	 * @var String
	 */
	var $defaultvalue = "";
	
	function handleUpload(&$env) {
		
	}
	
	function handleDownload(&$env) {
		
	}
	
	function showInfo(&$env) {
		
	}
	
	function handleDelete ( &$env) {
		
	}
	
	function getFilepath ($filename) {
		return $this->filestore . $filename;
	}
	/**
	 * moving uploaded files
	 *
	 * this function will move the newly uploaded file in place
	 * you can pass $filename to this function to specify the existing image.
	 * it will be overwritten by the upload function if it exists and differs
	 * from $defaultvalue. a new filename is generated otherwise
	 * @param String $filename
	 * @param String $fieldname overwrite the objects fieldname with $fieldname (optional)
	 * @return String new filename
	 */
	function uploadFile($filename, $fieldname = null) {
		if ($fieldname != null)
			$this->fieldname = $fieldname;

		qlog(__CLASS__ . "::" . __FUNCTION__ . ": handling file upload filename => {$filename}" );
		$fullpath = $this->filestore . $filename;
		$fieldname = $this->fieldname;
		$newname = "";
		if (!$this->defaultset && empty($filename)) {
			$ret = $this->defaultvalue;
		} else {
			if (empty($filename))
				$filename = $this->defaultvalue;
			$ret = $this->filestore . $filename;
		}

		if (isset($_FILES[$fieldname]) && is_uploaded_file($_FILES[$fieldname]['tmp_name'])) {
			$pathinfo = pathinfo($_FILES[$fieldname]["name"]);

			if (isset($pathinfo["extension"]))
				foreach ($this->allowedExtensions as $ext) {
					if (strtolower($pathinfo["extension"]) == strtolower($ext)) {
						$newname = $this->generateNewName($ext);
						move_uploaded_file($_FILES[$fieldname]["tmp_name"], WEBROOT . $newname) && $ret = $newname;
						break;
					}
				}
		}
		return $ret;
	}

	/**
	 * generate a new filname
	 *
	 * gerate a new unique filename.
	 *
	 * exp: [pdf/][pdf_][1234567898765432].[pdf]
	 *
	 * @param String $ext extension of file.
	 * @return String new filename.
	 */
	function generateNewName($ext) {
		$newid = substr(md5(microtime()), 0, 16);
		$newname = $this->filestore . $this->fileprefix . $newid . "." . $ext;
		while ( file_exists ( $newname )) {
			$newid = substr(md5(microtime()), 0, 16);
			$newname = $this->filestore . $this->fileprefix . $newid . "." . $ext;
		}
		
		qlog("Generated new Name: $newname");
		return $newname;
	}
	
	/** 
	 * setup this store from config object
	 * 
	 * @param Object $config 
	 */
	function setup( $config ) {
		qlog("setup from config");
				
		$this->fieldname = $config->fieldname;
		$this->filestore = $config->filestore;
		$this->fileprefix = $config->fileprefix;
		$this->allowedExtensions = $config->allowedExtensions;
		$this->defaultset = $config->defaultset;
		$this->defaultvalue = $config->defaultvalue;
	}
}

