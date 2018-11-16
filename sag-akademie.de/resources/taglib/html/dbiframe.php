<?
$id="";
if ( getAttribute("name", $attributes, false)) {
	$id = $dsl->get("dbtable", $attributes['name']);
}

$eId = getOptional("id");
$name = sprintf('name="%s"', getAttribute("id",$attributes));
$base = $attributes['base'];
$style = getAttribute("style", $attributes, "position: absolute; left:1px; right: 1px; bottom: 1px;");
?>
<iframe <?=$eId?> <?=$name?> src="<?=$base?><?=$id?>"  style="<?=$style?> height: 100%; width: 100%;">
</iframe>