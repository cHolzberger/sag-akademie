<?php
include("adm/dbcontent.php");

class ADM_User extends ADM_DBContent {
	function map($name) {
		return "ADM_User";
	}
}
