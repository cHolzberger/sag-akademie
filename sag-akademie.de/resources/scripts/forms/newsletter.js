/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

$().ready(function () {
	$("#newsletterAnmelden").click(function ( ) {
		// bugfix
		$("#newsletterAnmeldenForm .re").addClass("required");

		var v = $("#newsletterAnmeldenForm").validate();
		$("#newsletterAnmeldenForm input").valid();


		if ( $("#newsletterAnmeldenForm .error").length == 0) {
			$("#newsletterAnmeldenForm").submit();
		}

		$("#newsletterAnmeldenForm .re").removeClass("required");
	});

	$("#newsletterAbmelden").click(function ( ) {
		$("#newsletterAbmeldenForm .re").addClass("required");
		var v = $("#newsletterAbmeldenForm").validate();

		$("#newsletterAbmeldenForm input").valid();


		if ( $("#newsletterAbmeldenForm .error").length == 0) {
			$("#newsletterAbmeldenForm").submit();
		}
		$("#newsletterAbmeldenForm .re").removeClass("required");
	})
});
