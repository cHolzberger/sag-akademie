<?php
/**
 * @classDescription Configuration Class for the page reader
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */
class MosaikPageReaderConfig {
	public $elementBasepath;
	public $pageBasepath;
	public $encoding;
	public $variable;

	function __construct ($pageBasePath = "", $elementBasePath = "", $encoding = "utf8") {
		if (empty ( $pageBasePath )) {
			$pageBasePath = MosaikConfig::getVar("srvPagePath");
		}
		if (empty ( $elementBasePath )) {
			$elementBasePath = ELEMENT_PATH;
		}
		$this->httpBasepath = "templates/pages/httpstatus/";
		$this->pageBasepath = $pageBasePath;
		$this->elementBasepath = $elementBasePath;
		$this->encoding = $encoding;
		$this->variable = array(
			"page_background" => "/img/header_bg_gross.jpg",
			"DOCTYPE" => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
		);
	}

	static function getDefault( $type = "html", $pagedir = "") {
		$basepath = ELEMENT_BASE_PATH;
		return new MosaikPageReaderConfig ( $pagedir,  "$basepath/$type" );
	}
}
?>
