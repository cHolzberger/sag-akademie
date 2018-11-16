<?php
include_once("config.php");

class ADM_DB_Migrations extends SAG_Admin_Component {
	function GET() {
		$GLOBALS['path'][] = array('name'=> $this->name(), 'url' => $this->url());
			
		$text .= "<span><h1>Generating Migrations</h1>";
		$oldVersion = intval( MosaikConfig::getVar("dbversion"));
		$newVersion = $oldVersion + 1;
		
		//$migration->setCurrentVersion($newVersion);
		
		$text .= "From: $oldVersion To: $newVersion<br/>";
		
		$schemaOld = WEBROOT . "/lib/Doctrine/yaml/schema_v_".$oldVersion.".yml";
		$schemaNew = WEBROOT . "/lib/Doctrine/yaml/schema_v_".$newVersion.".yml";
			
		$text .= "<div>Migrations from Models...";
		
		$text .= "<br/>Old Schema: ".$schemaOld;
		$text .= "<br/>New Schema: ".$schemaNew."<br/>";
		Doctrine::generateMigrationsFromDiff ( WEBROOT . "/lib/Doctrine/migrations/", $schemaOld, $schemaNew);
		$text .= " OK</div>";
		
		// we are the root of the migration, so we allready contain this migration: 
		$ver = Doctrine::getTable("MigrationVersion")->findAll()->getFirst();
		$ver->version = $newVersion;
		$ver->save();
		
		return $text . "<div>Done</div></span>";
	}

	function HEAD() {
		throw new k_http_Response(200);
	}
}
?>