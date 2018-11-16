<?php
	$headline = getAttribute("label", $attributes);
	$style = getAttribute("style",$attributes);
	$qp = getAttribute("queryparm",$attributes, "a");
	$href = getAttribute("href",$attributes, "");
	$icon = getAttribute("icon",$attributes, "/img/admin/icon_seminare.png");
?>
<script language="text/javascript">
	function alphabetKontaktClick(a)
	{
		var searchtext = a; 
		var searchart = $("[name=search_art]:checked").val(); 
		$.mosaikRuntime.load( '<?=$href?>?<?=$qp?>=' + escape(searchtext) + '&art=' + escape(searchart));	
	}
</script>
<div class="ui-corner-all widget-alphabet" style="<?=$style?>" >
<img src="<?=$icon?>" border="0" style="position:absolute;left:60px;top:-25px;"/>
	  	<div style="width:100%;text-align:center;"><span class="headline" style=""><?=$headline?></span></div>
<br/><br/>
		<span class="widget-content">
<? for (  $i=65; $i<91; $i++ ) { ?>
	<a style="display: block; float: left; width: 20px; height: 20px;" href="#" onClick="javascript:alphabetKontaktClick('<?=chr($i)?>')"><?=chr($i) ?></a>
	<? if ( $i % 12 == 0 ): ?><br/><? endif;?>
<? } ?>
	</span>
</div>
