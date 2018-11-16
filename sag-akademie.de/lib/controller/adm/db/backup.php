<? 
class ADM_DB_Backup extends SAG_Admin_Component {
	function map($name) {
		return "SAG_DBBackup";
	}
	
	function construct() {
		$this->path = WEBROOT . "/resources/db";
		$this->createPageReader();
		$this->dsDb = new MosaikDatasource("dbtable");
		$this->pageReader->addDatasource($this->dsDb);
	}
	
	function forward($class, $namespace="") {
			$this->entryId = $this->next();
	}
	
	function GET() {
		$files = scandir($this->path);
		$tmp = array();
		
		foreach ( $files as $file) {
			if ( $file != ".." && $file != "." && $file != ".svn" ) {
				$year = substr ( $file, 0,4);
				$month = substr ($file, 4,2);
				$day = substr ($file, 6,2);
				$hour = substr ($file, 8,2); 
				$minute = substr ($file, 10,2);
				
				if ( !array_key_exists( "$year$month$day", $tmp)) {
					$tmp ["$year$month$day"] = array("headline"=>"$day.$month.$year", "items" => array());
				}
				
				$tmp ["$year$month$day"]['items'][] = array(
					"time" => "$hour:$minute", 
					"title" =>  $file, 
					"link" => "/resources/db/" . $file
				);
			}
		}
		
		$this->dsDb->add("files", $tmp);
		$this->pageReader->loadPage("db/backup.xml");
		
		return $this->pageReader->output->get();
	}
}
?>