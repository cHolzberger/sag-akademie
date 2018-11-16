<? 
require_once("Doctrine.php");

spl_autoload_register(array("Doctrine", "autoload"));

Doctrine_Core::compile("Doctrine.compiled.php", array("mysql"));
