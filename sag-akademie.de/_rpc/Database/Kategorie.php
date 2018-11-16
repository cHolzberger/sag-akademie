<?php

/*
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

include_once("services/TemplateService.php");

class KategorieInfo {
	static $sections = array();
	
	var $table = "";
	var $section = "";
	var $title = "";
	
	static function create ( $table, $info, $title) {
		$k = new KategorieInfo();
		$k->table = $table;
		$k->section = $info;
		$k->title = $title;
		self::$sections[] = $info;
		self::$sections = array_values(array_unique( self::$sections ));
		return $k;
	}
}

/***
 * email templates
 */
class Database_Kategorie {
	var $_tables = array();
	
	function __construct() {
		$this->_tables = array ( 
		 //  ** Downloads 
			KategorieInfo::create( "DownloadKategorie", "Downloads", "Download-Kategorie"),
		 
		 // ** Kunden 
			KategorieInfo::create( "KontaktKategorie", "Kunden", "Kunden-Kategorie"),
			KategorieInfo::create( "KontaktQuelle", "Kunden", "Kunden-Quelle"),
			KategorieInfo::create( "KontaktStatus", "Kunden", "Kunden-Status"),	
 		 	KategorieInfo::create( "XBranche", "Kunden", "Kunden-Branchen"),
		 	KategorieInfo::create( "XTaetigkeitsbereich", "Kunden", "Kunden-Tätigkeitsbereiche"),
			
		 // ** Kooperationspartner 
 			KategorieInfo::create( "KooperationspartnerKategorie", "Kooperationspartner", "Kooperationspartner-Kategorie"),	
		 
		 // ** Seminare
		 	KategorieInfo::create( "SeminarArtRubrik", "Seminare", "Seminar-Rubrik"),	
			KategorieInfo::create( "SeminarArtStatus", "Seminare", "Seminar-Status"),
		 
		 // ** Stellenangebote
		 	KategorieInfo::create( "StellenangebotKategorie", "Stellenangebote", "Stellanangebot-Kategorie"),
		 
		 // ** Todo
		 	KategorieInfo::create( "XTodoKategorie", "Todo", "Todo-Kategorie"),
			KategorieInfo::create( "XTodoRubrik", "Todo", "Todo-Rubrik"),
			KategorieInfo::create( "XTodoStatus", "Todo", "Todo-Status"),
		 
		 // ** Allgemein
			KategorieInfo::create( "XBundesland", "Allgemein", "Bundesländer"),
 			KategorieInfo::create( "XGrad", "Allgemein", "Grad"),
 			KategorieInfo::create( "XLand", "Allgemein", "Länder"),
  			KategorieInfo::create( "XStatus", "Allgemein", "Status")
		);
	}
	
	function getSections() {
		return KategorieInfo::$sections ;
	}
	
	/**
	 * gets all KategorieInfo objects for a section
	 * 
	 * @param type $section
	 * @return type array
	 */
	function getTables( $section ) {
		$ret = array ();
		
		for ( $i=0; $i < count ( $this->_tables); $i++ ) {
			if ( $this->_tables[$i]->section == $section) {
				 $ret[] =  $this->_tables[$i];
			}
		}
		
		return array( "tables"=> $ret, "section" => $section);
	}
	
	function getValues ( $table ) {
		$values = Doctrine::getTable($table)->findAll();
		
		return $values->toArray();
	}
	
	/**
	 *
	 * @param string $table
	 * @param string $id
	 * @param string $text
	 * @return stdClass
	 */
	function saveKategorie($table, $id, $text) {
		qlog(__CLASS__. "::" . __FUNCTION__ . ": table => {$table} id => {$id} text => {$text}");
		
		$item = Doctrine::getTable($table)->find($id);
		$item->name = $text;
		$item->save();
		Doctrine::getTable('XTableVersion')->increment($table);

		return $item->toArray();
	}
	
	function deleteKategorie($table, $id) {
		qlog(__CLASS__. "::" . __FUNCTION__ . ": table => {$table} id => {$id}");
		
		$item = Doctrine::getTable($table)->find($id);
		$item->delete();
		
		return array();
	}
	
	/**
	 *
	 * @param string $table
	 * @param string $text
	 * @return stdClass
	 */
	function createKategorie( $table, $text) {
		$item = new $table ();
		$item->name = $text;
		$item->save();
		Doctrine::getTable('XTableVersion')->increment($table);

		return $item->toArray();
	}
}