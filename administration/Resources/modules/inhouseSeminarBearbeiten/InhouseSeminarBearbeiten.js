/** require dependencies **/
dojo.require("dojo.number");

dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.CurrencyTextBox');
dojo.require("dojox.widget.Standby");

dojo.require('dijit.Editor');
dojo.require("dijit._editor.plugins.AlwaysShowToolbar");

dojo.require('mosaik.ui.FlexTable');
dojo.require('mosaik.ui.DatasetNavigator');
dojo.require("mosaik.ui.ColorInput");
dojo.require("mosaik.util.Mailto");

dojo.provide("module.inhouseSeminarBearbeiten.InhouseSeminarBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.inhouseSeminarBearbeiten.InhouseSeminarBearbeiten", [mosaik.core.Module], {
	moduleName: "InhouseSeminarBearbeiten",
	moduleVersion: "1",
	dropdowns: null,
	service: null,
	referentService: null,
	referentStandby: null,
	_options: null,
	formPrefix: "SeminarArt",
	_currentData: null,
	_initDropdownsDone: false,
	container: null, // container node -> border container
	_currentIsNew: false,
	_copyFromSeminar: null,
	
	buttons: {
		seminarEditBtn: null,
		startseiteBtn: null,
		infoBtn: null,
		speichernBtn: null
	},

	constructor: function () {
		this.dropdowns = {};
	},

	run: function( options ) {
		console.debug("InhouseSeminarBearbeiten::run >>>");
		this._options = options;
		this.service =  this.sandbox.getRpcService("database/inhouseSeminar");

		this.container = dijit.byId('borderContainer');
		this.chooserFrame = dijit.byId("chooserFrame");
		this.chooserStack = dijit.byId("chooserStack");

		this.stundenGesamtNode = dojo.byId("stundenGesamt");
		this.pauseGesamtNode = dojo.byId("pauseGesamt");
		this.stundenOhnePauseNode = dojo.byId ("stundenOhnePause");
		this.ueGesamtNode = dojo.byId("ueGesamt");

	
		this.initForm();

		// fetch or create
		if ( typeof ( options.create ) !== "undefined") {
			console.log("CREATE");
			this.onCreate();
		} else {
			console.log("FETCH");
			this.fetchData();
		}

		// relayout the container
		this.container.layout();
        this.flexTable.hide();
		
		console.debug ("<<< InhouseSeminarBearbeiten::run");
	},

	initForm: function () {
		dojo.connect(this, "onValuesSet", this, "calculate");
		dojo.connect(this, "valueUpdate", this, "calculate");
		this.seminarArtContainer = dojo.byId("seminarArtContainer");

		
		this.referentStandby = new dojox.widget.Standby ({
			target: "referentenContainer"
		});

		this.linkButtons();

		console.log("Setting Options");
		// dom elemente holen
		dijit.byId("SeminarArt:status").addOption( sandbox.getSelectArray("SeminarArtStatus"));
	},

	onCreate: function () {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

		this.chooserFrame.show();

		// get buttons
		this.nextButton = dijit.byId("nextButton");
		this.prevButton = dijit.byId("prevButton");
		this.createButton = dijit.byId("createButton");

		this._createButtonHandle = dojo.connect( this.createButton, "onClick", this, "createDone");

		this.createSeminarDetail();
	},

	createSeminarDetail: function () {
		this.chooserFrame.set("title","Inhouse-Seminar anlegen");
		this.chooserStack.selectChild( dijit.byId("seminarDetailPane"));

		//this.nextButton.domNode.style.display="none";
		//this.prevButton.domNode.style.display="none";
		this.nextButton.domNode.style.display="none";

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
				style: "font-weight: bold; "
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

	createDone: function () {
		sandbox.showLoadingScreen("Daten speichern...");
		
		var seminarId = dijit.byId("newSeminarId").get("value");
		var inPlanung = dijit.byId("newSeminarInPlanung").get("value") ? 1: 0;
		var cloneFrom = dojo.trim(dojo.byId("seminarArtName").innerHTML);
		this._currentIsNew = true;

		if ( cloneFrom == "" ) {
			console.log("CREATING SEMINAR " + seminarId);
			this.service.create( seminarId, inPlanung ).addCallback ( dojo.hitch ( this, "updateData")).addErrback (dojo.hitch ( this, function (data) {
				sandbox.hideLoadingScreen();
				console.log ("==!!> InhouseSeminarBearbeiten::createDone Error: "+data);
				alert("Fehler beim Erstellen.\n\nFehler: " + data.message);
			}));
		} else {
			console.log("CLONING SEMINAR " + cloneFrom);
			this.service.cloneFromSeminar(cloneFrom , inPlanung, seminarId ).addCallback ( dojo.hitch ( this, "updateData")).addErrback (dojo.hitch ( this, function (data) {
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
		
		if ( this._currentIsNew) {
			// request full sync if we created a new inhouse-seminar
			sandbox.publish ("sync/start");
			this._currentIsNew = false;
		}
		
		console.dir(data);
		this.setValue ( data );

		console.log ( data.farbe );
		dojo.byId('SeminarArtFarbe').style.backgroundColor = data.farbe.toString().replace("0x","#") + " !important";
		dojo.byId('SeminarArtTextfarbe').style.backgroundColor = data.textfarbe.toString().replace("0x","#") + " !important";

		sandbox.hideLoadingScreen();
		this.chooserFrame.hide();
	},

	linkButtons: function() {
		console.log("LinkButtons");
		var key;
		for ( key in this.buttons ) {
			this.buttons[key] = dojo.byId(key);
		};

		dojo.connect(this.buttons.seminarEditBtn, "onclick", this, function (e ) {
			sandbox.loadShellModule("inhouse",{
				seminarId: this._currentData.id
				});
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
	
	onDelete: function() {
		var reallyDelete = confirm("Wollen Sie das Seminar wirklich löschen?\n\nBitte beachten Sie, dass die Termine und Buchungen für dieses Seminar ebenfalls gelöscht werden und sich so die Statistik verändert.");
		if ( reallyDelete == true) {
			
			this.service.remove( this._currentData.id ).addCallback(function() {
				alert("Seminar erfolgreich gelöscht.")
				sandbox.loadShellModule("inhouse");				
			});
		}
	}
});
