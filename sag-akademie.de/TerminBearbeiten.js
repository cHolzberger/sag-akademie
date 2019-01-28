/** require dependencies **/
dojo.require("dojo.number");

dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.CheckBox');

dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.CurrencyTextBox');
dojo.require("dojox.widget.Standby");



dojo.require('mosaik.ui.FlexTable');
dojo.require('mosaik.ui.DatasetNavigator');

dojo.require("mosaik.util.Mailto")

dojo.provide("module.terminBearbeiten.TerminBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.terminBearbeiten.TerminBearbeiten", [mosaik.core.Module], {
	moduleName: "TerminBearbeiten",
	moduleVersion: "1",
	dropdowns: null,
	service: null,
	referentService: null,
	referentStandby: null,
	_options: null,
	formPrefix: "Seminar",
	container: null, // container node -> border container
	flexTableServiceURL: "",
	_currentActiveSeminarButton: null,

	// rte
	_rteEvtHandle: null, // reference to store the event handler reference in
	
	buttons: {
		seminarEditBtn: null,
		startseiteBtn: null,
		infoBtn: null,
		kalenderBtn: null,
		refTerminEditBtn: null,
		refSeminarEditBtn: null,
		anwesenheitslisteBtn: null,
		tischbeschilderungBtn: null,
		teilnehmerlisteBtn: null,
		referentenMailBtn: null,
		teilnehmerMailBtn: null,
		topBtn: null,
		speichernBtn: null
	},

	constructor: function () {
		this.dropdowns = {};
	},

	run: function( options ) {
		this.widgets.dnav.update( options, "terminId", "terminBearbeiten");
		console.debug("TerminBearbeiten::run >>>");
		// need to create service on run - air is a bit picky
		// on modifying the dom while running
        console.dir(options);
		this.service =  this.sandbox.getRpcService("database/termin");
		this.referentService = this.sandbox.getRpcService("database/referent");
		this.container = dijit.byId('borderContainer');
		this._options = options;

		this.dropdowns = {};
		this.initForm();

        this.initFlexTable();

        if ( typeof ( options.create ) !== "undefined") {
			this.onCreate();
		} else {
            this.updateFlexTable();
			this.fetchData();
		}



		console.debug ("<<< TerminBearbeiten::run");

	},

    onResume: function () {
        this.updateFlexTable();
    },

	onCreate: function() {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

		this._newSeminarArt = null;

		dijit.byId("newStandort").addOption( sandbox.getSelectArray("Standort"));

		this.chooserFrame.show();
		this.flexTable.hide();

		// get buttons
		this.nextButton = dijit.byId("nextButton");
		this.prevButton = dijit.byId("prevButton");
		this.createButton = dijit.byId("createButton");

		
		this.createSeminarArt();
	},

	createSeminarArt: function () {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;
		
		var newSeminarArtId = 0;

		this.chooserFrame.set("title","Seminar ausw&auml;hlen");
		this.chooserStack.selectChild( dijit.byId("seminarArtPane"));

		// Summary:
		// syncs the "rubrik" table with the view
		var data =  sandbox.getObject("SeminarArtRubrik");
		var seminareStore = sandbox.getItemStore("SeminarArt");

		if ( typeof(this._options.seminarArtId) !== "undefined" ) {
			newSeminarArtId  = this._options.seminarArtId;
		}

		// hide prev and create button
		this.createButton.domNode.style.display="none";
		this.prevButton.domNode.style.display="none";

	
		this.seminarArtContainer.innerHTML="";

		for ( var i=0; i < data.length; i++) {
			var container = dojo.create("div", {
				innerHTML: data[i].name, 
				"class": "gradient1", 
				style: "margin-bottom: 2px; margin-top: 2px; padding-top: 2px; padding-bottom: 2px; padding-left: 2px; font-weight: bold;"
			}, this.seminarArtContainer);

			var seminare = seminareStore.query ( {
				"fk1": data[i].id
			} );
			
			
			dojo.forEach ( seminare, dojo.hitch(this, function( item ) {
				var _class = "seminarBtn";
				
				if ( item.name == newSeminarArtId ) {
					this._newSeminarArt = item;
					_class += " seminarBtnActive";
				}
				
				var element = dojo.create("div", {
					style: "padding-left: 5px;", 
					innerHTML: item.name , 
					"class": _class
				}, this.seminarArtContainer);

				dojo.connect(element, "onclick", this, function(e) {
					this.nextButton.domNode.style.display="block";
					if ( this._currentActiveSeminarButton != null) {
						dojo.removeClass(this._currentActiveSeminarButton, "seminarBtnActive");
					}
					dojo.addClass(e.currentTarget, "seminarBtnActive");
							
					this._newSeminarArt = item;
					this._currentActiveSeminarButton = e.currentTarget;
				});
			}));
			
			
		}
		
		// newSeminarArt may change in the loop
		if ( this._newSeminarArt == null) {
			this.nextButton.domNode.style.display="none";
		} else {
			this.nextButton.domNode.style.display="block";
		}

		
		this._nextButtonHandle= dojo.connect( this.nextButton, "onClick", this, "createStandortDatum");
	},

	createStandortDatum: function() {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

		this.chooserFrame.set("title","Standort und Datum eingeben");
		this.chooserStack.selectChild( dijit.byId("terminDetailPane"));

		this.createButton.domNode.style.display="block";
		this.prevButton.domNode.style.display="block";
		this.nextButton.domNode.style.display="none";

		this._prevButtonHandle= dojo.connect( this.prevButton, "onClick", this, "createSeminarArt");
		this._createButtonHandle = dojo.connect( this.createButton, "onClick", this, "createDone");
	},


	createDone: function () {
		sandbox.showLoadingScreen("Erstelle Termin...");
		// Summary: form input is complete... send it to the server
		var seminarArtId = this._newSeminarArt.name.toString();
		var standortId = dijit.byId("newStandort").get("value");
		var newValue = dijit.byId("newDatumVon").get("value");
		var newEnde = newValue.getTime();
		var datumBegin =  ""+newValue.getFullYear() + "-" +  (parseInt(newValue.getMonth())+1).toString() + "-" + newValue.getDate();
		var datumEnde =  ""+newValue.getFullYear() + "-" +  (parseInt(newValue.getMonth())+1).toString() + "-" + newValue.getDate();
		var gesperrt = dijit.byId("newGesperrt").get("value");

		this.service.create(seminarArtId, standortId, datumBegin , gesperrt ).addCallback( dojo.hitch( this, "updateData" ))
		.addErrback(function (data) {
			console.log ("Seminar-Save Error: " + data);
			sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});

	},

	initForm: function () {
		// fixme: fehleR?!
		dojo.connect(this, "onValuesSet", this, "numberChanged");
		dojo.connect(this, "valueUpdate", this, "numberChanged");

		// container
		this.chooserFrame = dijit.byId("chooserFrame");
		this.chooserStack = dijit.byId("chooserStack");
		this.seminarArtContainer = dojo.byId("seminarArtContainer");

		// dom elemente holen
		this.dropdowns.standort = dijit.byId ('Seminar:standort_id');
		this.dropdowns.freigabe = dijit.byId ('Seminar:freigabe_status');

		this.dropdowns.standort.set("options", sandbox.getSelectArray("Standort"));
		this.dropdowns.freigabe.set("options", sandbox.getSelectArray("SeminarFreigabestatus"));

		this.referentStandby = new dojox.widget.Standby ({
			target: "referentenContainer"
		});

		this.referentStandby.show();
		this.linkButtons();
	},


	onSave: function () {
		sandbox.showLoadingScreen("Daten speichern...");
		console.log("Save");
		//console.dir(this._changedData);

		this.service.save( this._currentData.id, this._changedData ).addCallback( dojo.hitch( this, "updateData" ))
		.addErrback(function (data) {
			console.log ("Seminar-Save Error: " + data);
			sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},

	fetchData: function () {
		this.service.find(this._options.terminId).addCallback ( this, "updateData" )
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> TerminBearbeiten::run Error: "+data);
		}));
	},

	updateData: function (data) {
		this._ignoreUpdate = true;
		console.log("SaveDone");
		console.dir(data);
		this.setValue(data);


		this.referentService.findByTermin (data.id, data.standort_id )
		.addCallback ( dojo.hitch ( this, "setupReferenten") )
		.addErrback(dojo.hitch ( this, function(data) {
			console.log("==!!> ReferentenLaden fehler: " + data);
		}));
		
		sandbox.hideLoadingScreen();

		this.updateFlexTable();
		this.chooserFrame.hide();
		this._ignoreUpdate = false;
	},

	linkButtons: function() {
		var key;
		for ( key in this.buttons ) {
			this.buttons[key] = dojo.byId(key);
		};

		dojo.connect(this.buttons.topBtn, "onclick", this, function (e) {
			// http://sag-akademie.de/pdf/top2;pdf/
			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/pdf/top2;pdf/" + this._currentData.seminar_art_id);
			dojo.stopEvent(e);
		});
		
		dojo.connect(this.buttons.startseiteBtn, "onclick", this, function(e) {
			
			
			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") );
			dojo.stopEvent(e);

		});

		dojo.connect(this.buttons.infoBtn, "onclick", this, function(e) {
			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/seminar/info/" + this._currentData.seminar_art_id );
			dojo.stopEvent(e);

		});


		dojo.connect(this.buttons.infoBtn, "onclick", this, function(e ) {
			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/seminar/buchen/" + this._currentData.id );
			dojo.stopEvent(e);
		});

		dojo.connect(this.buttons.seminarEditBtn, "onclick", this, function (e ) {
			sandbox.loadShellModule("seminarBearbeiten", {
				seminarId: this._currentData.seminar_art_id
			});
			dojo.stopEvent(e);

		});

		dojo.connect(this.buttons.refTerminEditBtn, "onclick", this, function (e ) {
			sandbox.loadShellModule("terminReferentenEditor",{
				terminId: this._currentData.id, 
				standortId: this._currentData.standort_id
			});
			dojo.stopEvent(e);

		});

		dojo.connect(this.buttons.refSeminarEditBtn, "onclick", this, function (e ) {

			sandbox.loadShellModule("seminarReferentenEditor",{
				seminarId: this._currentData.seminar_art_id, 
				standortId: this._currentData.standort_id
			});
			dojo.stopEvent(e);

		});

		dojo.connect(this.buttons.kalenderBtn, "onclick", this, function(e ) {
			sandbox.loadShellModule("kalender", {
				terminId: this._currentData.id
			});
			dojo.stopEvent(e);
		});

		// Anwesenheitsliste
		dojo.connect(this.buttons.anwesenheitslisteBtn, "onclick", this, function ( e ) {
			var token = sandbox.getUserinfo().auth_token;

			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/admin/pdf/teilnehmerliste/"+this._currentData.id+";pdf?token=" + token );
			dojo.stopEvent(e);
		});

		// Tischbeschilderung als PDF
		dojo.connect(this.buttons.tischbeschilderungBtn, "onclick", this, function ( e ) {
			var token = sandbox.getUserinfo().auth_token;

			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/admin/pdf/tischbeschilderung/"+this._currentData.id+";pdf?token="+token);
			dojo.stopEvent(e);
		});

		// Teilnehmerliste als PDF
		dojo.connect(this.buttons.teilnehmerlisteBtn, "onclick", this, function ( e) {
			var token = sandbox.getUserinfo().auth_token;

			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/admin/csv/teilnehmerliste/"+this._currentData.id+";csv?token="+token);
			dojo.stopEvent(e);
		});

		// MaIL AN REFERENTEN
		dojo.connect(this.buttons.referentenMailBtn, "onclick", this, function ( e ) {
			var mailtolink = this._referentenMailto.toString();
			console.log(mailtolink);
			sandbox.navigateToUrl( mailtolink );
			dojo.stopEvent(e);
		});

		// MAIL AN TEILNEHMER
		dojo.connect(this.buttons.teilnehmerMailBtn, "onclick", this, function ( e ) {
			// getAllRows ist buggy, workaround
            var email = JSON.parse(this.flexTable.getCol("email")); // war mal> ["status","email","kontakt_email", "ansprechpartner_email"]
			var status = JSON.parse(this.flexTable.getCol("status"));
			var anspr = JSON.parse(this.flexTable.getCol("ansprechpartner_email"));

			var alleTn = [];
			console.dir(email);
			dojo.forEach(email, function(email, idx) {
				alleTn[idx] = {"email": email, "status": status[idx], "ansprechpartner_email": anspr[idx]};
			});
			var _mt = new mosaik.util.Mailto();
			_mt.setSubject (  this._currentData.kursnr );
			_mt.addTo("info@sag-akademie.de");
			dojo.forEach(alleTn, function (item, request) {
                if ( !(item.status == 1 || item.status==4)) return;

				if ( item.email != "" ) {
					console.info ( "MailTo: " + item.email);
					_mt.addBcc( item.email );
				}

                if ( item.ansprechpartner_email != "" && item.ansprechpartner_email != undefined ) {
                    console.info ( "Ansprechpartner-MailTo: " + item.ansprechpartner_email);
                    _mt.addBcc( item.ansprechpartner_email );
                }

                if ( item.kontakt_email != "" && item.kontakt_email != undefined ) {
                    console.info ( "MailTo: " + item.kontakt_email);
                    _mt.addBcc( item.kontakt_email );
                }
			});

			console.log(_mt.toString() );
			sandbox.navigateToUrl( _mt.toString() );
			dojo.stopEvent(e);

		});

		dojo.connect(this.buttons.speichernBtn, "onclick", this, "onSave");
		

	},

	numberChanged: function () {
		this.calculate();
		this.recalculateDauer();

		this.recalculateAuslastung();
		this.recalculateVerwKosten();
		this.recalculateGewinn();
	},

	calculate: function () { // TODO: formel rueberholen
		var tnMax = dojo.number.parse ( dijit.byId("Seminar:teilnehmer_max").get("value"));
		var tnMin = dojo.number.parse ( dijit.byId("Seminar:teilnehmer_min").get("value"));
		var tnStand =dojo.number.parse ( dojo.byId("Seminar:anzahlBestaetigt").innerHTML);

		this.calculateUmsatz("Seminar:kursgebuehr", "tnUmsatzMax", tnMax);
		this.calculateUmsatz("Seminar:kursgebuehr", "tnUmsatzMin", tnMin);
		this.calculateUmsatz("Seminar:kursgebuehr", "tnUmsatzStand", tnStand);
	},

	setupReferenten: function (data) {
		var _mt = new mosaik.util.Mailto();
		console.dir(data);

		_mt.setSubject (  this._currentData.kursnr );
		_mt.addTo("info@sag-akademie.de");
		//console.log(data);
		var _referenten = data;
		var string="";
		var row=null;
		var tbody = dojo.byId("referentenTBody");
		var nameCell = null;
		tbody.innerHTML = "";
		
		for ( var tag=1; tag<= data.length; tag++ ) {
			row = dojo.create("tr",{}, tbody);
			
			dojo.create("td", {
				innerHTML: tag.toString(),
				"class": "tagLabel"
			},row);
			
			nameCell = dojo.create("td", {}, row);
			
			for ( var i=0;i < _referenten[tag].length; i++ ) {
				if ( _referenten[tag][i].theorie == "1" ||  (_referenten[tag][i].theorie="0" &&  _referenten[tag][i].praxis=="0") ) {
					if  ( _referenten[tag][i].email != "" && !_mt.isBcc(_referenten[tag][i].email ) ) {
						_mt.addBcc ( _referenten[tag][i].email);
					}
					dojo.create("a", {
						innerHTML: _referenten[tag][i].name + ", "+_referenten[tag][i].vorname,
						href: "#",
						onclick: "sandbox.loadShellModule('referentBearbeiten', {referentId: '"+_referenten[tag][i].id+"'});"
					}, nameCell);
					dojo.create("br",{},nameCell);
				}
			}
			
			nameCell = dojo.create("td", {}, row);

			for ( var i=0;i < _referenten[tag].length; i++ ) {
				if ( _referenten[tag][i].praxis == "1" ) {
					if  ( _referenten[tag][i].email != "" && !_mt.isBcc(_referenten[tag][i].email ) ) {
						_mt.addBcc ( _referenten[tag][i].email);
					}
					dojo.create("a", {
						innerHTML: _referenten[tag][i].name + ", "+_referenten[tag][i].vorname ,
						href: "#",
						onclick: "sandbox.loadShellModule('referentBearbeiten', {referentId: '"+_referenten[tag][i].id+"'});"
					}, nameCell);
					dojo.create("br",{},nameCell);
				}
			}
		}

		this._referentenMailto = _mt;

		//$("#linkReferentenMail").click( function () {
		//	window.location = _mt.toString();
		//	console.log ( _mt.toString());
		//	return false;
		//});

		//$("#referenten").html(string);
		//$("#referentenTable").show();
		//dojo.byId("referentenTBody")dojo.byId("referentenTBody").innerHTML = string;

		if ( this.referentStandby ) {
			this.referentStandby.hide();
		}
	},

	// targetId => simple dom node
	// sourceId => dijit widget
	// quantumId => multiplyer
	calculateUmsatz: function (sourceId, targetId, quantum) {
		var _target = dojo.byId(targetId);
		var _source = dijit.byId(sourceId);

		var _sKosten = _source.get("value");
		var _fKosten = dojo.number.parse(_sKosten);

		_target.innerHTML = dojo.number.format (_fKosten * quantum);
	},

	recalculateAuslastung: function () {
		var _max = new Number( dijit.byId ("Seminar:teilnehmer_max").get("value"));
		var _cur = new Number ( dojo.byId( "Seminar:anzahlBestaetigt").innerHTML );

		var _pro = parseInt( _cur *100 / _max );

		dojo.byId("tnAuslastung").innerHTML =  ('(' + _pro.toString()  + "%)&nbsp;");
	},

	recalculateVerwKosten: function() {
		var proz = new Number(dijit.byId("Seminar:kosten_allg").get("value")) / 100;
		var umsatz =	dojo.number.parse(dojo.byId("tnUmsatzStand").innerHTML);
		dojo.byId("seminarVerwaltungskosten").innerHTML =  dojo.number.format( proz*umsatz , this.currencyFormat);
	},

	recalculateGewinn: function() {
		console.log("recalculateGewinn...");
		var gewinnNode = dojo.byId("gewinn");
		gewinnNode.innerHTML = "-,--";
		var _dauer = dojo.number.parse( dojo.byId("Seminar:dauer").innerHTML);

		var _umsatz =	dojo.number.parse( dojo.byId("tnUmsatzStand").innerHTML );
		var _refKosten = dijit.byId("Seminar:kosten_refer").get("value");
		var _unterlagenKosten = dijit.byId("Seminar:kosten_unterlagen").get("value");
		var _verpflegungKosten = dijit.byId("Seminar:kosten_verpflegung").get("value");
		var _pruefungKosten = dijit.byId("Seminar:kosten_pruefung").get("value");

		console.log("Verwaltungskosten: " + dojo.byId("seminarVerwaltungskosten").innerHTML);
		var _verwKosten = dojo.number.parse( dojo.byId("seminarVerwaltungskosten").innerHTML.replace("&nbsp;","") );

		var _cur = dojo.number.parse( dojo.byId("Seminar:anzahlBestaetigt").innerHTML );

		var _pruefungsgebuehren = (_umsatz - _unterlagenKosten * _cur -  _verpflegungKosten * _cur * _dauer) * (_pruefungKosten / 100);

		dojo.byId("seminarUnterlagen").innerHTML = dojo.number.format ( _unterlagenKosten * _cur, this.currencyFormat );
		dojo.byId("seminaVerpflegung").innerHTML =dojo.number.format( _verpflegungKosten * _cur * _dauer, this.currencyFormat );
		dojo.byId("seminarPruefung").innerHTML =dojo.number.format( _pruefungsgebuehren, this.currencyFormat );

		dojo.byId("seminarReferentenkosten").innerHTML =  dojo.number.format( _refKosten * _dauer, this.currencyFormat );

		var gewinn = _umsatz;
		
		gewinn -= _refKosten * _dauer;
		gewinn -= _unterlagenKosten * _cur;
		gewinn -= _cur * _verpflegungKosten * _dauer;
		gewinn -=  _pruefungsgebuehren;
		gewinn -= _verwKosten;

		if ( gewinn < 0 ) {
			gewinnNode.style.color= "red";
		} else {
			gewinnNode.style.color= "black";
		}
		console.log("Gewinn: " + gewinn);
		gewinnNode.innerHTML = dojo.number.format ( gewinn , this.currencyFormat );
	},

	recalculateDauer: function () {
		var dauer = dojo.number.parse(dojo.byId("Seminar:dauer").innerHTML);
		var pause = dijit.byId("Seminar:pause_pro_tag").get("value");
		
		var pauseGesamt = pause * dauer;

		var stunden_pro_tag = dijit.byId("Seminar:stunden_pro_tag").get("value");
		var ue_pro_stunde = 4/3;

		var stundenGesamt = stunden_pro_tag * dauer - pauseGesamt;
		var ueGesamt = Math.round (stundenGesamt * ue_pro_stunde * 10)/10;

		dojo.byId("gesamtUe").innerHTML = dojo.number.format (ueGesamt) ;
		dojo.byId("pauseGesamt").innerHTML = dojo.number.format(pauseGesamt) ;
		dojo.byId("stundenGesamt").innerHTML = dojo.number.format( stundenGesamt + pauseGesamt) ;
		dojo.byId("gesamtStunden").innerHTML =  dojo.number.format (stundenGesamt);
	},
	/*** FLEXTABLE HANDLING **/
	_ftInitDone: false,
	initFlexTable: function () {
		// Summary:
		// connects buttons and flex table events

		if ( this._ftInitDone ) { return };
        this.flexTable.show();

        this.flexTable.setTitle("Buchungen:");
		this.flexTable.clearContextMenu();
		
        this.subscribeTo("flextable/editBuchung", "editBuchung");
        this.subscribeTo("flextable/editPerson", "editPerson");
        this.subscribeTo("flextable/editKontakt", "editKontakt");
        this.subscribeTo("flextable/doubleClick", "editBuchung");

        this.flexTable.addContextMenuItem("Buchung bearbeiten", "flextable/editBuchung");
        this.flexTable.addContextMenuItem("Firma aufrufen", "flextable/editKontakt");
        this.flexTable.addContextMenuItem("Person aufrufen", "flextable/editPerson");

        this._ftInitDone = true;
	},

	editBuchung: function ( data ) {
		// Summary:
		// loads buchungBearbeiten module passes in the buchungId
		console.log("Edit Buchung: " + data.id);
		sandbox.loadShellModule("buchungBearbeiten", {
			buchungId: data.id
		});
	},

	editPerson: function ( data ) {
		// Summary:
		// loads personBearbeiten module passes in the personId
		console.log("Edit Person: " + data.person_id);
		sandbox.loadShellModule("personBearbeiten", {
			personId: data.person_id
		});
	},

	editKontakt: function ( data ) {
		// Summary:
		// loads personBearbeiten module passes in the personId
		console.log("Edit Kontakt: " + data.kontakt_id);
		sandbox.loadShellModule("kontaktBearbeiten", {
			kontaktId: data.kontakt_id
		});
	},
	
	updateFlexTable: function () {
		// Summary:
		// sets flex table options
		// and relayouts if necessary
        this.flexTable.show();
        this.flexTableServiceURL = this.sandbox.getServiceUrl ( "termin/buchungen" ) + this._options.terminId;
        this.flexTable.queryService(this.flexTableServiceURL , {});
    },


	
	doPrint: function() {
		var token = sandbox.getUserinfo().auth_token;
		
		var pdfurl = this.sandbox.getServiceUrl ( "print/termin" ) + this._currentData.id + "?token=" + token;
		
		app.openPdf(pdfurl);
	},
	
	onDelete: function() {
		var reallyDelete = confirm("Wollen Sie den Termin wirklich löschen?\n\nBitte beachten Sie, dass die Buchungen für diesen Termin ebenfalls gelöscht werden und sich so die Statistik verändert.");
		if ( reallyDelete == true) {
			
			this.service.remove( this._currentData.id ).addCallback(function() {
				alert("Termin erfolgreich gelöscht.")
				sandbox.loadShellModule("termineSeminare");				
			});
		}
	}
});
