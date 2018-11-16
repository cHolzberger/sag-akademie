/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
function updateKosten() {
	updateInput ("#input_Referent_kosten_ganzertag");
	updateInput ("#input_Referent_kosten_halbertag");
	updateInput ("#input_Referent_kosten_uebernachtung");
		updateInput ("#input_Referent_kilometerpauschale");
		calculateAnfahrt();

}

function calculateAnfahrt () {
		var mf = new MosaikMoneyFormat();
	var kosten = mf.toNumber($("#input_Referent_kilometerpauschale").val());

	var _kmLuenen = parseInt ($("#input_Referent_kosten_anfahrt_luenen").val());
	var _kmDarmstadt =parseInt ($("#input_Referent_kosten_anfahrt_darmstadt").val());

	$("#kostenAnfahrtLuenen").html ( mf.format ( _kmLuenen * kosten));
	$("#kostenAnfahrtDarmstadt").html ( mf.format ( _kmDarmstadt * kosten));
}

function updateInput ( id ) {
	var mf = new MosaikMoneyFormat();
	var kosten = mf.toNumber($(id).val());
	$(id)[0].value = mf.format ( kosten );
}

$().ready(function () {
	updateKosten();
	$("#input_Referent_kosten_ganzertag").change( updateKosten );
	$("#input_Referent_kosten_halbertag").change( updateKosten );
	$("#input_Referent_kosten_uebernachtung").change( updateKosten );
		$("#input_Referent_kilometerpauschale").change( updateKosten );


});