<?
$id = "#ID#"; //$dsl->get("dbtable","id");
$base = $attribute['basehref'];

$buttons = getAttribute("buttons", $attributes, "edit,delete");

$buttons = explode(",",$buttons);
?>

<div id="dbbuttons" class="dbContextMenu contextMenu ui-corner-all dbbuttons removeOnLoad" style="display:none;">
<?
foreach ( $buttons as $button) {
	$button=ltrim($button);
	$lbutton = ucfirst($button);
	$alt = getOptional("tooltip".$lbutton, $attributes, "alt");
	$text = getAttribute("tooltip" .$lbutton, $attributes, "Unbenannt");
?>
<a href="<?=$base?><?=$id?>?<?=$button?>" class="<?=$button?>">
<div class="contextMenuItem">
			<a href="<?=$base?><?=$id?>?<?=$button?>" class="<?=$button?>"><img src="/css/theme/icons/<?=$button?>.png" border="0" <?=$alt?> /> <?=$text?></a>
</div> 	
</a>
<?
} #foreach
?>

</div>