<?
// DEPRECATED: see mdb:input

$inputId="";
$inputvalue = "";
$tooltip = getOptional("tooltip", $attributes,"title");
$forId = sprintf ( "upload_%s", $attributes['name']);
$table= $dsl->get("dbtable","dbtable");
$key = $attributes['name'];
$inputId = sprintf('id="%s"', $forId);
$inputClass = "";
if (array_key_exists("validate", $attributes)) {
	$inputClass = 'class="' . $attributes['validate'] .'"';
}
?>

<div class="dbinput <?=getAttribute( "class", $attributes); ?>" style="<?=getAttribute("style", $attributes); ?>">
	<? if (array_key_exists("label",$attributes)) { ?>
		<label class="label" for="<?=$forId?>"><?=$attributes['label']?></label>
	<? } else { ?>
		<label class="label" for="<?=$forId?>">&nbsp;</label>
	<? } ?>
<input <?=$inputId?> type="file" name="<?=$forId?>" <?=$inputClass?> <?=$tooltip?> />
</div>

