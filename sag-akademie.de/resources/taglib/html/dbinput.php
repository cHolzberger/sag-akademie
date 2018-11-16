<?
// DEPRECATED: see mdb:input

$inputId="";
$inputvalue = "";
$tooltip = getOptional("tooltip", $attributes,"title");

$nspace = $dsl->get("dbtable","dbclass");
if ( empty ($nspace) ) {
	$inputvalue = $dsl->get("dbtable", $attributes['name']);
	
} else {
	$inputvalue = $dsl->get("dbtable", $nspace .":". $attributes['name']);
	
}

$forId = sprintf ( "input_%s", $attributes['name']);

if ( empty ($inputvalue) && array_key_exists("default", $attributes) ) {
	if ( empty ($nspace) ) {
		$inputvalue = $dsl->get("dbtable", $attributes['default']);
	} else {
		$inputvalue = $dsl->get("dbtable", $nspace .":". $attributes['default']);
	}
}

if (array_key_exists("converter", $attributes)) {
	$converter = $attributes['converter'];
	$inputvalue = $converter( $inputvalue );
}

// convert the input value
$inputvalue = convertString($inputvalue, $attributes);

$type = "text";
$checked = "";
if (array_key_exists("type", $attributes)) {
	$type =$attributes['type'];
	if (($type == "radio" || $type=="checkbox") && array_key_exists("value", $attributes)) {
		if ( $inputvalue == $attributes['value'])  $checked='checked="checked"';
		$inputvalue = $attributes['value'];
		$forId = $forId . "_" . $inputvalue;
	}
}

$table= $dsl->get("dbtable","dbtable");
$key = $attributes['name'];
if (isset ($table) && !empty ($table)) {
	$fName = $table."[".$key."]";
} else {
	$fName = $key;
}

$inputId = sprintf('id="%s"', $forId);

$inputClass = "";
if (array_key_exists("validate", $attributes)) {
	$inputClass = 'class="' . $attributes['validate'] .'"';
}

$rdonly = "";
if ( array_key_exists("readonly",$attributes) ) {
    $rdonly='readonly="readonly" disabled="disabled"';
}
?>
<? if ($type != "hidden") { ?>

<div class="dbinput <?=getAttribute( "class", $attributes); ?>" style="<?=getAttribute ("style", $attributes); ?>">
	<? if (array_key_exists("label",$attributes)) { ?>
		<label class="label" for="<?=$forId?>"><?=$attributes['label']?></label>
	<? } else { ?>
		<label class="label" for="<?=$forId?>" style="<?=getAttribute ("labelStyle", $attributes); ?>">&nbsp;</label>
	<? } ?>
<? } ?>
<input <?=$inputId?> type="<?=$type?>" name="<?=$fName?>" style="<?=getAttribute ("inputStyle", $attributes); ?>" value="<?=$inputvalue?>" <?=$checked?> <?=$inputClass?> <?=$tooltip?> <?=$rdonly?>/>

<? if ($type != "hidden") { ?>
</div>
<? } ?>
