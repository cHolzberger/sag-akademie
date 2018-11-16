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
class MosaikFileStore {
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
    function uploadFile ( $filename, $fieldname = null) {
	if ( $fieldname != null ) $this->fieldname = $fieldname;
	
	
	$fullpath = $this->filestore . $filename;
	$fieldname = $this->fieldname;
	$newname = "";
	if(!$this->defaultset && empty($filename)) {
		$ret = $this->defaultvalue;
	}else{
		 if ( empty (  $filename) ) $filename = $this->defaultvalue;
		$ret = $this->filestore . $filename;
	}
	

	if ( isset ( $_POST[$fieldname . "_unlink"] ) || isset ($_GET[$fieldname . "_unlink"]) ) {
	    if(is_file (WEBROOT . $fullpath) && $filename !=  $this->defaultvalue ) unlink(WEBROOT . $fullpath);
	    $filename= $this->filestore. $this->defaultvalue;
	     if(!$this->defaultset) {
		$ret = $this->defaultvalue;
	     }else{
		$ret = $filename;
	     }

	}

	if( isset ( $_FILES[$fieldname] ) && is_uploaded_file($_FILES[$fieldname]['tmp_name'])  ) {
	    $pathinfo = pathinfo($_FILES[$fieldname]["name"]);

	    if ( isset($pathinfo["extension"])) foreach ( $this->allowedExtensions as $ext) {
		    if( $pathinfo["extension"] == $ext ) {
			$newname = $this->filestore  . $filename;

			if($filename == $this->defaultvalue) {
			    $newname = $this->generateNewName($ext);
			}

			if ( is_file ( WEBROOT . $newname) ) unlink ( WEBROOT . $newname );
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
    function generateNewName( $ext ) {
	$newid = substr ( md5(microtime()), 0, 16 );
	$newname = $this->filestore . $this->fileprefix . $newid . "." . $ext;

	return $newname;
    }
}
?>
