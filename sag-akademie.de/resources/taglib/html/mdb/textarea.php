<?
$inputId = sprintf('id="input_%s"', $attributes['mpath']);
$labelId = sprintf('id="label_%s"', $attributes['mpath']);
$forId = sprintf ( "input_%s", $attributes['mpath']);
$rte = getAttribute("rte", $attributes, false);
if ( $rte ) $rte = 'class="wymeditor"';
$fName = "";
$key = $attributes['mpath'];
$fArray = explode ( ":", $key);

if ( count($fArray) > 1 ) {
	$fName = array_shift($fArray);
	
	foreach ( $fArray as $fsub) {
		$fName .= "[$fsub]";	
	}
} else {
	$fName = $key;
}
$content = $dsl->get("dbtable", $key);

?>
<div class="dbtextarea" <?=$labelId?>>
<? if (array_key_exists("label",$attributes)) { ?>
	<label class="label" for="<?=$forId?>"><?=$attributes['label']?></label>
<? } else { ?>
	<!--<label class="label" for="<?=$forId?>">&nbsp;</label>-->
<? } ?>
<textarea <?=$rte?> <?=$inputId?> name="<?=$fName?>" style="<?=getAttrStyle($attributes); ?>" ><?=$content?></textarea>
</div>
