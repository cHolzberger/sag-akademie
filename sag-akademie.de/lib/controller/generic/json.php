<?php
/* 
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class GENERIC_Json extends Generic_Admin_Component {
	function init() {
		
	}
	
	function renderJson() {
		return json_encode($this->data);
	}
	
	function renderJsonForward($class_name, $namespace) {
		return $this->renderJson();
	}
}
?>