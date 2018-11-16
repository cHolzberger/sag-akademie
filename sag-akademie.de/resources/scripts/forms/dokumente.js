/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/** INIT **/
if ( checkEdit === undefined ) {
	var currentEdit = null;
}

function checkEdit () {
	if ( currentEdit != null ) {
		$.mosaikDs.set(currentEdit + "_text", $("#vorlagen_editor").ckeditorGet().getData());
		$.mosaikDs.set(currentEdit + "_betreff", $("#betreff")[0].value);
		$.mosaikDs.set(currentEdit + "_check", $("#tage")[0].value);

		if ( currentEdit == "warnung1_mail" || currentEdit == "warnung2_mail" || currentEdit=="warnung3_mail" ) {
			showEinstellungen();
		} else {
			hideEinstellungen;
		}
	}
}

function updateData() {
	if ( currentEdit != null ) {
		$("#vorlagen_editor").ckeditorGet().setData($.mosaikDs.get(currentEdit + "_text"));
		$("#betreff")[0].value = $.mosaikDs.get(currentEdit + "_betreff");
		$("#tage")[0].value = $.mosaikDs.get(currentEdit + "_check");
	}
}

function hideEinstellungen() {
	$("#warnung_mail_einstellung").hide();
}

function showEinstellungen() {
	$("#warnung_mail_einstellung").show();
}

// vor dem absenden datenfelder uebertragen
var _onSubmit = function () {
	checkEdit();
	$(window).unbind("beforeSubmit", _onSubmit);
}



$().ready(function()
{
	$("#vorlagen_editor").css('width','95%').css('height','95%');
	hideEinstellungen()
	var config = {
		toolbar:
		[
			['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink'],
			['UIColor']
		],
		height: ($('#imain').height() - 125 ).toString() + "px",
		width: ( $("#imain").width() - 260).toString()+"px"
	};
	$("#betreff").width( ($("#imain").width() - 200 - 200).toString()+"px" );
	$('#vorlagen_editor').ckeditor(config);
	// event bindings vor dem absenden
	$(window).bind("beforeSubmit", _onSubmit);

	window.setTimeout ( updateData, 500 );
});

/** FENSTER GROESSE ANPASSEN **/
$(window).resize(function () {
	$("#cke_contents_vorlagen_editor").height ( $('#imain').height() - 125  );
	// achtung kein bug! hoehe wird ueber den editor eingestellt
	// breite ueber den wrapper
	$("#cke_vorlagen_editor").width ( $('#imain').width() - 260  );
	$("#betreff").width( ($("#imain").width() - 200 - 200).toString()+"px" );
});

/** GEBURTSTAGSMAIL **/
$('#geb_mail').click(function () {
	checkEdit();
	currentEdit = "geb_mail";
	hideEinstellungen();
	updateData();
});

/** WARNUNG 1 **/
$('#warnung1_mail').click(function () {
	checkEdit();
	currentEdit = "warnung1_mail";
	showEinstellungen();
	updateData();

});

/** WARNUNG 2 **/
$('#warnung2_mail').click(function () {
	checkEdit();
	currentEdit = "warnung2_mail";
	showEinstellungen();

	updateData();
});

/** WARNUNG 3 **/
$('#warnung3_mail').click(function () {
	checkEdit();
	currentEdit = "warnung3_mail";
	showEinstellungen();

	updateData();
});