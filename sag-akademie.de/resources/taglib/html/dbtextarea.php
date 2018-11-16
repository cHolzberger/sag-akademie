<?
$nspace = $dsl->get("dbtable","dbclass");
$content = $dsl->get("dbtable", $nspace .":".$attributes['name']);
$inputId = sprintf('id="input_%s"', $attributes['name']);
$forId = sprintf ( "input_%s", $attributes['name']);

$rte = getAttribute("rte", $attributes, false);
if ( $rte ) $rte = 'class="wymeditor"';
$rdonly = "";
if ( array_key_exists("readonly",$attributes) ) {
    $rdonly='readonly="readonly" disabled="disabled"';
}
?>

<div class="dbtextarea">
<? if (array_key_exists("label",$attributes)) { ?>
	<label class="label" for="<?=$forId?>"><?=$attributes['label']?></label><? if($rte) echo "<br/>"; ?>
<? } else { ?>
	<label class="label" for="<?=$forId?>" style="<?=getAttribute ("labelStyle", $attributes); ?>">&nbsp;</label><? if($rte) echo "<br/>"; ?>
<? } ?>
<textarea <?=$rte?> <?=$inputId?> name="<?=$dsl->get("dbtable","dbtable")?>[<?=$attributes['name']?>]" style="<?=getAttrStyle($attributes); ?>" <?=$rdonly?> ><?=$content?> </textarea>
</div>
