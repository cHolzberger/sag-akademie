<?php

/* * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
include_once("lib/debug.php");
include_once('Mosaik/EventQueue.php');
define ( "CONFIGDIR", dirname(dirname(__FILE__)));

class GenericService {
    /**
     * der Name dieses Services wird in den Mappings und zum cachen der Singletons verwendet
     * @var String
     */
    var $serviceName = "GenericService";

    /**
     *	Die event Queue speichert die Event => event handler mappings 
     * @var array of array of String
     */
    static $eventQueue = array();

    /**
     * die instance variable speichert die instanzen der singletons
     * @var array of objects
     */
    static $instance = array();

    function __construct( $serviceName ) {
	$this->serviceName = $serviceName;
	GenericService::$instance[ $serviceName ] = $this;
    }
    /**
     * liest Service => EventHandler
     * und EventHandler => Event
     * mappings um EventHandler mit bestimmten Events zu verbinden
     * @param String $serviceName
     */
    static function _initEventMap ($serviceName) {
	
	$mapString = file_get_contents( CONFIGDIR . "/eventMap.json");
	// event map wird komplett als asoc array geladen
	$serviceMap = json_decode($mapString, true);
	MosaikDebug::msg($serviceMap, "Service map:");

	$eventMap = $serviceMap[$serviceName];

	//MosaikDebug::msg($eventMap, "Event map for " . $serviceName);
	if ( !array_key_exists ( $serviceName,  GenericService::$eventQueue) ) {
	    GenericService::$eventQueue[$serviceName] = new Mosaik_EventQueue();
	}

	if ( is_array ( $eventMap )) foreach ( $eventMap as $handler=>$eventArray ) {
	   GenericService::$eventQueue[$serviceName]->addHandlerForEvent( $handler, $eventArray );
	}
    }

    static function &_getInstance( $serviceName ) {
	if ( !array_key_exists($serviceName, GenericService::$instance ) ) {
	    GenericService::$instance[ $serviceName ] = new $serviceName( $serviceName );
	}

	

	return GenericService::$instance[ $serviceName ];
    }

    static function &_getEventQueue($serviceName) {
	if ( !array_key_exists($serviceName, GenericService::$eventQueue ) ) {
	    GenericService::_initEventMap($serviceName);
	}

	return  GenericService::$eventQueue[$serviceName];
    }
    /**
     * loest das Event $eventInfo aus
     *
     * @param object $eventInfo
     */
     static function _dispatchEvent ($serviceName, &$eventInfo)  {
		
		 qlog("New Event Topic: ($serviceName) -- Event: " . $eventInfo->name . " {" . $eventInfo->sourceClass . "::" . $eventInfo->sourceFunction . "} [" .$eventInfo->sourceFile .  ":". $eventInfo->sourceLine."]");
		 try {
			GenericService::_getEventQueue($serviceName)->dispatch($eventInfo);
		 } catch ( Exception $e) {
		 	qlog("Error while dispatching");
			qlog($e->getMessage());
		 }
    }


   
}