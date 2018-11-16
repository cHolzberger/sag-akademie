<?php
/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */

class JSResourceFactory {
	static private $instance = NULL;

	static public function getInstance() {
		if ( ! JSResourceFactory::$instance ) {
			JSResourceFactory::$instance = new JSResourceBundle();
		}
        return JSResourceFactory::$instance;
    }

     private function __construct(){}
     private function __clone(){}
}


class CSSResourceFactory {
	static private $instance = NULL;

	static public function getInstance() {
		if ( ! CSSResourceFactory::$instance ) {
			CSSResourceFactory::$instance = new CSSResourceBundle();
		}
        return CSSResourceFactory::$instance;
    }

     private function __construct(){}
     private function __clone(){}
}

class JSResourceBundle extends MosaikResourceBundle {
	private $tagname = "script";
	private $attributes = array(
	"tag" => "script",
 	"type" => "text/javascript"
	);

	function add ($item, $content=NULL) {
		$this->list[] = array("file" => $item, "content" => $content);
	}
}

class CSSResourceBundle extends MosaikResourceBundle {
	private $tagname = "link";
	private $attributes = array(
	"rel"=>"stylesheet",
	"type"=>"text/css"
	);

	function add ($item, $content=NULL) {
		$this->list[] = array("file" => $item, "content" => $content);
	}
}

class MosaikResourceBundle {
	public $list = array();

	function __construct () {
		$this->list=array();
	}

	function add ($item) {
		$this->list[]=$item;
	}

	function get() {
		return $this->list;
	}
}

?>
