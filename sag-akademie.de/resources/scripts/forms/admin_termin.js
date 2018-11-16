/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

function kursgebuehr_changed () {
	var mf = new MosaikMoneyFormat();

	var _fKosten = mf.toNumber($("#input_Seminar_kursgebuehr").val());
	var _refKosten = mf.toNumber($("#input_Seminar_kosten_refer").val());
	var _unterlagenKosten = mf.toNumber($("#input_Seminar_kosten_unterlagen").val());
	var _verpflegungKosten = mf.toNumber($("#input_Seminar_kosten_verpflegung").val());


	$("#input_Seminar_kursgebuehr")[0].value = mf.format ( _fKosten);
	$("#input_Seminar_kosten_refer")[0].value = mf.format ( _refKosten);
	$("#input_Seminar_kosten_unterlagen")[0].value = mf.format ( _unterlagenKosten);
	$("#input_Seminar_kosten_verpflegung")[0].value = mf.format ( _verpflegungKosten);

	recalculate();
}



function updateReferenten(data) {
	var _mt = new MosaikMailto();

	_mt.setSubject (  $("#input_Seminar_kursnr").val() );
	_mt.addTo("info@sag-akademie.de");
	//console.log(data);
	var _referenten = data.data;
	var string="";
	
	for ( var tag=1; tag<= data.length; tag++ ) {
		string += "<tr><td class='tag-label'>"+tag+".Tag</td><td>";
		console.log("Theorie");
		for ( var i=0;i < _referenten[tag].length; i++ ) {
			if ( _referenten[tag][i].theorie == "1" ||  (_referenten[tag][i].theorie="0" &&  _referenten[tag][i].praxis=="0") ) {
				if  ( _referenten[tag][i].email != "" && !_mt.isBcc(_referenten[tag][i].email ) ) {
					_mt.addBcc ( _referenten[tag][i].email);
				}
				string +="<a href='#/admin/referenten/"+_referenten[tag][i].id +"?edit'>"+_referenten[tag][i].name + ", "+_referenten[tag][i].vorname + "</a><br/>";

				console.log(_referenten[tag][i]);
			}
		}
		string +="</td><td>";
		console.log("Praxis");
		for ( var i=0;i < _referenten[tag].length; i++ ) {
			if ( _referenten[tag][i].praxis == "1" ) {
				if  ( _referenten[tag][i].email != "" && !_mt.isBcc(_referenten[tag][i].email ) ) {
					_mt.addBcc ( _referenten[tag][i].email);
				}
				string +="<a href='#/admin/referenten/"+_referenten[tag][i].id +"?edit'>"+_referenten[tag][i].name + ", "+_referenten[tag][i].vorname + "</a><br/>";

				console.log(_referenten[tag][i]);
			}
		}
		string += "</td></tr>";
	}

	$("#linkReferentenMail").click( function () {
		window.location = _mt.toString();
		console.log ( _mt.toString());
		return false;
	});

	$("#referenten").html(string);
	$("#referentenTable").show();
}

function requestUpdateReferenten() {
	$("#referentenTable").hide();
	// FIXME: die input_id wird es sicher nicht mehr lange geben
	return $.getJSON("/admin/json/seminarReferent/"+$("#input_id").val()+";json", {
		standort_id: $("#input_Seminar_standort_id").val()
		}, updateReferenten);
}

function recalculateUmsatz (sourceId, quantumId, targetId) {
	var _target = $(targetId);
	var _source = $(sourceId);
	var _quantum = $(quantumId);

	_target.fadeOut();

	_target.queue ( function () {
		var mf = new MosaikMoneyFormat();

		var _sKosten = _source.val();
		var _fKosten = mf.toNumber(_sKosten);

		var quantum = new Number(_quantum.val());

		_target.html( mf.format (_fKosten * quantum));

		$(this).dequeue();

		
	});
	_target.fadeIn();
}

function recalulateAuslastung () {
	var _max = new Number($("#input_Seminar_teilnehmer_max").val());
	var _cur = new Number ( $("#input_anzahlBestaetigt").val());

	var _pro =parseInt( _cur *100 / _max );

	$("#tnAuslastung").html ('(' + _pro.toString()  + "%)&nbsp;");
}

function recalculateVerwKosten() {
	var mf = new MosaikMoneyFormat();

	var proz = new Number($("#input_Seminar_kosten_allg").val()) / 100;
	var umsatz =	mf.toNumber($("#tnUmsatzStand").html());
	$("#seminarVerwaltungskosten").html( mf.format (proz*umsatz) );
}

