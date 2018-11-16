<?php

class Identity_DoctrineUserTable {
	var $tableName = "XUser";
	var $data = null;
	var $isValid = false;

	function authenticate($authData) {
		$table = Doctrine::getTable($this->tableName);
		@$arguments = array($authData['username'], $authData['password'], $authData['token'],1);
		
		try {
			$data = $table->findByDql(" ((name=? AND password = ?) OR auth_token = ?) AND aktiv=? ", $arguments)->getFirst();
			
			if (!is_object($data)) {
				throw new Exception("Invalid Credentials");
			}
			$_SESSION['token'] = $data['auth_token'];
			$this->data = $data;
			$this->isValid = true;
			return true;
		} catch (Exception $e) {
			qerror("Exception:");
			qerror( $e->getMessage() );
			qerror("Invalid Authentication from: " . $_SERVER['REMOTE_ADDR']);
			qerror_dir($arguments);
			$this->isValid = false;
		}
		
		
		return false;
	}

	function getUserdata() {
		if ( !$this->data) {
			qlog("User not authenticated");
			qdir($_SESSION);
			$this->authenticate($_SESSION);
		}
		return $this->data->toArray();
	}

	function getToken() {
		if ( !$this->data) {
			qlog("User not authenticated");
			qdir($_SESSION);
			$this->authenticate($_SESSION);
		}
		return $this->data->auth_token;
	}

	function getDn() {
		if ( !$this->data) {
			qlog("User not authenticated");
			qdir($_SESSION);
			$this->authenticate($_SESSION);
		}
		return $this->data->name;
	}

	function getId() {
		if ( !$this->data) {
			qlog("User not authenticated");
			qdir($_SESSION);
			$this->authenticate($_SESSION);
		}
		return $this->data->id;
	}

	function uid() {
		if ( !$this->data) {
			qlog("User not authenticated");
			qdir($_SESSION);
			$this->authenticate($_SESSION);
		}
		
		if ( !$this->data) {
			return -1;
		}
		return $this->data->id;
	}
	
	function isValid() {
		if (is_object($this->data)) {
			return true;
		}
		return false;
	}
}