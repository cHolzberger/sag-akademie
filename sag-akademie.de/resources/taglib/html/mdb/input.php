<?
$inputId="";
$inputvalue = "";
$tooltip = getOptional("tooltip", $attributes,"title");

$inputvalue = $dsl->get("dbtable", $attributes['mpath']);
	
$forId = sprintf ( "input_%s", str_replace(":","_",$attributes['mpath']));

// FIXME: default werte anders festlegen
if ( ($inputvalue === null || $inputvalue === -1 || $inputvalue === "-1" || empty ($inputvalue) || $inputvalue == 0 || $inputvalue=="0,00") && array_key_exists("default", $attributes) ) {
		$inputvalue = $dsl->get("dbtable", $attributes['default']);
}

if (array_key_exists("converter", $attributes)) {
	$converter = $attributes['converter'];
	$inputvalue = $converter( $inputvalue );
}

// convert the input value
$inputvalue = convertString($inputvalue, $attributes);

$type = "text";

$checked = getOptional("checked", $attributes, "");
if (array_key_exists("type", $attributes)) {
	$type =$attributes['type'];

	if ($type == "file") {
	    $type = "text";
	    $inputvalue = "type=file unsupoorted";
	}

	if (($type == "radio" || $type=="checkbox") && array_key_exists("value", $attributes)) {
		if ( $inputvalue == $attributes['value'])  $checked='checked="checked"';
		$inputvalue = $attributes['value'];
		$forId = $forId . "_" . $inputvalue;
	}
}

$key = $attributes['mpath'];
$fName = "";
if ( array_key_exists("iName", $attributes) ) {
	$fName = $attributes['iName'];
} else {
    
    $fArray = explode ( ":", $key);

    if ( count($fArray) > 1 ) {
	$fName = array_shift($fArray);
	
	foreach ( $fArray as $fsub) {
		$fName .= "[$fsub]";	
	}
    } else {
	$fName = $key;
    }
}

$myId = getAttribute("id", $attributes, $forId);
$inputId = sprintf('id="%s"', $myId);

$inputClass = "";
if (array_key_exists("validate", $attributes)) {
	$inputClass = 'class="' . $attributes['validate'] .'"';
}
$rdonly = "";
if ( array_key_exists("readonly",$attributes) ) {
    $rdonly='readonly="readonly" disabled="disabled"';
}
?>

    <? if ( $type == "radio" ) { ?><!-- RADIO BUTTON -->
    <div style="float: left; margin-right: 5px; vertical-align: middle;"> 
        <input <?=$inputId?> type="<?=$type?>" style="<?=getAttribute ("inputstyle", $attributes); ?>" name="<?=$fName?>" value="<?=$inputvalue?>" <?=$checked?> <?=$inputClass?> <?=$tooltip?> <?=$rdonly?> />
	<? if ($type != "hidden") { ?>
	    <div class="dbinput mdb-input <?=getAttribute( "class", $attributes); ?>" style="<?=getAttribute ("style", $attributes); ?>; clear: none; width: 80px; padding-top: 2px; padding-left: 3px;">
		    <? if (array_key_exists("label",$attributes)) { ?>
			    <label style="padding-left: 5px;" class="label" for="<?=$forId?>" style="<?=getAttribute ("labelstyle", $attributes); ?>"><?=$attributes['label']?></label>
		<? } else { ?>
        		<label class="label" for="<?=$forId?>">&nbsp;</label>
		<? } ?>
	</div>
	<? } ?>

    </div>

    <? } ?>



<? if ( $type != "radio" ) { ?> <!-- KEIN RADIO BUTTON -->

    <? if ($type != "hidden") { ?>
    <div class="dbinput mdb-input <?=getAttribute( "class", $attributes); ?>" style="<?=getAttribute ("style", $attributes); ?>">
	    <? if (array_key_exists("label",$attributes)) { ?>
		    <label class="label" for="<?=$forId?>"><?=$attributes['label']?></label>
	    <? } else if ( array_key_exists("nolabel", $attributes) && $attributes['nolabel'] == "true") { ?>
		    <!-- nolabel -->
	    <? } else { ?>
		    <label class="label" for="<?=$forId?>">&nbsp;</label>
	    <? } ?>
	    <input <?=$inputId?> type="<?=$type?>" style="<?=getAttribute ("inputstyle", $attributes); ?>" name="<?=$fName?>" value="<?=$inputvalue?>" <?=$checked?> <?=$inputClass?> <?=$tooltip?> <?=$rdonly?> />

	    <? /** OPTIONAL ein text anhaengen **/
  if ( getAttribute ("append", $attributes, false)) { ?>
		<div style="float: left; padding-top: 3px; width: 10px; top: 0px; position: absolute; right: 0px; text-align: right; z-index: 999;"><?=getAttribute ("append", $attributes)?></div>
	    <? } ?>
    </div>

    <? } ?>

<? if ( $type=="hidden" ) { ?>
    <input  <?=$inputId?> type="<?=$type?>" name="<?=$fName?>" value="<?=$inputvalue?>" <?=$checked?> <?=$inputClass?> <?=$tooltip?> <?=$rdonly?> />
    <? } ?>

<? } ?>




