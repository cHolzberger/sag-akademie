<?
$tempId="";
if ( isset($GLOBALS['dynRibbonGroup'])) {	  
	$GLOBALS['dynRibbonGroup'] ++;
	$tempId = "dynRibbonGroup" . $GLOBALS['dynRibbonGroup'];
} else {
	$GLOBALS['dynRibbonGroup']=0;
	$tempId = "dynRibbonGroup" . $GLOBALS['dynRibbonGroup'];
}

$forId = $forId = getAttribute("forId",$attributes,"Undefined Id");
$script = <<<END
	removeDynamicRibbons();
	// $('#{$tempId}').appendTo('#{$forId}');// wird inzwischen zu allen ribbon bars hinzugefuegt
	var count = 0;
	
	$('div.ui-ribbon-tab').each (function () {
		var elem = $('#{$tempId}').clone();	
		elem.appendTo ($(this));
		elem.attr("id", "dyn_" + count);
		elem.show();
		count ++;
	});
	
	$('#{$tempId}').remove();
END;
addSiteScript($script);
 
?>
<div style="display: none;" class="ui-ribbon-group ui-dynamic-ribbon-group ui-corner-all ui-widget-header removeOnReload" id="<?=$tempId?>">
	<?=$value?>
</div>