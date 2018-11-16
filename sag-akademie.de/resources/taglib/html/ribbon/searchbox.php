<?php
	$headline = getAttribute("label", $attributes);
	$style = getAttribute("style",$attributes);
	
	$qp = getAttribute("queryparm",$attributes, "q");
	$href = getAttribute("href",$attributes, "/admin");
	$icon = getAttribute("icon",$attributes, "/img/admin/icon_seminare.png");
	$id = md5(microtime());
?>
<div style="width: 375px; padding-top: 0px; clear: none;" class="dbinput">
<div style="width: 70px; float: left; padding-left: 15px; padding-top: 5px;">
<?=$headline?>
</div>
<input type="text" id="search_<?=$id?>" class="ui-corner-all" style="width: 130px;"/>&nbsp;&nbsp;<button id="searchbtn_<?=$id?>">Suchen</button>
</div>
<?
$script = <<<END
	// ribbon wird beim starten geladen daher:
	$().ready (function() {
	$("#search_{$id}").keyup( function (ev) {
		if ( ev.keyCode == 13 ) {
			$("#searchbtn_{$id}").click();
		}
	});
	
	$("#searchbtn_{$id}").click( function() {
		var searchtext = $("#search_{$id}").val(); 
		$.mosaikRuntime.load( '{$href}?{$qp}=' + escape(searchtext), true);		
	});
	});
END;
addSiteScript($script);
?>