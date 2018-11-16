<?php

/*
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

class NotificationCheck {

	var $view = "none";
	var $_errors = 0;
	var $_ok = 0;
	var $_current = 0;
	var $_max = 0;
	var $_config = array();

	function run() {
		if (!$this->_config['sendMail']) {
			print "Mailversand: <span style='color: red;'>Testlauf</span><br/>";
		} else {
			print "Mailverand: via SMTP<br/>";
		}
	}

	function setConfig($var, $value) {
		MosaikDebug::msg($value, "Setting $var:");
		$this->_config[$var] = $value;
	}

	function setMax($i) {
		$this->_max = $i;
	}

	function setCurrent($i) {
		$this->_current = $i;
	}

	function addError($msg) {
		$this->_errors++;
		print "[" . $this->_current . "/" . $this->_max . "] - <span style='color: red'>" . $msg . " Fehlgeschlagen</span><br/>";
	}

	function addOk($msg) {
		$this->_ok++;
		print ("[" . $this->_current . "/" . $this->_max . "] - " . $msg . "<br/>");
	}

	function runTest() {
		print "Mailversand: <span style='color: red;'>Testlauf</span><br/>";
	}

}

?>
