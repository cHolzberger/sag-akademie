<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo "<pre>";
require("Doctrine.php");

spl_autoload_register(array('Doctrine', 'autoload'));
Doctrine::loadModels(dirname (__FILE__) . '/models/generated');
Doctrine::loadModels(dirname (__FILE__) . '/models');

$connection = Doctrine_Manager::connection( new PDO('sqlite::memory:') );
echo Doctrine::generateSqlFromModels();


echo "</pre>";
?>
