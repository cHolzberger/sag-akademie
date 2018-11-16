<?php
	$headline = getAttribute("label", $attributes);
	$style = getAttribute("style",$attributes);
	
	$v = getAttribute("queryparm_von",$attributes, "v");
	$b = getAttribute("queryparm_bis",$attributes, "b");
	$href = getAttribute("href",$attributes, "/admin");
	$icon = getAttribute("icon",$attributes, "/img/admin/icon_seminare.png");
	$id = md5(microtime());
?>
<div class="ui-corner-all widget-searchbox" style="<?=$style?>" >
<img src="<?=$icon?>" border="0" style="position:absolute;left:100px;top:-25px;"/>
	  	<div style="width:100%;text-align:center;"><span class="headline" style=""><?=$headline?></span></div>
<br/><br/>
		<span class="widget-content">
			<div style="width:40px;float:left;padding-top:10px;">Von:</div><input class="datepicker" type="text" style="width: 140px;" id="search_von" /> 
			<div style="width:40px;float:left;padding-top:10px;clear:both;">Bis:</div><input class="datepicker" type="text" style="width: 140px;" id="search_bis" /><br/> 
			<button id="searchbtn_date" style="position: absolute; right: 47px; bottom: 5px;">Suchen</button> 
	</span>
</div>
<script language="text/javascript">
	$("#searchbtn_date").click( function() {
		var search_von = $("#search_von").val(); 
		var search_bis = $("#search_bis").val(); 
		$.mosaikRuntime.load( '<?=$href?>?<?=$v?>=' + escape(search_von) + '&<?=$b?>=' + escape(search_bis));		
	});
</script>
