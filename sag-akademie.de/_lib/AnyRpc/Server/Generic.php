<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of GenericServer
 *
 * @author cholzberger
 */
class AnyRpc_Server_Generic {
	protected $_mode = "";
	/**
	 * classes extending this class have to define the className of their Zend_SERVER_Server
	 * in this property
	 * @var String Classname
	 */
	protected $rpcServerClassName = "Zend_UNKNOWN_Server";
	/**
	 * this saves the service descriptor used in JsonRPC and SOAP as a string to make it cacheable
	 *
	 * @var String the Service descriptor
	 */
	protected $_smdString = "";
	/**
	 * the specific smd generator / introspector
	 * @var mixed
	 */
	protected $_smdGenerator = null;
	/**
	 * the Zend_*_Server instance
	 * @var mixed
	 */
	protected $_rpcServer = null;

	/**
	 * handles the incoming request, generates (or reads the cache of) the smd if $mode == "smd"
	 *
	 * @param string $mode
	 * @return mixed 
	 */
	function &handle( ) {
		if ($this->_mode == "smd" ) {
			echo $this->generateServiceDescription();
		} else {
			echo $this->_rpcServer->handle();
		}
		return $this;
	}

	function getResponse() {
		return $this->_rpcServer->getResponse();
	}

	/**
	 * proxy for Zend_SERVER_Server->setClass with the difference that $arguments is an array to
	 * pass data to your classes (eg. array("myvar1"=>"value", "myvar2"=>"value2" .. )
	 *
	 * it either calls setClass on _smdGenerator (when $mode="smd" and _smdGenerator is not null) or register
	 * the classes through _rpcServer
	 *
	 * @param <type> $name
	 * @param <type> $ns
	 * @param
	 */
	function setClass($name, $ns=null, $arguments=null) {
		if ( AnyRpc::get("targetService") === null) {
			$dottedName = str_replace ("_",".", $name);
			$this->_rpcServer->setClass($name, $dottedName, $arguments);
		} else {
			$this->_rpcServer->setClass($name, null, $arguments);
		}

		
	}

	/**
	 * called after the server object has been constructed to set default parameter
	 */
	function setRpcServerOptions() {
		
	}

	/**
	 * find out which mode we are running in, called right after object creation
	 * @param <type> $mode
	 * @return <type>
	 */
	function &init( $mode ) {
		$this->_mode = $mode;
		$this->createServer();

		return $this;
	}

	/** creates the Server class from $this->className
	 * @return mixed
	 */
	function createServer() {
		if ( $this->_rpcServer !== null ) return;
		
		$class = $this->rpcServerClassName;

		$this->_rpcServer = new $class();
		$this->setRpcServerOptions();
	}


	function createServiceDescriptor() {
		return null;
	}

	/** generates the service description after all calls to ->setClass have been succeded
	 * may return null
	 * @return mixed
	 */
	function generateServiceDescription() {
		return null;
	}

	function fault($error, $code=404) {
		qlog("RPC Error {$code}: ");
		qdir($code);
		$this->_rpcServer->fault($error, $code);
	}
}
