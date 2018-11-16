<?php
/** 
 * Copyright 2011 by Christian Holzberger <ch@mosaik-software.de> - MOSAIK Software 
 * 
 */

class MSUtil_Mixin extends ArrayObject {
	private $_parts = null;
	private $_write = null;

	public $defaultValue = null;

	public function __construct ( &$writeRef ) {
		$this->_write = &$writeRef;

		$arguments = func_get_args();

		array_shift($arguments);
		if (count ($arguments) > 0 ) {
			$this->__initParts( $arguments );
		}
	}

	public function attach ( &$array ) {
		$this->__initParts();
		$this->_parts->push ( $array );
	}

	public function __get($name) {
		if ( $this->_parts === null || $this->_parts->isEmpty() ) {
			return $this->defaultValue;
		}

		
		if ( is_array($this->_write ) && array_key_exists ($name, $this->_write ) ) {
			return $this->_write[$name];
		} else if (is_object ($this->_write) && @isset ($this->_write->$name)) {
			return $this->_write->$name;
		}
		$this->_parts->rewind();
		
		 while ($this->_parts->valid()) {
			$node = $this->_parts->current();

			if ( is_array ($node)  && @isset ($node[$name])) {

				return $node[$name];
			} else if (is_object ($node) && @ isset ($node->$name)) {
				$var = $node->$name;
				return $var;
			}
			 $this->_parts->next();
		} 
		
		return $this->defaultValue;
	}

	public function offsetGet ($key) {
		return $this->__get($key);
	}

	public function offsetSet ( $key, $value) {
		$this->set($key, $value);
	}

	public function __set($key, $value) {
		$this->set ($key, $value);
	}

	public function set ( $key, &$value) {
		if ( $this->_write === null ) {
			throw new RuntimeException("Object is Unwritable");
		}

		$this->_write [$key] = &$value;
	}

	private function __initParts($arr=null) {
		if ( $this->_parts == null ) {
			$this->_parts = new MSUtil_FifoList();
			
			foreach ($arr as $a) {
				if ( is_array($a) && count ($a) == 0 ) continue; // bug in spl
				$this->_parts->push ( $a );
			}
		}
	}

	
}