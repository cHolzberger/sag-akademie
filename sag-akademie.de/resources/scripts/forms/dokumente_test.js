/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
jQuery().ready(function () {
	jQuery('#testSendenButton').click(function() {

		window.open ('/_notifications.php?test=' + jQuery('#testVorlage').val(),'_blank','status=0,toolbar=0,location=0,menubar=0,directories=0,width=400,height=450');
		
	});
});

