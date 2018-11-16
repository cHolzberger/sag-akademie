<?
$inputvalue = "";
$type = "text";

$fName = $attributes['name'];
if (array_key_exists("type", $attributes)) {
	$type = $attributes['type'];
}

if (array_key_exists("value", $attributes)) {
	$inputvalue = $attributes['value'];
}

$inputstyle="";
if (array_key_exists("inputstyle", $attributes)) {
	$inputstyle = $attributes['inputstyle'];
}

$noSpan = false;
if (array_key_exists("nospan", $attributes)) {
	$noSpan = true;
}

$inputClass = "";
if (array_key_exists("validate", $attributes)) {
	$inputClass = 'class="' . $attributes['validate'] .'"';
}

$inputClass = "";
if (array_key_exists("validate", $attributes)) {
	$inputClass = 'class="' . $attributes['validate'] .'"';
}

$checked = getOptional("checked",$attributes);

$key = $attributes['name'];

$id = getOptional("id",$attributes);
$for_id = "";

if ( $id == "") { 
	$id = sprintf('id="input_%s"', $attributes['name']);
	$forId = sprintf ( "input_%s", $attributes['name']);
} else { 
	$forId = sprintf ( "input_%s", $attributes['name']);
}

$isHidden = ($type == "hidden");
$isRadio = ($type == "radio") || ($type=="checkbox");

$postLabel = ""; 
$preLabel = "";
$label="";

if ( array_key_exists("label",$attributes)  ) {
	$lbl = array_key_exists("label",$attributes) ? $attributes['label']: $value; 
	//$lbl = $attributes['label'];
	$label = sprintf('<label class="label" for="%s">%s</label>', $forId, $lbl);
	
	$preLabel = !$isRadio 
				&& !$isHidden; 
				
	$postLabel =!$preLabel  
				&& !$isHidden;
}
?>

<? if ( $isHidden && $noSpan ): ?>
<!--hidden-->
<? else: ?>
<?=$isRadio?"<span":"<div"; ?> class="minput minput<?=ucfirst($type)?> <?=getAttribute("class", $attributes); ?>" style="<?=getAttribute("style", $attributes); ?>">
<? endif; ?>

<?=$preLabel ? $label:""?>
<input type="<?=$type?>" <?=$id?> <?=$checked?> name="<?=$fName?>" value="<?=$inputvalue?>" <?=$inputClass?> style="<?=$inputstyle?>"/>
<?=$postLabel? $label:""?>

<?if ($isHidden && $noSpan): ?><!--/hidden-->
<?else: ?><?=$isRadio?"</span>":"</div>" ?>
<? endif; ?>