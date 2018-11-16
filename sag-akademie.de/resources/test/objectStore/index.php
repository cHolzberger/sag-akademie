<?

function e ( $str ) {
	echo $str . "\n";
}
include_once ("../../../lib/Mosaik/objectstore.php");

class TestNode {
	function getData() {
		return array("Generic");
	}
	
	function refresh(&$node) { // can add dynamic data here
		
	}
}

class TestNode1 {
	function getData() {
		return array("Generic1");
	}
	
	function refresh(&$node) { // can add dynamic data here
		
	}
}

class TestNode2 {
	function getData() {
		return array("xxroot2");
	}
	
	function refresh(&$node) { // can add dynamic data here
		
	}
}

class TestNode3 {
	function getData() {
		return array("xxroot3");
	}
	
	function refresh(&$node) { // can add dynamic data here
		
	}
}

class TestNode31 {
	function getData() {
		return array("xxroot31");
	}
	
	function refresh(&$node) { // can add dynamic data here
		
	}
}

$store = Mosaik_ObjectStore::init();

$store->get("/")->add("generic", "TestNode");
$store->get("/generic")->add("generic1", "TestNode1");
$store->get("/generic")->add("generic2", "TestNode2");
$store->get("/generic")->add("generic3", "TestNode3");
$store->get("/generic/generic3")->add("generic31", "TestNode31");
$store->get("/generic2")->add("generic2", "TestNode3");
$store->get("/generic3")->add("generic3", "TestNode3");
$store->get("/generic4")->add("generic4", "TestNode3");
$store->get("/generic5")->add("generic5", "TestNode3");



echo "<pre>";
e ( "Info: " . $store->get("/")->name );
e ( "Generic:firstChild: " . $store->get("/generic:firstChild")->name );
e ( "Generic:lastChild " . $store->get("/generic:lastChild")->name );
e ( "Generic:lastChild/generic31 " . $store->get("/generic:lastChild/generic31")->name );
e ( "Generic:count " . $store->get("/generic:countChildren")->name );
e ( "Info: " . $store->get("/generic/generic2")->name );

e("");
e("Obj");
//print_r( $store->get("/")->getData() );
print_r( $store->get("/generic")->getData() );
print_r( $store->get("/generic/generic2")->getData() );
e( $store->get("/generic/generic2:countChildren")->getData() );
e( $store->get("/generic:countChildren")->getData() );
print_r( $store->get("/generic:lastChild/generic3")->getData() );

//print_r( $store->get("/")->getData() );
print_r( $store->get("/generic")->getData() );
print_r( $store->get("/generic/generic2")->getData() );
echo "</pre>";

$store->dump();
?>