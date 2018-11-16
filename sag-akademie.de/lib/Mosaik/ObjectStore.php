<?php
/***
 * ObjectStore implementation
 * manages generic objects via paths and lets the programmer query it through xpath expressions
 * 
 * store architecture: 
 *
 * 		Mosaik_ObjectNode {
 * 			"children" => array of MosaikObjectNodes => 
 * 			"node" => classname (to be instanciated on load
  *			"name" => name without slashes
  			"info" => information for the path browser
 * 		} 
 * 
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */
class Mosaik_ObjectNode {
    public $children, $className, $info, $name;

    private $instance=null;

    public function __construct ( $name, $className, $info="",  $children=null) {
	$this->name = $name;

	if ( is_object ( $className) ) { // classname was actually an object
	    $this->instance = $className;
	    $this->className = "unknown";
	} else {
	    $this->className = $className;
	}
	if ( $children == null ) {
	    $this->children = array();
	} else {
	    $this->children = $children;
	}
	$this->info = $info;
    }

    public function __call( $method, $param) { // forwards any calls to the object for this node
	$this->obj();
	return call_user_func_array(array(&$this->instance, $method),$param);
    }

    public function &firstChild() {
	return $this->children[0];
    }

    public function countChildren() {
	$x = new Mosaik_ObjectNode("dynamic", "Mosaik_CountNode", "dynamic count");
	$x->obj()->data = count($this->children);
	return $x;
    }

    public function &lastChild() {
	return $this->children[count($this->children)-1];
    }

    public function &add ($path, $classname, $info="No Info") { // adds chidren
	$x = new Mosaik_ObjectNode($path, $classname, $info);
	$this->children[]= $x;
	return $x;
    }

    public function refresh() { // refreshes the children for this object
	if ( in_array ("refresh", get_class_methods( $this->obj() ))) {
	    $this->obj()->refresh($this);
	}
    }

    public function &obj() { // returns the object for this node
	if ( $this->instance == null ) {
	//auto loader
	    if ( !class_exists( $this->className)) {
		include_once("stores/" . $this->className . ".php");
	    }
	    $this->instance = new $this->className;
	}
	return $this->instance;
    }

    public function cleanup() {
	$this->children = array();
    }
}

class Mosaik_PersistentNode {
    public $data = null;
    function getData() {
	return array("presistent data");
    }

    function refresh(&$node) {

    }
}

class Mosaik_DummyNode {
    public $data = 0;
    function getData() {
	return null;
    }

    function refresh(&$node) { // can add dynamic data here

    }
}

class Mosaik_CountNode {
    public $data = 0;
    function getData() {
	return $this->data;
    }

    function refresh(&$node) { // can add dynamic data here

    }
}

class Mosaik_RootNode {
    function getData() {
	return array("root");
    }

    function refresh(&$node) { // can add dynamic data here

    }
}

class Mosaik_ObjectStore {
    public $rootNode;
    static $instance=null;

    static function &getPath($path) {
	return Mosaik_ObjectStore::init()->get($path);
    }

    static function &init( $path=null ) {
	if ( Mosaik_ObjectStore::$instance == null ) {
	    Mosaik_ObjectStore::$instance = new Mosaik_ObjectStore();
	}

	if ( $path != null ) {
	    return Mosaik_ObjectStore::$instance->get($path);
	} else {
	    return Mosaik_ObjectStore::$instance;
	}
    }

    function __construct ( ) {
	$this->rootNode = new Mosaik_ObjectNode("", "Mosaik_RootNode", "Root Node");

    }

    function &get ($path, $refresh=false) {
	$splitPath = explode( "/", $path );
	$currentNode = $this->rootNode;

	if ( $path == "/" ) {
	    return $currentNode;
	}

	foreach ( $splitPath as $pathElement ) {
	    for ( $i=0; $i<count($currentNode->children); $i++) {
	    // lets do some xpath stuff
	    // node:i
		$query = explode (":", $pathElement);
		$name = $query[0];
		$selector = "";
		if ( count($query) > 1) {
		    $selector = $query[1];
		}

		if ( $currentNode->children[$i]->name == $name ) {
		    if ( !empty ($selector)) {
			$currentNode = $currentNode->children[$i]->$selector();
		    } else {
			$currentNode = $currentNode->children[$i];
		    }
		    break;
		}
	    }

	}
	return $currentNode;
    }

    function dump() {
	echo "<pre>Store Dump:\n";
	$root = $this->get("/");
	$tab = 0;
	echo "|". $root->name;
	echo ": ";
	echo $root->info;
	echo "\n";

	foreach ( $root->children as $child ) {
	    $tab = 0;
	    $this->dumpElement($child, $tab);
	}

	echo "</pre>";
    }

    function dumpElement ( $elem, &$tab) {
	$tab ++;
	$itab = $tab;
	$tabs = "+";
	for($i=0; $i<$tab; $i++) {
	    $tabs .= "--";
	}
	$tabs .= "> ";
	echo $tabs . $elem->name;
	echo ": ";
	echo $elem->info;
	echo "\n";
	$elem->refresh();
	if ( count ( $elem->children ) > 0 ) {
	    foreach ( $elem->children as $c ) {
		$tab = $itab;
		$this->dumpElement($c,$tab);
	    }
	}
    }
}

?>