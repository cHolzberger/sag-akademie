<?
$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);
@$add = $attributes['offset'];
if (isset($add)) {
    $inputvalue = $inputvalue + $add;
}
// convert the input value
$inputvalue = convertString($inputvalue, $attributes);
$suffix = "";
if (array_key_exists("suffix", $attributes)) {
	$suffix = "&nbsp;".$attributes['suffix'];
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
<a <?=$class?> <?=$style?> href="mailto:<?=$inputvalue?>">
<?=$alink?>
<?=$inputvalue ?><?=$suffix?>
<?=$alinkend?>
</a>
