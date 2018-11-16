<?
$id = $dsl->get("dbtable", $attributes['name']);
$uuid = createUuid(); 

$basehref=getAttribute("basehref", $attributes);
$target = getOptional("target", $attributes);
$class = getOptional("class", $attributes);

$ivalue = $prefix = "";
if (array_key_exists("value", $attributes)) {
	$ivalue = $dsl->get("dbtable", $attributes['value'], $attributes['value']);
} else { 
	$ivalue = $value;
}


if (array_key_exists("prefix", $attributes)) {
	$prefix = $dsl->get("dbtable", $attributes['prefix'], $attributes['prefix']);
	$prefix.="&nbsp;";
}

$tagname = "a";
$tagargs = sprintf ( 'href="%s%s"', $basehref, $id);
if (array_key_exists("type", $attributes)) {
	if ( $attributes['type'] == "button" ) {
		$tagname = "button";
		$tagargs = sprintf('id="%s"', $uuid);;
		addSiteScript( sprintf ( '$("#%s").click(function () { window.location.href = "%s%s" });', $uuid, $basehref, $id));
	}
} 



?>
<?=$prefix?><<?=$tagname?> <?=$tagargs?> <?=$class?> <?=$target?> ><?=$ivalue?></<?=$tagname?>>