<? 
class ADM_DB_Labels extends SAG_Admin_Component {
    function map($name) {
	return "ADM_DB_Labels";
    }

    function construct() {
	$this->createPageReader();
	$this->dsDb = new MosaikDatasource("dbtable");
	$this->pageReader->addDatasource($this->dsDb);
    }

    function forward($class, $namespace="") {
	$this->entryId = $this->next();

	$fields = Doctrine::getTable($this->entryId)->getColumns();
	foreach ( $fields as $t=>$v ) {
	    $x = array ();
	    $x['name'] = $t;
    	    $x['type'] = $v['type'];

	    $tmp[]=$x;
	}
	$this->dsDb->add("Field", $tmp);
	$this->pageReader->loadPage("db/labels/edit");

	return $this->pageReader->output->get();
    }

    function GET() {
	$tables = Doctrine::getLoadedModels();
	array_multisort ($tables);
	$tmp=array();
	foreach ( $tables as $t ) {
	    $x = array ();
	    $x['name'] = $t;
	    $tmp[]=$x;
	}
	$this->dsDb->add("Table", $tmp);
	$this->pageReader->loadPage("db/labels");

	return $this->pageReader->output->get();
    }
}
?>