<?php

/*
 * 07.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 *
 * liefert alle infos die fuer die neue startseite gebraucht werden zurueck
 */
include_once ("helpers.php");

/**
 * Stores user settings in the database
 */
class User_Settings {
	
	/**
	 * fetches all objects at once
	 * 
	 * @param string $name 
	 * 
	 * @return object
	 */
	function fetchNs ( $ns ) {
		qlog(__CLASS__ . "::" . __FUNCTION__  .": namespace => $ns");
        $ret = array();

        try {
			$u = Identity::get();

			$s = Doctrine::getTable("XSettings")->findByDql ("(user_id = ? OR user_id = -1 ) AND namespace = ?", array($u->getId(), $ns))->toArray();

			$c = count($s) ;
			qlog("Found $c settings to transfer");

			for ( $i =0; $i<$c; $i++) {
				$name = $s[$i]['name'];

				if ( ! array_key_exists ( $name, $ret )) {
					$ret[ $name ] = array();
				}

				$ret[$name] = $s[$i]['value'];
			}

		} catch ( Exception $e) {
			qdir("Exception:");
			qdir($e->getMessage());
		}
		
		
		return $ret;
	}

    /**
     * fetches one setting
     *
     * @param $table
     * @internal param string $name
     *
     * @return array
     */
	function getUserSettings($table) {
		$settings = $this->fetchNs("UserSettings");
        $data = array();
        try {
		    foreach ( $settings as $key=>$val ) {
    			$data[$key] = json_decode($val);
	    	}
        } catch ( Exception $e) {
            qdir("Exception:");
            qdir($e->getMessage());
        }


		return array( "table_name"=>$table, "data" => (object)$data);
	}

	function setUserSettings($data) {
		qlog(__CLASS__ . "::" . __FUNCTION__  . ":"  );
        //qdir($data);
		foreach ( $data as $set) {

			qlog("NS: UserSettings Key: " . $set['key']);
				//qlog($data);
				if ( is_object ($set['data'])) {
					$set['data'] = json_encode($set['data']);
				} else if (is_array($set['data'])) {
					$set['data'] = json_encode($set['data']);
				}

				$this->update("UserSettings", $set['key'], $set['data']);
			
		}

		
		return array("done");
	}

	/**
	 * updates a single setting
	 */
	function update($ns, $name, $value) {
		qlog(__CLASS__ . "::" . __FUNCTION__  . ":  Name: $ns/$name Value: $value"  );
		
		$u = Identity::get();
		
		$obj = Doctrine::getTable("XSettings")->findByDql("user_id = ? AND namespace = ? AND name = ?", array($u->getId(), $ns, $name))->getFirst();
		
		// create new setting if it doesnt exist
		if (! is_object ( $obj )) {
			qlog("creating new setting object");
			$obj = new XSettings();
			$obj->id =null;
		}
		
		$obj->name = $name; 
		$obj->namespace=$ns;
		$obj->value = $value;
		$obj->changed = currentMysqlDatetime();
		$obj->user_id = $u->getId();
		
		$obj->save();
	} 

}