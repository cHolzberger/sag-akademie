<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class MosaikHtmlDropDownBuilder {
	private $_possibleValues;
	private $_name;
	private $_preselect;
	public $container;
	public $element;
	public $labelField = "name";
	public $args;

	function __construct( $name , $preselect=0, $args="", $disabled = false) {
		if ( $disabled ) {
			$this->container = '<select name="%s" readonly="readonly" %s>%s</select>';
		} else {
			$this->container = '<select name="%s" %s>%s</select>';
		}
		$this->args = $args;
		$this->element = '<option value="%s" checked="%s">%s</option>';
		$this->_name = $name;
		$this->_preselect = $preselect;
	}

	function getPossibleValues() {
		return $this->_possibleValues;
	}

	function setPossibleValues ($data) {
		$this->_possibleValues = $data;
	}

	function toHtml() {
		$opt = "";
		$checked = "";

		for ( $i=0; $i < count($this->_possibleValues); $i++) {
			if ( $this->_possibleValues[$i]['id'] == $this->_preselect ) $checked = "checked";
			else $checked = "";

			$opt .= sprintf($this->element, $this->_possibleValues[$i]['id'], $checked, $this->_possibleValues[$i][$this->labelField]);
		}

		$opt = sprintf($this->container, $this->_name, $this->args, $opt);
		return $opt;
	}
}
?>