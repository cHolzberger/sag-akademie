<?
$dsl->log();
$optionValue = $dsl->get("dbtable","id"); //fixme: use dynamic key !!!
$selected = getOptional("selected",$attributes);
$id = getOptional("id",$attributes);
?>
<option value="<?=$optionValue?>" <?=$id?> <?=$selected?>><?=$value?></option>