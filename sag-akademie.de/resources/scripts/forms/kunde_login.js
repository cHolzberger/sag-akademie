/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */


$().ready(function ( ) {
	$("#pw_anfragen").click(function ( ) {
		if ( $("#pw_username")[0].value.length < 1 ) {
			alert("Fehler: Sie haben keinen Benutzernamen eingegeben.");
			return;
		}

		$("#pwvergessen").submit();
	});


});