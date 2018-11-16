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
class AnyRpc_Server_JsonRpc extends AnyRpc_Server_Generic {

	protected $rpcServerClassName = "Zend_Json_Server";

	/**
	 * sets rpc options for the jsonrpc module
	 */
	function setRpcServerOptions() {
		$this->_rpcServer->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
	}

	/**
	 * generate smd from server objec
	 * @return string
	 */
		function generateServiceDescription() {
		$env = MSUtil::getEnv();
		$mode = AnyRpc::get("mode");
		$targetService = AnyRpc::get("targetService");
		
		$cacheString = "jsonrpc_".$targetService.".smd";

		if ($env->hasCacheKey($cacheString)) {
			$this->_smdString = $env->getCacheKey($cacheString);
		} else {
			$serverName = AnyRpc::get("serverName");
			$myUrl = "";
			if ($targetService !== null) {
				$myUrl = RPC_PROTO . "://". $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?s=$serverName&t=$targetService";
			} else {
				$myUrl = RPC_PROTO . "://". $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?s=$serverName";
			}

			$this->_rpcServer->setTarget($myUrl)
			 ->setId($myUrl);

			// Set Dojo compatibility:
			$map = $this->_rpcServer->getServiceMap();
			//$map->setDojoCompatible(true);
			ob_start();
			echo $map->toJson();
			$this->_smdString = ob_get_contents();
			ob_end_clean();
			
			$env->setCacheKey($cacheString, $this->_smdString);
		}
		return $this->_smdString;
	}

}

?>
