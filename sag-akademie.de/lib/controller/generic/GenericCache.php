<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
class MosaikCacheController {
    public $storeForSession = false;


    function saveObj($namespace, $id, $data) {
	$co = Doctrine::getTable('GenericCache')->findBySql("id=? and namespace=?", array($id,$namespace))->getFirst();
	if ( empty ( $co )) $co = new GenericCache();

	$co->id = $id;
	$co->namespace = $namespace;
	$co->data = serialize($data);
	$co->save();
    }

    function restoreObj($namespace, $id, $data) {
	$co = Doctrine::getTable('GenericCache')->findBySql("id=? and namespace=?", array($id,$namespace))->getFirst();
	return unserialize($co->data);
    }
}
?>
