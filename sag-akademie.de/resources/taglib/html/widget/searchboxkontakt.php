<?php 
	$headline = getAttribute("label", $attributes);
	$style = getAttribute("style",$attributes);
	
	$qp = getAttribute("queryparm",$attributes, "q");
	$href = getAttribute("href",$attributes, "/admin");
	$icon = getAttribute("icon",$attributes, "/img/admin/icon_seminare.png");
	$id = md5(microtime());
?>
<div class="ui-corner-all widget-searchbox" style="<?=$style?>" >
<img src="<?=$icon?>" border="0" style="position:absolute;left:100px;top:-25px;"/>
	  	<div style="width:100%;text-align:center;"><span class="headline" style=""><?=$headline?></span></div>
<br/><br/>
		<span class="widget-content">
			<input type=radio name="search_art" value="3" style="width: 10px;" /> Beide<br />
			<input type=radio name="search_art" value="1" style="width: 10px;"/> Nur Akquise Kontakte<br />
			<input type=radio name="search_art" value="2" style="width: 10px;" checked="checked"/> Nur Kunden<br />
			<input type=text style="width: 140px;" id="search_<?=$id?>_full" /> 
			<button id="searchbtn_<?=$id?>_full">Suchen</button> 
	</span>
</div>

<script language="text/javascript">
	$("#search_<?=$id?>_full").keyup( function (ev) {
		if ( ev.keyCode == 13 ) {
			$("#searchbtn_<?=$id?>_full").click();
		}
	});
	
	$("#searchbtn_<?=$id?>_full").click( function() {
		var searchtext = $("#search_<?=$id?>_full").val(); 
		var searchart = $("[name=search_art]:checked").val(); 
		$.mosaikRuntime.load( '<?=$href?>?<?=$qp?>=' + escape(searchtext) + '&art=' + escape(searchart));		
	});
</script>