function recalculateGewinn() {
	var mf = new MosaikMoneyFormat();
	var _dauer = mf.toNumber($("#input_Seminar_dauer").val());

	var _umsatz =	mf.toNumber($("#tnUmsatzStand").html());
	var _refKosten = mf.toNumber($("#input_Seminar_kosten_refer").val());
	var _unterlagenKosten = mf.toNumber($("#input_Seminar_kosten_unterlagen").val());
	var _verpflegungKosten = mf.toNumber($("#input_Seminar_kosten_verpflegung").val());
	var _pruefungKosten = mf.toNumber($("#input_Seminar_kosten_pruefung").val());
	var _verwKosten = mf.toNumber( $("#seminarVerwaltungskosten").html() );
	var _cur = new Number ( $("#input_anzahlBestaetigt").val());

	var _pruefungsgebuehren = (_umsatz - _unterlagenKosten * _cur -  _verpflegungKosten * _cur * _dauer) * (_pruefungKosten / 100);

	$("#seminarUnterlagen").html( mf.format( _unterlagenKosten * _cur) );
	$("#seminaVerpflegung").html( mf.format( _verpflegungKosten * _cur * _dauer) );
	$("#seminarPruefung").html( mf.format( _pruefungsgebuehren) );

	$("#seminarReferentenkosten").html( mf.format( _refKosten * _dauer) );
	
	var gewinn = _umsatz;
	gewinn -= _refKosten * _dauer;
	gewinn -= _unterlagenKosten * _cur;
	gewinn -= _cur * _verpflegungKosten * _dauer;
	gewinn -=  _pruefungsgebuehren;
	gewinn -= _verwKosten;

	if ( gewinn < 0 ) {
		$("#gewinn").css("color","red");
	} else {
		$("#gewinn").css("color","black");
	}
	$("#gewinn").html ( mf.format ( gewinn ));
}

function calculateDauer() {
	var mf = new MosaikMoneyFormat();


	var dauer = mf.toNumber($("#input_Seminar_dauer").val());
	var pause = mf.toNumber ( $("#input_Seminar_pause_pro_tag").val());
	$("#input_Seminar_pause_pro_tag")[0].value = mf.format(pause);
	var pauseGesamt = pause * dauer;

	var stunden_pro_tag = mf.toNumber ( $("#input_Seminar_stunden_pro_tag").val() );
	$("#input_Seminar_stunden_pro_tag")[0].value = mf.format(stunden_pro_tag);
	var ue_pro_stunde = 4/3;
	
	var stundenGesamt = stunden_pro_tag * dauer - pauseGesamt;
	var ueGesamt = Math.round (stundenGesamt * ue_pro_stunde * 10)/10;

	$("#gesamtUe").html( mf.format (ueGesamt) + " UE");
	$("#pauseGesamt").html( mf.format(pauseGesamt) );
	$("#stundenGesamt").html(mf.format( stundenGesamt + pauseGesamt) );
	$("#gesamtStunden").html( mf.format (stundenGesamt) + " Std");
}

function recalculate () {
	recalculateUmsatz("#input_Seminar_kursgebuehr", "#input_Seminar_teilnehmer_max", "#tnUmsatzMax");
	recalculateUmsatz("#input_Seminar_kursgebuehr", "#input_Seminar_teilnehmer_min", "#tnUmsatzMin");
	recalculateUmsatz("#input_Seminar_kursgebuehr", "#input_anzahlBestaetigt", "#tnUmsatzStand");
	recalulateAuslastung();
	

	//buggy browser?
	window.setTimeout ( "recalulateStep2 ()",500);
}

function recalulateStep2 () {
	recalculateVerwKosten();
	recalculateGewinn();
}


$().ready (function () {
	// event handler verbinden 
	$("#input_Seminar_kursgebuehr").change(kursgebuehr_changed);
	$("#input_Seminar_kosten_refer").change(kursgebuehr_changed);
	$("#input_Seminar_kosten_unterlagen").change(kursgebuehr_changed);
	$("#input_Seminar_kosten_verpflegung").change(kursgebuehr_changed);
	$("#input_Seminar_kosten_pruefung").change(kursgebuehr_changed);
	$("#input_Seminar_teilnehmer_min").change(recalculate);
	$("#input_Seminar_teilnehmer_max").change(recalculate);
	$("#input_Seminar_kosten_allg").change(recalculate);
	$(window).resize(resizeTable);

	// erstes update anstossen
	recalculate();
	requestUpdateReferenten();
	requestUpdateMailtoTeilnehmer();
	kursgebuehr_changed();
});

$().ready ( function () {
	window.setTimeout( "resizeTable()", 2500);
});

$().ready(function () {
	calculateDauer();
	$("#input_Seminar_dauer").change(calculateDauer);
	$("#input_Seminar_pause_pro_tag").change(calculateDauer);
	$("#input_Seminar_stunden_pro_tag").change(calculateDauer);
});

function resizeTable() {
	$(".itable2")[0].style.height= $(".content").height() - $("#dbform").height() + "px !important";
}

/*** MAIL AN TEILNEHMER ***/
function updateMailtoTeilnehmer(data) {
	var _teilnehmer = data.data;
	var _mt = new MosaikMailto();

	_mt.setSubject (  $("#input_Seminar_kursnr").val());
	_mt.addTo("info@sag-akademie.de");

	for ( var i=0; i < data.count; i++ ) {
		_mt.addBcc(_teilnehmer[i].email)
	}

	$("#linkTeilnehmerMail").click ( function () {
		window.location =  _mt.toString() ;
		console.log ( _mt.toString() );
		return false;
	})
	
}

function requestUpdateMailtoTeilnehmer() {
	// FIXME: die input_id wird es sicher nicht mehr lange geben
	return $.getJSON("/admin/json/terminTeilnehmer/"+$("#input_id").val()+";json", {}, updateMailtoTeilnehmer);
}