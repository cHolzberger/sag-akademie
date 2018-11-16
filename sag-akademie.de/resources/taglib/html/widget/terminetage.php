<input type="button" id="termine7tage" value="Termine der nächsten 14 Tage" style="width:220px;"/><br/>
		<input type="button" id="termine30tage" value="Termine der nächsten 30 Tage" style="width:220px;"/><br/>
		<input type="button" id="termine90tage" value="Termine der nächsten 90 Tage" style="width:220px;"/>

		<script language="text/javascript">
	$("#termine7tage").click( function() {
		aktuell = new Date();
		aktuell2 = new Date();
		aktuell2.setDate(aktuell2.getDate()+14);
		aktuellTag = aktuell.getDate();
		if (aktuellTag < 10){
		aktuellTag = "0" + aktuellTag;
		}
		aktuellMonat = aktuell.getMonth()+1;
		if (aktuellMonat < 10){
		aktuellMonat = "0" + aktuellMonat;
		}
		aktuellJahr = aktuell.getYear()+1900;
		aktuell2Tag = aktuell2.getDate();
		if (aktuell2Tag < 10){
		aktuell2Tag = "0" + aktuell2Tag;
		}
		aktuell2Monat = aktuell2.getMonth()+1;
		if (aktuell2Monat < 10){
		aktuell2Monat = "0" + aktuell2Monat;
		}
		aktuell2Jahr = aktuell2.getYear()+1900;
		von = aktuellTag+'.'+aktuellMonat+'.'+aktuellJahr;
		bis = aktuell2Tag+'.'+aktuell2Monat+'.'+aktuell2Jahr;
		$.mosaikRuntime.load('/admin/termine?v='+von+'&b='+bis);	
	});
		$("#termine30tage").click( function() {
		aktuell = new Date();
		aktuell2 = new Date();
		aktuell2.setDate(aktuell2.getDate()+30);
		aktuellTag = aktuell.getDate();
		if (aktuellTag < 10){
		aktuellTag = "0" + aktuellTag;
		}
		aktuellMonat = aktuell.getMonth()+1;
		if (aktuellMonat < 10){
		aktuellMonat = "0" + aktuellMonat;
		}
		aktuellJahr = aktuell.getYear()+1900;
		aktuell2Tag = aktuell2.getDate();
		if (aktuell2Tag < 10){
		aktuell2Tag = "0" + aktuell2Tag;
		}
		aktuell2Monat = aktuell2.getMonth()+1;
		if (aktuell2Monat < 10){
		aktuell2Monat = "0" + aktuell2Monat;
		}
		aktuell2Jahr = aktuell2.getYear()+1900;
		von = aktuellTag+'.'+aktuellMonat+'.'+aktuellJahr;
		bis = aktuell2Tag+'.'+aktuell2Monat+'.'+aktuell2Jahr;
		$.mosaikRuntime.load('/admin/termine?v='+von+'&b='+bis);	
	});
		$("#termine90tage").click( function() {
		aktuell = new Date();
		aktuell2 = new Date();
		aktuell2.setDate(aktuell2.getDate()+90);
		aktuellTag = aktuell.getDate();
		if (aktuellTag < 10){
		aktuellTag = "0" + aktuellTag;
		}
		aktuellMonat = aktuell.getMonth()+1;
		if (aktuellMonat < 10){
		aktuellMonat = "0" + aktuellMonat;
		}
		aktuellJahr = aktuell.getYear()+1900;
		aktuell2Tag = aktuell2.getDate();
		if (aktuell2Tag < 10){
		aktuell2Tag = "0" + aktuell2Tag;
		}
		aktuell2Monat = aktuell2.getMonth()+1;
		if (aktuell2Monat < 10){
		aktuell2Monat = "0" + aktuell2Monat;
		}
		aktuell2Jahr = aktuell2.getYear()+1900;
		von = aktuellTag+'.'+aktuellMonat+'.'+aktuellJahr;
		bis = aktuell2Tag+'.'+aktuell2Monat+'.'+aktuell2Jahr;
		$.mosaikRuntime.load('/admin/termine?v='+von+'&b='+bis);	
	});
</script>