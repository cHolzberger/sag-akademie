<?
$tempId="";
if ( isset($GLOBALS['dynRibbonGroupRight'])) {	  
	$GLOBALS['dynRibbonGroupRight'] ++;
	$tempId = "dynRibbonGroupRight" . $GLOBALS['dynRibbonGroupRight'];
} else {
	$GLOBALS['dynRibbonGroupRight']=0;
	$tempId = "dynRibbonGroupRight" . $GLOBALS['dynRibbonGroupRight'];
}

$offset = getAttribute("offset", $attributes, "15px");
$forId = $forId = getAttribute("forId",$attributes,"Undefined Id");
$class = getAttribute ("class", $attributes, "ui-ribbon-group-save");

$script = <<<END
	//$('#{$tempId}').appendTo('#{$forId}'); // right ribbons immer sichtbar
	$('#{$tempId}').appendTo('body');
	$('#{$tempId}').show();
END;
addSiteScript($script);
 
?>
<div style="z-index: 1001; display: none; position: fixed; top: 42px; right: <?=$offset?>;" class="ui-ribbon-group <?=$class?> ui-dynamic-ribbon-group ui-corner-all ui-widget-header removeOnReloadRight removeOnLoad" id="<?=$tempId?>">
	<?=$value?>
</div>