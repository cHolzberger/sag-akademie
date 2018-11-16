<?
$dataNs = getAttribute("table",$attributes,"dbtable"); 

$inputvalue = $dsl->get($dataNs, $attributes['name']);

// convert the input value
$inputvalue = convertString($inputvalue, $attributes);

//$inputvalue = str_replace('p style=', "p oldstyle=", $inputvalue); // quick hack
$inputvalue = noStyle($inputvalue);

$suffix="";
if (array_key_exists("suffix", $attributes)) {
	$suffix = "&nbsp;".$attributes['suffix'];
}

$valign = 'valign="top"';
if (array_key_exists("valign",$attributes)) {
	$valign=sprintf('valign=%s', $attributes['valign']);
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
<? if (!array_key_exists("notable", $attributes) ) { ?><td <?=$jslink?> <?=$class?> <?=$style?> <?=$valign?>><? }
else { ?> <span <?=$class?> <?=$style?>> <? } ?>
<?=$alink?>
<?=$inputvalue ?><?=$suffix?>
<?=$alinkend?>
<? if (!array_key_exists("notable",$attributes ) ) { ?></td><? }
else { ?></span><? } ?>
