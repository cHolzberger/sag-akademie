<?
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class Mosaik_Report {
	var $content=array();
	var $status=array();

	function add($import) {
		$this->content[] = $import;
	}

	function status($status) {
		if ( $status == true) {
			$this->status[]='<div style="border: 1px solid green; background-color: lightgreen; float: right;">OK</div><div style="height: 1px; clear: both;">&nbsp;</div>';
		} else {
			$this->status[]='<div style="border: 1px solid red; background-color: lightred; float: right;">Fehler</div><div style="height: 1px; clear: both;">&nbsp;</div>';
		}
	}

	function output() {
		$content = "";
		for($i=0; $i<count($this->content); $i++) {
			$content .= '<div style="border-top: 1px solid grey;">' .$this->content[$i] . $this->status[$i]."</div>";
		}
		return $content;
	}
}
?>