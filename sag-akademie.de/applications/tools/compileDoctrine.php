<?php
include_once('../../lib/Doctrine/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));

Doctrine::compile('../../resources/cache/php/Doctrine.compiled.php', array("mysql"));
?>
