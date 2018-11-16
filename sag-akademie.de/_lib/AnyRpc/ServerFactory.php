<?php
/**
 * This Class handles all the creation and setup of the rpc response and the descriptor mapping (if any)
 */
class AnyRpc_ServerFactory {
	private $_prefix = "AnyRpc_Server";
	private $_instance = null;

	function &createServer( $name, $mode ) {
		$_cn =$this->_prefix ."_" .$name;

		$this->_instance = new $_cn;
		$this->_instance->init($mode);
		return $this->_instance;
	}
}