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
			<input type=text style="width: 140px;" id="search_<?=$id?>" /> 
			<button id="searchbtn_<?=$id?>">Suchen</button> 
	</span>
</div>

<script language="text/javascript">
	$("#search_<?=$id?>").keyup( function (ev) {
		if ( ev.keyCode == 13 ) {
			$("#searchbtn_<?=$id?>").click();
		}
	});
	
	$("#searchbtn_<?=$id?>").click( function() {
		var searchtext = $("#search_<?=$id?>").val(); 
		$.mosaikRuntime.load( '<?=$href?>?<?=$qp?>=' + escape(searchtext));		
	});
</script>
