<script type="text/javascript">
    $().ready(function ( ) {
	$("#kundeloginSenden").click(function () {
	    if ( $("#benutzer")[0].value.length < 1 || $("#password")[0].value.length < 1) {
		alert("Fehler: Bitte kontrollieren Sie Ihren Benutzername und Ihr Passwort.");
		return;
	    }
	    $("#kundelogin").submit();
	});
    });
</script>
<div>
    <form action="/kunde/" method="POST" id="kundelogin">
    <?=$value ?>
	<div class="headerLabel" style="width:315px;">
	    <b>
					Einloggen in den Kundenbereich
	    </b>
	</div>

	<div style="position:relative;width:315px;" class="labelInput">
				Benutzername:
	    <input name="username" style="position:absolute;right:10px;" type="text" id="benutzer"/>
	</div>

	<div style="position:relative;width:315px;" class="labelInput">
				Passwort:
	    <input style="position:absolute;right:10px;" name="password" type="password" id="password"/>
	</div>
    </form>
    <div style="position:relative;width:315px; height: 50px;">
	<input style="position:absolute;right:0px;margin-top:10px;" type="submit" value="Einloggen" id="kundeloginSenden"/>
    </div>

</div>