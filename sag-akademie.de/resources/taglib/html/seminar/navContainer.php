<div class="navContainer">
<?=$value ?>
    <script type="text/javascript">
	$().ready ( function () {
	    var aktiv = "";
	    if ( ! window.location.hash ) {
		var splited = window.location.href.split("/");
		aktiv = decodeURI(splited[splited.length-1]);
		
	    } else {
		aktiv = decodeURI(window.location.hash.replace("#",""));
		var label = aktiv.replace ("_a", "ä");
		label = label.replace ("_o", "ö");
		label = label.replace ("_u", "ü");
		$("#rubrikLabel").html(label);
	    }
	    aktiv = aktiv.replace (" ", "");
	    $("#menu_" + aktiv).addClass("navItemActive");
	});
    </script>
</div>