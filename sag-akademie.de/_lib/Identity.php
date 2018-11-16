<?php

include_once("Identity/DoctrineUserTable.php");

class Identity {
	static $identity = null;

	static function &create () {
		if ( self::$identity == null ) {
			self::$identity = new Identity_DoctrineUserTable();
		}

		return self::$identity;
	}

	static function &getIdentity() {
		return self::$identity;
	}

	static function &get() {
		return self::$identity;
	}
}