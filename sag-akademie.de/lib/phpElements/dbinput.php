<?
$inputvalue = $dsl->get("dbtable", $attributes['name']);
if (array_key_exists("converter", $attributes)) {
	$converter = $attributes['converter'];
	$inputvalue = $converter( $inputvalue );
}
$type = "text";
if (array_key_exists("type", $attributes)) {
	$type =$attributes['type'];
}

$table= $dsl->get("dbtable","dbtable");
$key = $attributes['name'];
if (isset ($table) && !empty ($table)) {
		$fName = $table."[".$key."]";
} else {
	$fName = $key;
}
?>
<div class="dbinput">
<? if (array_key_exists("label",$attributes)) { ?>
	<span class="label"><?=$attributes['label']?></span>
<? } else { ?>
	<span class="label">&nbsp;</span>
<? } ?>
<input type="<?=$type?>" name="<?=$fName?>" value="<?=$inputvalue?>" class="dbinput <?=getAttrClass( $attributes); ?>" style="<?=getAttrStyle( $attributes); ?>"/>
</div>
