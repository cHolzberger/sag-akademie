<?
	$headline = getAttribute("headline", $attributes,"");
	$icon = getAttribute("icon",$attributes, "/img/admin/icon_seminare.png");
	$style = getOptional("style",$attributes);
?>
<div class="ui-corner-all widget-container" <?=$style?> >
	<img src="<?=$icon?>" border="0" style="position:absolute;left:90px;top:-25px;"/>
  	<div style="width:100%;text-align:center;"><span class="headline" style=""><?=$headline?></span></div>
	<br/><br/>
	<span class="widget-content">
	<?=$value?>
	</span>
</div>