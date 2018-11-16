<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class DBPool_ModelsToSql {
	function generateSql () {
		$manager = Doctrine_Manager::getInstance();

		$manager->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL);
		return Doctrine::generateSqlFromModels();
	}
}
