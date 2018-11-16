<?php
/**
 * MosaikObjectQueue
 *
 * a queue of objects, contruct the object using the method name you want to call later
 * then add objects via add method
 * and then  run them with the run method
 * 
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 */
class MosaikObjectQueue {
	private $queue =array ();
	private $method;

	function __construct ( $method ) {
		$this->method = $method;
	}

	function add($item) {
		$this->queue[] = $item;
	}

	function run() {
		foreach ( $this->queue as $item) {
			$mname = $this->method;
			$item->$mname();
		}
	}
}

?>
