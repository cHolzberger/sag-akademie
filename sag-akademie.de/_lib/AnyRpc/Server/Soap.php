<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of ServerFactoryJsonRPC
 *
 * @author cholzberger
 */
class AnyRpc_Server_Soap extends AnyRpc_Server_Generic {
	protected $rpcServerClassName = "Zend_Soap_Server";

	function &init( $mode ) {
		$this->_mode = $mode;
		if ( $mode == "smd") {
			 $this->createServiceDescriptor();
		} else {
			$this->createServer();
		}

		return $this;
	}

	function setClass($name, $ns=null, $arguments=null) {
		// to specify the right url we have to split the name again
		$dashPos = strpos ($name, "_");
		$targetNotation = substr ( $name, 0, $dashPos) . "." . substr ( $name, $dashPos+1);

		if ( $this->_mode == "smd"  ) {
			$this->_smdGenerator->setUri(htmlentities('https://' .$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?s=Soap__m=smd__t=$targetNotation"));

			$this->_smdGenerator->setClass ($name);
		} else if ( $this->_rpcServer ) {
		

			$this->_rpcServer->setUri( htmlentities ( 'https://' .$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?s=Soap&m=smd&t=$targetNotation"));

			$this->_rpcServer->setClass($name, $ns, $arguments);
			$this->_rpcServer->setEncoding('UTF-8');
		}
	}

	/** initializes the objects to use when introspection is running, 
	 * special case for Soap, because of Zend_Soap_AutoDiscover
	 * @return mixed
	 */
	function createServiceDescriptor() {
		$this->_smdGenerator = new  Zend_Soap_AutoDiscover();
	}

	function generateServiceDescription() {
		// Grab the SMD
		ob_start();
		echo $this->_smdGenerator->handle();
		$this->_smdString = ob_get_contents();
		ob_end_clean();

		return $this->_smdString;
	}
}

