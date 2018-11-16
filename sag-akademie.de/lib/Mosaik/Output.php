<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class MosaikOutput { #class to mess around with content afterwards
	var $content=array();
	public static $replacement=array();

	function add($str) {
	    array_push($this->content, $str);
	}

	function startTag($tag,$arguments) {
		if ($tag == "html") {
			array_push($this->content, sprintf( '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">%s', "\n"));
		}
		array_push( $this->content, "<$tag");
		if ($arguments) foreach ($arguments as $key=>$val) {
			array_push ($this->content, " $key=\"$val\"");
		}
		array_push ($this->content, ">");

	}

	function shortTag($tag, $arguments) {
		array_push($this->content, "<$tag");
		if ($arguments) foreach ($arguments as $key=>$val) {
			array_push($this->content, " $key=\"$val\"");
		}

		array_push($this->content, " />\n");
	}

	function closeTag($tag) {
		array_push($this->content,"</$tag>\n");
	}

	function addReplacement($name, $value) {
		self::$replacement[$name] = $value;
	}

	function getReplacement($name, $default = false) {
	    if (array_key_exists($name, self::$replacement)) {
		return self::$replacement[$name];
	    } return $default;
	}

	function get($repl=array()) {
		$arr = array_merge(self::$replacement, $repl);
		$content = implode("",$this->content);

		foreach ($arr as $key=>$value) {
			$content = str_replace("%". $key. "%", $value, $content );
		}
		return $content;
	}

	function clear() {
		$this->content = array();
	}

}


?>
