<?
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$k = Doctrine::getTable("KontaktKategorie");
$ka = $k->findAll();
foreach ($ka as $kategorie ) {
?>
<input type="checkbox" value="<?=$kategorie->id?>" name="kategorie[]" /> <?=$kategorie->name?><br/>

<? } ?>