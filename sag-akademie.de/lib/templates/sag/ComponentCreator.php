<?
class SAG_ComponentCreator extends k_DefaultComponentCreator {
    static $dataStore=null;
    static $pageReader=null;

    public function create ( $class_name, k_Context $context, $namespace = "") {
	$component = parent::create($class_name,$context, $namespace);

	if ( is_subclass_of ( $component ,"SAG_Component") || $component instanceof SAG_Component ) {
	    $component->construct();
	}
	return $component;
    }
    
    protected function instantiate($class_name) {
	if (! class_exists( $class_name ) ) {
	    $fn = CONTROLLER_BASEPATH . strtolower ( str_replace("_", "/",$class_name)) . ".php";
	    MosaikDebug::infoDebug(array($class_name, $fn), "Autoloading");
	    try {
		include ( $fn );
	    } catch(Exception $e) {
		MosaikDebug::errorDebug($e);
	    }
	}

	$ret = new $class_name();
	return $ret;
    }
}

?>