<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$id = $dsl->get("dbtable", 'id');
$kurzbeschreibung = $dsl->get("dbtable", 'kurzbeschreibung');
$infos = array (
    array( "link"=>$dsl->get("dbtable", 'info_link'), "title" => $dsl->get("dbtable", 'info_title')),
    array( "link"=>$dsl->get("dbtable", 'info_link2'), "title" => $dsl->get("dbtable", 'info_title2')),
    array( "link"=>$dsl->get("dbtable", 'info_link3'), "title" => $dsl->get("dbtable", 'info_title3'))

);

foreach  ($infos as $info) 
    if ( !empty ( $info['link'] )) {
    ?>
<div style="font-size: 11px; width: 290px; min-height: 70px; float: left; padding-left: 60px; position: relative; padding-bottom: 12px;">
    <a href="<?=$info['link']?>" target="_blank">
	<img src="/img/pdf.png" alt="" border="0" width="53" min-height="53" style="position: absolute; left: 2px; top: 2px;"/>
	<? if (empty ($info['title'])) { ?>
	    Kursinformationen: <?=$id?>
	<?} else { ?>
	    <?=$info['title']?>
	<? } ?>
	<br/></a>
	<?=$kurzbeschreibung?>
</div>
<? } ?>