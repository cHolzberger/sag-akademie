<?
class Mosaik_EventQueue {

    var $check = array();
    var $_config = array();
    var $_handlerCache = array();

    function setConfig($var, $value) {
	$this->_config[$var] = $value;
    }

    /**
     * nimmt einen klassennamen als string auf
     */
    function addHandlerForEvent($eventHandler, $forEvent,$args=array()) {
	//print "Filter: " . $check . "<br/>";
	if ( !is_array($forEvent)) $forEvent=array($forEvent);

	$obj=array();
         $obj['eventHandler'] = $eventHandler;
	 $obj['forEvent'] = $forEvent;
	 $obj['args'] = $args;
	 $this->check[] = $obj;
    }
    
    function &_getHandler($eventHandler) {
	    if ( ! array_key_exists ( $eventHandler, $this->_handlerCache)) {
		if ( ! class_exists( $eventHandler )) {
		    include ("EventHandler/$eventHandler.php");
		}
		$this->_handlerCache[$eventHandler] = new $eventHandler;
	    }
	    
	    return $this->_handlerCache[$eventHandler];
	}
    /**
     * durchlaeuft alle event maps und ruft den entsprechenden handler zum event auf
     *
     * achtung! event handler werden gecached, sie werden also maximal 1x pro aufruf instanzieert
     */
    function dispatch( &$event ) {
        foreach ($this->check as $checkObj) {
	    if ( ! in_array($event->name , $checkObj['forEvent'])) continue;

	    extract($checkObj );
	    //echo "Starte Filter: <b>" . $check ."</b><br/>";
	    

	    $obj = $this->_getHandler($eventHandler);
	   
	    foreach ( $this->_config as $key=>$value ) {
		$obj->setConfig($key, $value);
	    }

	    foreach ( $args as $key => $value) {
		$obj->setConfig($key, $value);
	    }

            $obj->handle($event);
	    //echo "Erledigt: " .$check . ":<br/>";
	    //echo "Erfolgreicht ausgeliefert: ". $obj->_ok ."<br/>";
	    //echo "Fehlerhaft: ". $obj->_errors;
	    //echo "<br/><br/><hr/>";
        }

	
    }

}
