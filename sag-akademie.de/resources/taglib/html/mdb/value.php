<?
$mpath = (getAttribute ("mpath", $attributes));
$inputvalue = "";
$inputId="";
$emptyValue = "";
if ( array_key_exists("emptyValue", $attributes) ) {
	$emptyValue = $attributes["emptyValue"];
}

if ( ! empty ($mpath) ) {
	$inputvalue = $dsl->get("dbtable", $mpath);
	if ( $inputvalue == $emptyValue) $inputvalue = "";
	$inputId = sprintf ( "input_%s", str_replace(":","_",$attributes['mpath']));
}

if ( empty ($inputvalue) ) {
    $inputvalue = strval( getAttribute("default", $attributes, null) );
	if ( $inputvalue == $emptyValue) $inputvalue = "";
}

if ( array_key_exists("offset", $attributes)) {
	@$add = $attributes['offset'];
	if (isset($add)) {
	    $inputvalue = $inputvalue + $add;
	}
}
// convert the input value
$inputvalue = convertString($inputvalue, $attributes);
$suffix = "";
if (array_key_exists("suffix", $attributes)) {
	$suffix = "&nbsp;".$attributes['suffix'];
}

if (array_key_exists("nonEmptySuffix", $attributes) && !empty($inputvalue)) {
	$suffix = $attributes['nonEmptySuffix'];
}

$class = getOptional("class", $attributes);
$style = getOptional("style", $attributes);

$link = "";
$alink = "";
$jslink = "";
$alinkend='';
if (array_key_exists("link", $attributes)) {
	$id = $dsl->get("dbtable", $attributes['linkOn']);
	$linksuffix = $attributes['linkSuffix'];
	$base = $attributes['link'];
	$jslink="onClick=\"document.location='$base$id$linksuffix'\"";
	$alink="<a class=\"dba\" href=\"$base$id$linksuffix\" border=0>";
	$alinkend ="</a>";
}
?>
<span <?=$class?> <?=$style?>>
<?=$alink?>
<?=$inputvalue ?><?=$suffix?>
<?=$alinkend?>
    <input  id="<?=$inputId?>" type="hidden" name="__ignore" value="<?=$inputvalue?>"  />
</span>
