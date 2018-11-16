<?

class NotificationQueue {

	var $check = array();
	var $_config = array();

	function setConfig($var, $value) {
		$this->_config[$var] = $value;
	}

	/**
	 * nimmt einen klassennamen als string auf
	 */
	function addCheck($check, $args=array()) {
		print "Filter: " . $check . "<br/>";
		$obj = array();
		$obj['check'] = $check;
		$obj['args'] = $args;
		$this->check[] = $obj;
	}

	/**
	 * durchlaeuft alle filter
	 */
	function run() {
		foreach ($this->check as $checkObj) {
			extract($checkObj);
			echo "Starte Filter: <b>" . $check . "</b><br/>";
			$obj = new $check;
			foreach ($this->_config as $key => $value) {
				$obj->setConfig($key, $value);
			}

			foreach ($args as $key => $value) {
				$obj->setConfig($key, $value);
			}
			$obj->run();
			echo "Erledigt: " . $check . ":<br/>";
			echo "Erfolgreicht ausgeliefert: " . $obj->_ok . "<br/>";
			echo "Fehlerhaft: " . $obj->_errors;
			echo "<br/><br/><hr/>";
		}
	}

	function runTest($className) {
		foreach ($this->check as $checkObj) {
			extract($checkObj);
			if ($check != $className)
				continue;

			echo "Starte Test: <b>" . $check . "</b><br/>";
			$obj = new $check;
			foreach ($this->_config as $key => $value) {
				$obj->setConfig($key, $value);
			}

			foreach ($args as $key => $value) {
				$obj->setConfig($key, $value);
			}
			$obj->runTest();
			echo "Erledigt: " . $check . ":<br/>";
			echo "Erfolgreicht ausgeliefert: " . $obj->_ok . "<br/>";
			echo "Fehlerhaft: " . $obj->_errors;
			echo "<br/><br/><hr/>";
		}
	}

}

?>