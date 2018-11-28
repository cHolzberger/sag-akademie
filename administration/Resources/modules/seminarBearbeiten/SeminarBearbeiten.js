/** require dependencies **/
dojo.require("dojo.number");

dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");
dojo.require('mosaik.ui.DatasetNavigator');
dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.CurrencyTextBox');
dojo.require("dijit.form.CheckBox");
dojo.require("dojox.widget.Standby");

dojo.require('dijit.Editor');
dojo.require("dijit._editor.plugins.AlwaysShowToolbar");
 dojo.require("dojox.widget.ColorPicker");
dojo.require('mosaik.ui.FlexTable');
dojo.require('mosaik.ui.FileUpload');

dojo.require("mosaik.ui.ColorInput");
dojo.require("mosaik.util.Mailto")
dojo.require("module.seminarBearbeiten.TopDetail");
dojo.require("module.seminarBearbeiten.Standorte");


dojo.provide("module.seminarBearbeiten.SeminarBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.seminarBearbeiten.SeminarBearbeiten", [mosaik.core.Module], {
	moduleName: "SeminarBearbeiten",
	moduleVersion: "1",
	dropdowns: null,
	service: null,
	referentService: null,
	kooperationspartnerStandby: null,
	referentStandby: null,
	_options: null,
	formPrefix: "SeminarArt",
	_currentData: null,
	_initDropdownsDone: false,
	container: null, // container node -> border container
	_kooperationspartner: null,
	
	buttons: {
		seminarEditBtn: null,
		startseiteBtn: null,
		infoBtn: null,
		speichernBtn: null,
		topGenBtn: null,
		topBtn: null
	},

	constructor: function () {
		this.dropdowns = {};
	},

	run: function( options ) {
		this._kooperationspartner = [];
		
		console.debug("SeminarBearbeiten::run >>>");
		this._options = options;
		this.service =  this.sandbox.getRpcService("database/seminar");

		this.container = dijit.byId('borderContainer');
		this.chooserFrame = dijit.byId("chooserFrame");
		this.chooserStack = dijit.byId("chooserStack");

		this.stundenGesamtNode = dojo.byId("stundenGesamt");
		this.pauseGesamtNode = dojo.byId("pauseGesamt");
		this.stundenOhnePauseNode = dojo.byId ("stundenOhnePause");
		this.ueGesamtNode = dojo.byId("ueGesamt");

		this.seminarArtContainer = dojo.byId("seminarArtContainer");
		
		this.initForm();

		// fetch or create
		if ( typeof ( options.create ) !== "undefined") {
			this.onCreate();
		} else {
			this.fetchData();
		}

		// relayout the container
		this.container.layout();

		dojo.connect(this, "onValuesSet", this, "calculate");
		dojo.connect(this, "valueUpdate", this, "calculate");
		
		dojo.connect( this.widgets.top1, "onDataChange", this, "topChanged");
		dojo.connect( this.widgets.top2, "onDataChange", this, "topChanged");
		dojo.connect( this.widgets.top3, "onDataChange", this, "topChanged");

		console.debug ("<<< SeminarBearbeiten::run");
		console.dir(options);
		this.widgets.dnav.update( options, "seminarId", "seminarBearbeiten");
        this.flexTable.hide();
	},

	topChanged: function () {
		// Summary: 
		// called on changed top 
		// -- monitoring multi valued widgets not implemented
		
		console.log("SeminarBearbeiten::topChanged");
		this.updateValue("info_titel", this.widgets.top1.titleValue);
		this.updateValue("info_link", this.widgets.top1.linkValue);
		this.updateValue("info_titel2", this.widgets.top2.titleValue);
		this.updateValue("info_link2", this.widgets.top2.linkValue);
		this.updateValue("info_titel3", this.widgets.top3.titleValue);
		this.updateValue("info_link3", this.widgets.top3.linkValue);
	},
	
	initForm: function () {
		dojo.connect( this, "valueUpdate", this, "calculate");
		
		this.referentStandby = new dojox.widget.Standby ({
			target: "referentenContainer"
		});

		this.linkButtons();

		this.dropdowns = {};
		console.log("Setting Options");
		// dom elemente holen
		dijit.byId("SeminarArt:status").addOption( sandbox.getSelectArray("SeminarArtStatus") );

		var rubriken = sandbox.getSelectArray("SeminarArtRubrik");
		dijit.byId("SeminarArt:rubrik").addOption( dojo.clone( rubriken ));
		dijit.byId("SeminarArt:rubrik2").addOption( dojo.clone(rubriken ));
		dijit.byId("SeminarArt:rubrik3").addOption( dojo.clone(rubriken));
		dijit.byId("SeminarArt:rubrik4").addOption( dojo.clone(rubriken));
		dijit.byId("SeminarArt:rubrik5").addOption( dojo.clone(rubriken));


		var qualifikationsart = [
			{value:"", label:""},
			{value:"Sachkunde", label:"Sachkunde"},
			{value:"Fachkunde", label:"Fachkunde"},
			{value:"Unterweisung",label:"Unterweisung"},
			{value:"Fortbildung",label:"Fortbildung"},
			{value:"Weiterbildung",label:"Weiterbildung"}
		]
		dijit.byId("SeminarArt:qualifikationsart").addOption(qualifikationsart);

		
		var rezertifizierungszeit = [
			{value:"", label:"ohne"},
			{value:"1", label:"1 Jahr"},
			{value:"2", label:"2 Jahre"},
			{value:"3", label:"3 Jahre"},
			{value:"4", label:"4 Jahre"},
			{value:"5", label:"5 Jahre"}
		]
		dijit.byId("SeminarArt:rezertifizierungszeit").addOption(rezertifizierungszeit);
	},

	onCreate: function () {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

		this.chooserFrame.show();

		//get buttons
		this.nextButton = dijit.byId("nextButton");
		this.prevButton = dijit.byId("prevButton");
		this.createButton = dijit.byId("createButton");

		this._createButtonHandle = dojo.connect( this.createButton, "onClick", this, "createDone");
		var rubriken = sandbox.getSelectArray("SeminarArtRubrik");
		rubriken[0].selected=true;
		dijit.byId("newSeminarRubrik").removeOption( dijit.byId("newSeminarRubrik").getOptions());
		dijit.byId("newSeminarRubrik").addOption( rubriken );

		this.createSeminarDetail();
	},

	createSeminarArt: function () {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		//(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;
		
		this.chooserFrame.set("title","Seminar ausw&auml;hlen");
		this.chooserStack.selectChild( dijit.byId("seminarArtPane"));
		
		// Summary:
		// syncs the "rubrik" table with the view
		var data =  sandbox.getObject("SeminarArtRubrik");
		var seminareStore = sandbox.getItemStore("SeminarArt");
		
		// hide prev and create button
		this.createButton.domNode.style.display="none";
		this.prevButton.domNode.style.display="none";
		
		if ( this._newSeminarArt == null) {
			this.nextButton.domNode.style.display="none";
		} else {
			this.nextButton.domNode.style.display="block";
		}
		
		this.seminarArtContainer.innerHTML="";
		
		for ( var i=0; i < data.length; i++) {
			var container = dojo.create("div", {
				innerHTML: data[i].name,
				"class": "gradient1 normal-padding",
				style: " font-weight: bold; "
			}, this.seminarArtContainer);
			
			var seminare = seminareStore.query ( {
				"fk1": data[i].id
			} );
			
			
			dojo.forEach ( seminare, dojo.hitch(this, function( item ) {
				var element = dojo.create("div", {
					style: "padding-left: 5px;",
					innerHTML: item.name ,
					"class": "seminarBtn"
				}, this.seminarArtContainer);
				
				dojo.connect(element, "onclick", this, function(e) {
					this.nextButton.domNode.style.display="block";
					if ( this._currentActiveSeminarButton != null) {
						dojo.removeClass(this._currentActiveSeminarButton, "seminarBtnActive");
					}
					dojo.addClass(e.currentTarget, "seminarBtnActive");
					
					this._copyFromSeminar = item;
					this._currentActiveSeminarButton = e.currentTarget;
				});
			}));
			
			
		}
		
		var dim=dojo.contentBox(this.seminarArtContainer);
		dojo.anim(this.widgets.seminarArtPane.domNode, { height: dim.h } );
		
		
		this._nextButtonHandle= dojo.connect( this.nextButton, "onClick", this, "createSeminarDetail");
	},

	createSeminarDetail: function () {
		this.chooserFrame.set("title","Seminar anlegen");
		this.chooserStack.selectChild( dijit.byId("seminarDetailPane"));

		this.nextButton.domNode.style.display="none";
		this.prevButton.domNode.style.display="none";

		if ( this._copyFromSeminar != null ) {
			dojo.style(this.widgets.btnSeminarArt.domNode, "display", "none");
			dojo.byId("seminarArtName").innerHTML = this._copyFromSeminar.name;
			dojo.style("seminarArtName", "display", "block");
		} else {
			dojo.style(this.widgets.btnSeminarArt.domNode, "display", "block");
			dojo.style("seminarArtName", "display", "none");
		}
		this.createButton.domNode.style.display="block";
	},

	createDone: function () {
		sandbox.showLoadingScreen("Daten speichern...");
		var seminarId = dijit.byId("newSeminarId").get("value");
		var inPlanung = dijit.byId("newSeminarInPlanung").get("value") ? 1: 0;
		var rubrikId = dijit.byId("newSeminarRubrik").get("value");
		var cloneFrom = dojo.trim(dojo.byId("seminarArtName").innerHTML);
		if ( cloneFrom == "" ) {
			this.service.create( seminarId, rubrikId, inPlanung ).addCallback ( dojo.hitch ( this, "updateData")).addErrback (dojo.hitch ( this, function (data) {
				sandbox.hideLoadingScreen();
				console.log ("==!!> SeminarBearbeiten::createDone Error: "+data);
				alert("Fehler beim Erstellen.\n\nFehler: " + data.message);
			}));
		} else {
			this.service.cloneFromSeminar(cloneFrom , rubrikId, inPlanung, seminarId ).addCallback ( dojo.hitch ( this, "updateData")).addErrback (dojo.hitch ( this, function (data) {
				sandbox.hideLoadingScreen();
				console.log ("==!!> InhouseSeminarBearbeiten::createDone Error: "+data);
				alert("Fehler beim Erstellen.\n\nFehler: " + data.message);
			}));
		}
	},

	onSave: function () {
		sandbox.showLoadingScreen("Daten speichern...");
		console.log("Save");
		this.service.save( this._currentData.id, this._changedData ).addCallback( dojo.hitch ( this, "updateData")).addErrback(function (data) {
			console.log ("Seminar-Save Error: " + data);
			sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern.\n\nFehler: " + data.message);
		});

	},

	updateData: function ( data ) {
		// Summary:
		// update handler for the various loading functions
		this.initStaticFields();
	
		console.dir(data);
		if (data.angelegt_datum ){ 
			data.angelegt_datum = mysqlDateToLocal(data.angelegt_datum);
		}

		if ( data.deaktiviert_datum ) {
			data.deaktiviert_datum = mysqlDateToLocal(data.deaktiviert_datum);
		}
		this.setValue ( data );
		dijit.byId("standorte").set("seminarArtId", data.id);
		sandbox.hideLoadingScreen();
		this.chooserFrame.hide();
		
		this.widgets.top1.set("data", data);
		this.widgets.top2.set("data", data);
		this.widgets.top3.set("data", data);

        sandbox.setModuleChanged(false);
	},

	linkButtons: function() {
		var key;
		for ( key in this.buttons ) {
			this.buttons[key] = dojo.byId(key);
		}

		dojo.connect(this.buttons.topBtn, "onclick", this, function (e) {
			// http://sag-akademie.de/pdf/top2;pdf/
			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/pdf/top2;pdf/" + this._currentData.id);
			dojo.stopEvent(e);
		});
		
		dojo.connect(this.buttons.startseiteBtn, "onclick", this, function(e) {
			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") );
			dojo.stopEvent(e);

		});

		dojo.connect(this.buttons.infoBtn, "onclick", this, function(e) {
			sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/seminar/info/" + this._currentData.id );
			dojo.stopEvent(e);

		});

		dojo.connect(this.buttons.seminarEditBtn, "onclick", this, function (e ) {
			sandbox.loadShellModule("termineSeminare",{seminarId: this._currentData.id});
			dojo.stopEvent(e);

		});




        dojo.connect(this.buttons.speichernBtn, "onclick", this, "onSave");
	},

	fetchData: function () {
		this.service.find(this._options.seminarId).addCallback ( dojo.hitch ( this, "updateData" ) ).addErrback (dojo.hitch ( this, function (data) {
			sandbox.hideLoadingScreen();
			alert("Fehler beim Laden.\n\nFehler: " + data.message);
		}));
	},

	numberChanged: function () {
		this.calculate();
	},

	calculate: function () { // TODO: formel rueberholen
		var dauer = parseInt( dijit.byId("SeminarArt:dauer").get("value") );
		var stGes =  dauer * dijit.byId("SeminarArt:stunden_pro_tag").get("value"); // gesamt stundne inkl pausen
		var pauseGes = dauer * dijit.byId("SeminarArt:pause_pro_tag").get("value");

		this.stundenGesamtNode.innerHTML = dojo.number.format(stGes) + "&nbsp;Std";
		this.pauseGesamtNode.innerHTML =  dojo.number.format(pauseGes) + "&nbsp;Std";

		this.stundenOhnePauseNode.innerHTML = dojo.number.format(stGes - pauseGes) + "&nbsp;Std";
		this.ueGesamtNode.innerHTML = dojo.number.format(( stGes - pauseGes ) * 4/3) + "&nbsp;UE";

		var tnMax = dojo.number.parse ( dijit.byId("SeminarArt:teilnehmer_max_tpl").get("value"));
		var tnMin = dojo.number.parse ( dijit.byId("SeminarArt:teilnehmer_min_tpl").get("value"));
		var tnGew = dojo.number.parse( dijit.byId("SeminarArt:gewinn_tn").get("value") );
		
		
		this.calculateUmsatz("SeminarArt:kursgebuehr", "tnUmsatzMax", tnMax);
		this.calculateUmsatz("SeminarArt:kursgebuehr", "tnUmsatzMin", tnMin);
		var umsatz = this.calculateUmsatz("SeminarArt:kursgebuehr", "tnUmsatzStand", tnGew);

		// verwaltungskosten
		var proz = dijit.byId("SeminarArt:kosten_allg").get("value") / 100;
		
		
		dojo.byId("seminarVerwaltungskosten").innerHTML =  dojo.number.format( proz*umsatz , this.currencyFormat);
		
		this.recalculateGewinn(umsatz,  proz*umsatz, tnGew);
	},

	// targetId => simple dom node
	// sourceId => dijit widget
	// quantumId => multiplyer
	calculateUmsatz: function (sourceId, targetId, quantum) {
		var _target = dojo.byId(targetId);
		var _source = dijit.byId(sourceId);

		var _sKosten = _source.get("value");
		var _fKosten = dojo.number.parse(_sKosten);

		_target.innerHTML = dojo.number.format (_fKosten * quantum) + "&nbsp;&euro;";
		return _fKosten * quantum;
	},

	recalculateGewinn: function(_umsatz, _verwKosten , _cur) {
		console.log("recalculateGewinn...");
		var gewinnNode = dojo.byId("gewinn");
		gewinnNode.innerHTML = "-,--";
		
		var _dauer = dojo.number.parse( dijit.byId("SeminarArt:dauer").get("value"));
		var _refKosten = dijit.byId("SeminarArt:kosten_refer").get("value");
		var _unterlagenKosten = dijit.byId("SeminarArt:kosten_unterlagen").get("value");
		var _verpflegungKosten = dijit.byId("SeminarArt:kosten_verpflegung").get("value");
		var _pruefungKosten = dijit.byId("SeminarArt:kosten_pruefung").get("value");
		console.log("Kosten unterlagen: " + _unterlagenKosten);

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
		gewinnNode.innerHTML = dojo.number.format ( gewinn , this.currencyFormat ) + "&nbsp;&euro;";
	},
	
	initStaticFields: function () {
		
	},

	doPrint: function() {
		var token = sandbox.getUserinfo().auth_token;
		
		var pdfurl = this.sandbox.getServiceUrl ( "print/seminar" ) + this._currentData.id + "?token=" + token;
		
		app.openPdf(pdfurl);
	},
	/** Kooperationsparnter **/
	editKooperationspartner: function() {
		var dialog = dijit.byId("kooperationspartnerDialog");
		
		this.updateKooperationspartner();

		// FIXME: add current cooperationspartner
		dialog.show();
	},

	updateKooperationspartner: function () {
		if ( !this.kooperationspartnerStandby) {
			this.kooperationspartnerStandby = new dojox.widget.Standby ({
				target: "kooperationspartnerListe"
			});
		}
		this.kooperationspartnerStandby.show();
		this.service.getKooperationspartner(this._currentData.id).addCallback(this,this.onKooperationspartnerUpdate);
	},

	onKooperationspartnerUpdate: function (kooperationspartner) {

		console.dir (kooperationspartner);
		this._kooperationspartner = [];
		
		dojo.forEach(kooperationspartner, function (item) {
			this._kooperationspartner.push(item.kooperationspartner_id);
		}, this);
		
		var kooperationspartnerStore = sandbox.getSelectArray("Kooperationspartner");
		var targetListe= dojo.byId("kooperationspartnerListe");
		var targetAuswahl= dojo.byId("kooperationspartnerAuswahl");
		
		targetListe.innerHTML="";
		targetAuswahl.innerHTML="";

		
		dojo.forEach ( kooperationspartnerStore,function ( item ) {
			var self = this;
			
			if ( dojo.indexOf( this._kooperationspartner, item.value) >= 0) {
				var div = dojo.create("div", {
					style: "width: 100px;",
					"class": "seminarBtn",
					onclick: function() {
						self.delKooperationspartner( item.value, item.label);
					}
				}, targetAuswahl);
				
				var link = dojo.create("a", {
					innerHTML: item.label,
					href: "#"
					
				}, div);
			} else {
				var div = dojo.create("div", {
					style: "width: 100px;",
					"class": "seminarBtn",
					onclick: function() {
						self.addKooperationspartner( item.value, item.label);
					}
				}, targetListe);
				
				var link = dojo.create("a", {
					innerHTML: item.label,
					href: "#"
				}, div);
			}
		},this);

		this.kooperationspartnerStandby.hide();
	},

	addKooperationspartner: function( id, name) {
		console.log("add" + id + " " + name);
		this.service.addKooperationspartner(this._currentData.id, id).addCallback(this,this.onKooperationspartnerUpdate);


	},

	delKooperationspartner: function( id, name) {
		console.log("del" + id + " " + name);
		this.service.delKooperationspartner(this._currentData.id, id).addCallback(this,this.onKooperationspartnerUpdate);

	},

	onDelete: function() {
		var reallyDelete = confirm("Wollen Sie das Seminar wirklich löschen?\n\nBitte beachten Sie, dass die Termine und Buchungen für dieses Seminar ebenfalls gelöscht werden und sich so die Statistik verändert.");
		if ( reallyDelete == true) {
			
			this.service.remove( this._currentData.id ).addCallback(function() {
				alert("Seminar erfolgreich gelöscht.")
				sandbox.loadShellModule("termineSeminare");				
			});
		}
	},

    genTopPdf: function () {
        var token = sandbox.getUserinfo().auth_token;

        sandbox.navigateToUrl( this.sandbox.getAppVar("serverurl") + "/admin/pdf/top/" + this._currentData.seminar_art_id +";pdf?token="+token);
        dojo.stopEvent(e);
    }
});
