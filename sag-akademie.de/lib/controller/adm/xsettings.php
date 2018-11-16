<?php
include("adm/dbcontent.php");

class ADM_XSettings extends ADM_DBContent {
	var $dbClass = "XSettings";
	
	function map($name) {
		return "ADM_XSettings";
	}

	function fetchOne($id, $refresh=false) {
		$result = Doctrine::getTable("Kontakt")->detailed()->fetchOne(array($id), Doctrine::HYDRATE_ARRAY);
		return $result;
	}
	
	function POST() {
		$GLOBALS['firephp']->log("ADM_XSettings::POST");
		$ident = $this->identity();
		
		$data = json_decode ( $this->body("data", "[]"), true );
		$type = $this->body("type","list");
		$lastRefresh = $this->body("now", "0");
		$table = $this->getTable();

		foreach ($data as $property) {
			$data = $table->findBySql('name = ? AND namespace = ? AND user_id = ?', array ($property['name'], $property['namespace'], $ident->uid() )); // only find values changed before the data was submitted // do not override
			
			$obj = $data[0];
			$obj->user_id = $ident->uid();
			$obj->merge($property);
			$obj->save();
		}
		$GLOBALS['firephp']->log($type,"type");
		if ( $type == "list" ) {
			return $this->GET();
		}
		
	}
	
	function renderHtml() {
		$this->createPageReader();
		$this->getTable();
		$dbtable = new MosaikDatasource("dbtable");
		$this->pageReader->addDatasource($dbtable);
		$this->pageReader->loadPage( $this->name() );
		
		return $this->pageReader->output->get();
	}
	
	function renderJson($lastRefresh="0000-00-00 00:00:00") {
		$ident = $this->identity();
		
		$table = $this->getTable();
		$lastRefresh = $this->query("lastrefresh",$lastRefresh);
		//$GLOBALS['firephp']->log($lastRefresh);
		$sql = sprintf ( 'changed > "%s" AND ( user_id = %s OR user_id = -1)', $lastRefresh, $ident->uid() );
		$data = $table->findBySql($sql, array(), Doctrine::HYDRATE_ARRAY);
		$data = array(
			"data" => $data,
			"type" => "list",
			"now" => currentMysqlDatetime()
		);
		$data['type']="list";
		return json_encode($data);
	}
	
	function renderOneToJson($namespace, $name, $lastRefresh="0000-00-00 00:00:00" ) {
		$ident = $this->identity();
		
		$lastRefresh = $this->query("lastrefresh", $lastRefresh);
		$table = $this->getTable();
		$sql = sprintf ( '(user_id = %s OR user_id = -1) AND name="%s" AND namespace="%s" AND changed > "%s"', $ident->uid(), $key, $namespace, $lastRefresh );
		$data = $table->findBySql($sql, array(), Doctrine::HYDRATE_ARRAY);
		if (empty($data)) { 
			return json_encode( array ("type" => "NotFound" ));
		}
		
		$data = array(
			"data" => $data,
			"type" => "record",
			"now" => currentMysqlDatetime()
		);
		
		return json_encode($data);
	}
	
	function renderJsonForward() {
		$key = $this->next();
		$hit = strrpos($key, ".");
		
		
		$namespace = substr($key, 0, $hit);
		$key = substr($key, $hit+1);
		
		return $this->renderOnToJson($namespace, $key);
	}
}
